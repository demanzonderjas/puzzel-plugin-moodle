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
$registered = optional_param('registered', 0, PARAM_BOOL);
$after = optional_param('after', 0, PARAM_INT);
$activate = optional_param('activate', 0, PARAM_INT);

$pageurl = new moodle_url('/local/puzzel/setup.php');
$PAGE->set_url($pageurl);
$PAGE->set_title(get_string('setup', 'local_puzzel'));

if ($registered) {
    // Back from a registration: activate the tool it created, and only that
    // one. Tools that were on the site before it started are not touched.
    require_sesskey();
    $created = tool::created_after($after);
    if ($created && tool::activate((int) $created->id)) {
        redirect(
            $pageurl,
            get_string('activated', 'local_puzzel', format_string($created->name)),
            null,
            \core\output\notification::NOTIFY_SUCCESS
        );
    }
    redirect(
        $pageurl,
        get_string('activatednone', 'local_puzzel'),
        null,
        \core\output\notification::NOTIFY_INFO
    );
}

if ($activate) {
    // The administrator picked one pending tool from the list below.
    require_sesskey();
    $pending = tool::pending();
    if (isset($pending[$activate]) && tool::activate($activate)) {
        redirect(
            $pageurl,
            get_string('activated', 'local_puzzel', format_string($pending[$activate]->name)),
            null,
            \core\output\notification::NOTIFY_SUCCESS
        );
    }
    redirect(
        $pageurl,
        get_string('activatefailed', 'local_puzzel'),
        null,
        \core\output\notification::NOTIFY_WARNING
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
    // The newest tool id from before the registration, so that the way back
    // can tell the tool this registration creates from every other one.
    $returnurl = new moodle_url($pageurl, [
        'registered' => 1,
        'after' => tool::newest_type_id(),
        'sesskey' => sesskey(),
    ]);

    echo $OUTPUT->box_start('generalbox');
    echo html_writer::tag('p', get_string('intro_account', 'local_puzzel'));
    echo html_writer::tag('p', get_string('intro_activation', 'local_puzzel'));
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
    $types = tool::types();
    $pending = tool::pending();

    if (tool::is_ready()) {
        $statuskey = 'status_ready';
        $level = \core\output\notification::NOTIFY_SUCCESS;
    } else if (!empty($pending)) {
        $statuskey = 'status_pending';
        $level = \core\output\notification::NOTIFY_WARNING;
    } else if (!empty($types)) {
        // Configured but kept out of the chooser, or rejected: an
        // administrator's decision, which this plugin does not overrule.
        $statuskey = 'status_other';
        $level = \core\output\notification::NOTIFY_INFO;
    } else {
        $statuskey = 'status_none';
        $level = \core\output\notification::NOTIFY_INFO;
    }

    echo $OUTPUT->notification(get_string($statuskey, 'local_puzzel'), $level);

    echo html_writer::tag('p', get_string('intro', 'local_puzzel'));
    echo html_writer::tag('p', get_string('intro_subscription', 'local_puzzel'));

    if (!empty($types)) {
        // Every Puzzel.org tool on the site, so that it is plain which one a
        // button activates. Only a pending tool gets a button.
        $states = [
            LTI_TOOL_STATE_CONFIGURED => get_string('active', 'mod_lti'),
            LTI_TOOL_STATE_PENDING => get_string('pending', 'mod_lti'),
            LTI_TOOL_STATE_REJECTED => get_string('rejected', 'mod_lti'),
        ];
        $visibility = [
            LTI_COURSEVISIBLE_NO => get_string('show_in_course_no', 'mod_lti'),
            LTI_COURSEVISIBLE_PRECONFIGURED => get_string('show_in_course_preconfigured', 'mod_lti'),
            LTI_COURSEVISIBLE_ACTIVITYCHOOSER => get_string('show_in_course_activity_chooser', 'mod_lti'),
        ];

        $table = new html_table();
        $table->caption = get_string('tools', 'local_puzzel');
        $table->head = [
            get_string('typename', 'mod_lti'),
            get_string('clientid', 'local_puzzel'),
            get_string('registered', 'local_puzzel'),
            get_string('status'),
            get_string('visibility', 'local_puzzel'),
            '',
        ];
        foreach ($types as $type) {
            $action = '';
            if (isset($pending[$type->id])) {
                $action = $OUTPUT->single_button(
                    new moodle_url($pageurl, ['activate' => $type->id, 'sesskey' => sesskey()]),
                    get_string('finish', 'local_puzzel'),
                    'get'
                );
            }
            $table->data[] = [
                format_string($type->name),
                s($type->clientid),
                userdate($type->timecreated),
                $states[(int) $type->state] ?? '',
                $visibility[(int) $type->coursevisible] ?? '',
                $action,
            ];
        }
        echo html_writer::table($table);
        echo html_writer::tag('p', html_writer::link(
            new moodle_url('/mod/lti/toolconfigure.php'),
            get_string('managetools', 'local_puzzel')
        ));
    }

    echo $OUTPUT->single_button(
        new moodle_url($pageurl, ['start' => 1]),
        get_string(empty($types) ? 'connect' : 'connectagain', 'local_puzzel'),
        'get'
    );
}

echo $OUTPUT->footer();
