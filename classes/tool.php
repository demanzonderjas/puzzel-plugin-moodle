<?php
// This file is part of the Puzzel.org plugin for Moodle - https://puzzel.org
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <https://www.gnu.org/licenses/>.

namespace local_puzzel;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/lti/locallib.php');

/**
 * Finds and finishes the Puzzel.org LTI Advantage tool registration.
 *
 * Moodle's dynamic registration always leaves the new tool pending and out of
 * the activity chooser: openid-registration.php sets
 * state = LTI_TOOL_STATE_PENDING, and registration_to_config() hardcodes
 * coursevisible = LTI_COURSEVISIBLE_PRECONFIGURED. Neither value can be
 * influenced by the tool, so this is the one thing the plugin exists to do.
 *
 * @package    local_puzzel
 * @copyright  2026 Puzzel.org
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool {
    /** @var string Where Puzzel.org answers a dynamic registration. */
    public const DEFAULT_REGISTRATION_URL = 'https://puzzel.org/lti/register';

    /**
     * The configured registration URL.
     *
     * @return string
     */
    public static function registration_url(): string {
        $url = trim((string) get_config('local_puzzel', 'registrationurl'));
        return $url !== '' ? $url : self::DEFAULT_REGISTRATION_URL;
    }

    /**
     * The domain the registered tool is expected to carry.
     *
     * @return string
     */
    public static function tool_domain(): string {
        return lti_get_domain_from_url(self::registration_url());
    }

    /**
     * Every LTI 1.3 tool type pointing at the Puzzel.org domain.
     *
     * @return array of stdClass, newest first
     */
    public static function types(): array {
        global $DB;

        $domain = self::tool_domain();
        if ($domain === '') {
            return [];
        }

        return $DB->get_records('lti_types', [
            'tooldomain' => $domain,
            'ltiversion' => LTI_VERSION_1P3,
        ], 'timecreated DESC');
    }

    /**
     * Whether a usable Puzzel.org tool is already in place.
     *
     * @return bool
     */
    public static function is_ready(): bool {
        foreach (self::types() as $type) {
            if (
                (int) $type->state === LTI_TOOL_STATE_CONFIGURED
                    && (int) $type->coursevisible === LTI_COURSEVISIBLE_ACTIVITYCHOOSER
            ) {
                return true;
            }
        }
        return false;
    }

    /**
     * Activate every Puzzel.org tool type and show it in the activity chooser.
     *
     * The config is round-tripped through lti_get_type_type_config() and only
     * coursevisible is overridden: lti_update_type() rewrites the type's
     * config rows and its course categories from what it is handed, so a
     * sparse config would quietly drop settings the registration made.
     *
     * @return int the number of types changed
     */
    public static function activate(): int {
        $changed = 0;

        foreach (self::types() as $record) {
            $isactive = (int) $record->state === LTI_TOOL_STATE_CONFIGURED;
            $inchooser = (int) $record->coursevisible === LTI_COURSEVISIBLE_ACTIVITYCHOOSER;
            if ($isactive && $inchooser) {
                continue;
            }

            $config = lti_get_type_type_config($record->id);
            $config->lti_coursevisible = LTI_COURSEVISIBLE_ACTIVITYCHOOSER;

            $type = new \stdClass();
            $type->id = $record->id;
            $type->state = LTI_TOOL_STATE_CONFIGURED;

            lti_update_type($type, $config);
            $changed++;
        }

        return $changed;
    }
}
