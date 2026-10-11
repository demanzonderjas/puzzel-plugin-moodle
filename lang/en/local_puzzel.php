<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Strings for local_puzzel.
 *
 * @package    local_puzzel
 * @copyright  2026 Puzzel.org
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['activated'] = '“{$a}” is now active and shown in the activity chooser.';
$string['activatednone'] = 'Moodle did not register a new Puzzel.org tool, so nothing was activated. If you cancelled the connection, or updated a tool this site already had, that is expected.';
$string['activatefailed'] = 'That tool was not activated. Only a pending Puzzel.org tool can be activated from this page.';
$string['cancel'] = 'Cancel';
$string['clientid'] = 'Client ID';
$string['connect'] = 'Connect Puzzel.org';
$string['connectagain'] = 'Connect again';
$string['connecting'] = 'Connecting to Puzzel.org';
$string['finish'] = 'Activate and show in activity chooser';
$string['intro'] = 'Connecting adds Puzzel.org to this site as an LTI Advantage tool, so teachers can place a Puzzel activity in a course and scores come back to the gradebook.';
$string['intro_account'] = 'You will be asked to sign in with a Puzzel.org account during the connection. That account becomes the owner of the connection.';
$string['intro_activation'] = 'When the connection is complete, the tool it adds to this site is activated and shown in the activity chooser. Tools that are already on this site are not changed.';
$string['intro_subscription'] = 'Placing and playing activities is free. Sending scores back to the Moodle gradebook needs a Puzzel.org subscription.';
$string['managetools'] = 'Change a tool that is already active or rejected under Manage tools';
$string['pluginname'] = 'Puzzel.org';
$string['privacy:metadata'] = 'The Puzzel.org plugin stores no personal data. It only records which Puzzel.org tool registration belongs to this site; everything a student does is handled by the external tool activity itself.';
$string['registered'] = 'Registered';
$string['registrationurl'] = 'Registration URL';
$string['registrationurl_desc'] = 'Where Puzzel.org answers an LTI Advantage dynamic registration. Change this only if you were asked to — for example to test against a staging site.';
$string['settings'] = 'Settings';
$string['setup'] = 'Connect Puzzel.org';
$string['status_none'] = 'Puzzel.org is not connected to this site yet.';
$string['status_other'] = 'This site has a Puzzel.org tool, but teachers do not see it in the activity chooser. This page only activates a pending tool; an active or rejected one keeps the settings an administrator gave it.';
$string['status_pending'] = 'Puzzel.org is registered but not active yet. Activate the pending tool in the list below to finish.';
$string['status_ready'] = 'Puzzel.org is connected and teachers can add it from the activity chooser.';
$string['tools'] = 'Puzzel.org tools on this site';
$string['upgradenotice'] = 'This site already has a Puzzel.org tool. Moodle will ask whether to update that one or add a new tool — updating the existing one is almost always what you want, so that courses already using it keep working.';
$string['visibility'] = 'Tool configuration usage';
