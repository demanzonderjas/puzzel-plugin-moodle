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
 * Tests for the tool helper.
 *
 * @package    local_puzzel
 * @copyright  2026 Puzzel.org
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \local_puzzel\tool
 *
 * The coverage stays a doc-comment rather than a #[CoversClass] attribute.
 * PHPUnit 11 (Moodle 5.x) reports a deprecation for doc-comment metadata, but
 * moodle-cs does not yet recognise the attribute and then fails its coverage
 * sniff, which CI runs at --max-warnings 0. The deprecation does not fail a
 * run; the sniff does. Swap it when moodle-cs learns the attribute.
 */
final class tool_test extends \advanced_testcase {
    /**
     * Register a tool type exactly as mod_lti's dynamic registration leaves one.
     *
     * openid-registration.php sets the state to pending, and
     * registration_to_config() hardcodes coursevisible to preconfigured.
     *
     * @param string $domain the tool domain to register under
     * @return int the new type id
     */
    private function register_as_moodle_does(string $domain): int {
        $type = new \stdClass();
        $type->state = LTI_TOOL_STATE_PENDING;
        $type->ltiversion = LTI_VERSION_1P3;

        $config = new \stdClass();
        $config->lti_typename = 'Puzzel.org';
        $config->lti_toolurl = "https://{$domain}/lti/launch";
        $config->lti_tooldomain = $domain;
        $config->lti_ltiversion = LTI_VERSION_1P3;
        $config->lti_clientid = 'test-' . random_string(8);
        $config->lti_coursevisible = LTI_COURSEVISIBLE_PRECONFIGURED;
        $config->lti_contentitem = 1;
        $config->lti_keytype = 'JWK_KEYSET';
        $config->lti_publickeyset = "https://{$domain}/api/lti/jwks";
        $config->lti_initiatelogin = "https://{$domain}/lti/login";
        $config->lti_redirectionuris = "https://{$domain}/lti/launch";

        return lti_add_type($type, $config);
    }

    /**
     * A registration leaves the tool unusable, and activate() fixes exactly that.
     */
    public function test_activate_makes_the_tool_usable(): void {
        global $DB;
        $this->resetAfterTest();

        set_config('registrationurl', 'https://puzzel.test/lti/register', 'local_puzzel');
        $id = $this->register_as_moodle_does('puzzel.test');

        // What Moodle leaves behind: pending, and not in the activity chooser.
        $before = $DB->get_record('lti_types', ['id' => $id], '*', MUST_EXIST);
        $this->assertEquals(LTI_TOOL_STATE_PENDING, $before->state);
        $this->assertEquals(LTI_COURSEVISIBLE_PRECONFIGURED, $before->coursevisible);
        $this->assertFalse(tool::is_ready());

        $this->assertEquals(1, tool::activate());

        $after = $DB->get_record('lti_types', ['id' => $id], '*', MUST_EXIST);
        $this->assertEquals(LTI_TOOL_STATE_CONFIGURED, $after->state);
        $this->assertEquals(LTI_COURSEVISIBLE_ACTIVITYCHOOSER, $after->coursevisible);
        $this->assertTrue(tool::is_ready());
    }

    /**
     * Activating must not drop what the registration configured.
     *
     * lti_update_type() rewrites a type's config rows from whatever it is
     * handed, so a sparse config would silently lose deep linking.
     */
    public function test_activate_keeps_the_registered_config(): void {
        $this->resetAfterTest();

        set_config('registrationurl', 'https://puzzel.test/lti/register', 'local_puzzel');
        $id = $this->register_as_moodle_does('puzzel.test');

        tool::activate();

        $config = lti_get_type_config($id);
        $this->assertEquals(1, $config['contentitem']);
        $this->assertEquals('https://puzzel.test/lti/login', $config['initiatelogin']);
        $this->assertEquals('https://puzzel.test/api/lti/jwks', $config['publickeyset']);
    }

    /**
     * Only tools on the configured domain are touched.
     */
    public function test_activate_leaves_other_tools_alone(): void {
        global $DB;
        $this->resetAfterTest();

        set_config('registrationurl', 'https://puzzel.test/lti/register', 'local_puzzel');
        $otherid = $this->register_as_moodle_does('someone-else.test');

        $this->assertEquals(0, tool::activate());

        $other = $DB->get_record('lti_types', ['id' => $otherid], '*', MUST_EXIST);
        $this->assertEquals(LTI_TOOL_STATE_PENDING, $other->state);
    }

    /**
     * Running it twice changes nothing the second time.
     */
    public function test_activate_is_idempotent(): void {
        $this->resetAfterTest();

        set_config('registrationurl', 'https://puzzel.test/lti/register', 'local_puzzel');
        $this->register_as_moodle_does('puzzel.test');

        $this->assertEquals(1, tool::activate());
        $this->assertEquals(0, tool::activate());
    }
}
