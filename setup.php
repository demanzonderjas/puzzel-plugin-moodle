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
 * Connect this site to Puzzel.org and finish the registration.
 *
 * @package    local_puzzel
 * @copyright  2026 Puzzel.org
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/mod/lti/locallib.php');

use local_puzzel\tool;

admin_externalpage_setup('local_puzzel_setup');

$start = optional_param('start', 0, PARAM_BOOL);
$activate = optional_param('activate', 0, PARAM_BOOL);

$pageurl = new moodle_url('/local/puzzel/setup.php');
$PAGE->set_url($pageurl);
$PAGE->set_title(get_string('setup', 'local_puzzel'));

if ($activate) {
    require_sesskey();
    $changed = tool::activate();
    // On success the status notification below already says the tool is active
    // and in the activity chooser, so a second green box would only repeat it.
    if ($changed > 0) {
        redirect($pageurl);
    }
    redirect(
        $pageurl,
        get_string('activatednone', 'local_puzzel'),
        null,
        \core\output\notification::NOTIFY_INFO
    );
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('setup', 'local_puzzel'));

if ($start) {
    // Mirrors mod_lti's own tool_configure_controller: the registration runs
    // in an iframe and the tool closes it with an lti-dr postMessage.
    $registration = new moodle_url('/mod/lti/startltiadvregistration.php', [
        'url' => tool::registration_url(),
        'sesskey' => sesskey(),
    ]);
    $returnurl = new moodle_url($pageurl, ['activate' => 1, 'sesskey' => sesskey()]);

    echo $OUTPUT->box_start('generalbox');
    echo html_writer::tag('p', get_string('intro_account', 'local_puzzel'));
    if (!empty(tool::types())) {
        // Moodle shows mod_lti's "upgrade or add new" choice when a tool already
        // matches the registration domain, instead of redirecting.
        echo $OUTPUT->notification(
            get_string('upgradenotice', 'local_puzzel'),
            \core\output\notification::NOTIFY_INFO
        );
    }
    echo html_writer::empty_tag('iframe', [
        'src' => $registration->out(false),
        'title' => get_string('connecting', 'local_puzzel'),
        'style' => 'width:100%;height:600px;border:0;',
    ]);
    echo $OUTPUT->box_end();
    echo $OUTPUT->single_button($pageurl, get_string('cancel', 'local_puzzel'), 'get');

    $PAGE->requires->js_amd_inline("
        require([], function() {
            window.addEventListener('message', function(e) {
                if (e.data && e.data.subject === 'org.imsglobal.lti.close') {
                    window.location = " . json_encode($returnurl->out(false)) . ";
                }
            }, false);
        });
    ");
} else {
    $ready = tool::is_ready();
    $registered = !empty(tool::types());

    if ($ready) {
        $statuskey = 'status_ready';
        $level = \core\output\notification::NOTIFY_SUCCESS;
    } else if ($registered) {
        $statuskey = 'status_pending';
        $level = \core\output\notification::NOTIFY_WARNING;
    } else {
        $statuskey = 'status_none';
        $level = \core\output\notification::NOTIFY_INFO;
    }

    echo $OUTPUT->notification(get_string($statuskey, 'local_puzzel'), $level);

    echo html_writer::tag('p', get_string('intro', 'local_puzzel'));
    echo html_writer::tag('p', get_string('intro_subscription', 'local_puzzel'));

    if ($registered && !$ready) {
        echo $OUTPUT->single_button(
            new moodle_url($pageurl, ['activate' => 1, 'sesskey' => sesskey()]),
            get_string('finish', 'local_puzzel'),
            'get'
        );
    }

    echo $OUTPUT->single_button(
        new moodle_url($pageurl, ['start' => 1]),
        get_string($registered ? 'connectagain' : 'connect', 'local_puzzel'),
        'get'
    );
}

echo $OUTPUT->footer();
