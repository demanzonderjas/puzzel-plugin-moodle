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

namespace local_puzzel\privacy;

/**
 * Privacy provider for local_puzzel.
 *
 * The plugin stores no personal data of its own. It reads and writes rows in
 * mod_lti's own tool tables, which mod_lti is responsible for, and it keeps a
 * single site-level setting holding the Puzzel.org registration URL.
 *
 * @package    local_puzzel
 * @copyright  2026 Puzzel.org
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider implements \core_privacy\local\metadata\null_provider {
    /**
     * Why this plugin stores no personal data.
     *
     * @return string the identifier of a string explaining the reason
     */
    public static function get_reason(): string {
        return 'privacy:metadata';
    }
}
