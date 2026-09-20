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

/**
 * Admin settings and the setup page link.
 *
 * @package    local_puzzel
 * @copyright  2026 Puzzel.org
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $ADMIN->add('localplugins', new admin_category(
        'local_puzzel',
        get_string('pluginname', 'local_puzzel')
    ));

    $ADMIN->add('local_puzzel', new admin_externalpage(
        'local_puzzel_setup',
        get_string('setup', 'local_puzzel'),
        new moodle_url('/local/puzzel/setup.php')
    ));

    $settings = new admin_settingpage(
        'local_puzzel_settings',
        get_string('settings', 'local_puzzel')
    );

    $settings->add(new admin_setting_configtext(
        'local_puzzel/registrationurl',
        get_string('registrationurl', 'local_puzzel'),
        get_string('registrationurl_desc', 'local_puzzel'),
        \local_puzzel\tool::DEFAULT_REGISTRATION_URL,
        PARAM_URL
    ));

    $ADMIN->add('local_puzzel', $settings);
}
