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
 * View page for the EduPulse module.
 *
 * This file handles the display of the EduPulse activity, including the form for students
 * and the ability for teachers to view responses.
 *
 * @package    mod_edupulse
 * @copyright  2025 Universidade Federal de Santa Catarina
 * @author     Benjamin Grando Moreira <nivali@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once('lib.php');

$id = optional_param('id', 0, PARAM_INT);
$instance = optional_param('instance', 0, PARAM_INT);

if ($id) {
    $cm = get_coursemodule_from_id('edupulse', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
    $edupulse = $DB->get_record('edupulse', ['id' => $cm->instance], '*', MUST_EXIST);
} else if ($instance) {
    $edupulse = $DB->get_record('edupulse', ['id' => $instance], '*', MUST_EXIST);
    $course = $DB->get_record('course', ['id' => $edupulse->course], '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('edupulse', $edupulse->id, $course->id, false, MUST_EXIST);
} else {
    throw new moodle_exception('missingparam', 'edupulse');
}

require_login($course, true, $cm);

$context = context_module::instance($cm->id);
$PAGE->set_url('/mod/edupulse/view.php', ['id' => $cm->id]);
$PAGE->set_title(format_string($edupulse->name));
$PAGE->set_heading(format_string($course->fullname));

echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($edupulse->name));

// Check if the user has permission to view other responses.
if (has_capability('mod/edupulse:viewresponses', $context)) {
    echo $OUTPUT->notification(
        '<p><a href="responses.php?id=' . $cm->id . '">' . get_string('viewresponses', 'edupulse') . '</a></p>',
        null
    );
}

// Handle form submission.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response1 = clean_param(optional_param('response1', '', PARAM_TEXT), PARAM_TEXT);
    $response2 = clean_param(optional_param('response2', '', PARAM_TEXT), PARAM_TEXT);
    $rating = clean_param(optional_param('rating', '', PARAM_INT), PARAM_INT);

    if (empty($rating)) {
        echo $OUTPUT->notification(get_string('notifyproblem', 'edupulse'), 'notifyproblem');
    } else {
        // Insert the response into the database.
        $newresponse = new stdClass();
        $newresponse->edupulseid = $edupulse->id;
        $newresponse->userid = $USER->id;
        $newresponse->response1 = $response1;
        $newresponse->response2 = $response2;
        $newresponse->rating = $rating;
        $newresponse->timecreated = time();

        $DB->insert_record('edupulse_responses', $newresponse);

        echo $OUTPUT->notification(get_string('responsesubmitted', 'edupulse'), 'notifysuccess');
    }
}

// Check if the user has already responded to the questionnaire.
$response = $DB->get_record('edupulse_responses', ['edupulseid' => $edupulse->id, 'userid' => $USER->id]);

if ($response) {
    // Notify the user that they have already responded.
    echo $OUTPUT->notification(
        '<p>' . get_string('alreadyanswered', 'edupulse') . '</p>' .
        '<p><a href="myresponses.php?id=' . $cm->id . '">' . get_string('viewyourresponse', 'edupulse') . '</a></p>',
        'notifysuccess'
    );
} else {
    // Display the EduPulse form.
    echo '<div style="display: flex; justify-content: center;">';
    echo '<form method="post" action="" style="width:100%; max-width:600px;">';

    // Radio buttons for evaluation.
    echo '<div style="margin-bottom: 1em;">';
    echo '<p>' . get_string('ratingquestion', 'edupulse') . '</p>';
    echo '<div style="display: flex; gap: 20px;">';
    echo '<label style="color: #e53935;"><input type="radio" name="rating" value="1" style="accent-color: #e53935;" required> ' .
        get_string('verydissatisfied', 'edupulse') . '</label>';
    echo '<label style="color: #fb8c00;"><input type="radio" name="rating" value="2" style="accent-color: #fb8c00;"> ' .
        get_string('dissatisfied', 'edupulse') . '</label>';
    echo '<label style="color: #fbc02d;"><input type="radio" name="rating" value="3" style="accent-color: #fbc02d;"> ' .
        get_string('neutral', 'edupulse') . '</label>';
    echo '<label style="color: #43a047;"><input type="radio" name="rating" value="4" style="accent-color: #43a047;"> ' .
        get_string('satisfied', 'edupulse') . '</label>';
    echo '<label style="color: #00897b;"><input type="radio" name="rating" value="5" style="accent-color: #00897b;"> ' .
        get_string('verysatisfied', 'edupulse') . '</label>';
    echo '</div>';
    echo '</div>';

    echo '<div style="height:1px; background:linear-gradient(to right, #e0e0e0, #bdbdbd); margin:24px 0;"></div>';

    // Textual questions.
    echo '<div>';
    echo '<label for="response1">' . get_string('question1', 'edupulse') . '</label><br/>';
    echo '<textarea name="response1" rows="4" cols="50" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ccc; ' .
        'box-shadow:0 2px 6px rgba(0,0,0,0.07); resize:vertical; font-size:1em;"></textarea>';
    echo '</div>';

    echo '<div style="height:1px; background:linear-gradient(to right, #e0e0e0, #bdbdbd); margin:24px 0;"></div>';

    echo '<div>';
    echo '<label for="response2">' . get_string('question2', 'edupulse') . '</label><br/>';
    echo '<textarea name="response2" rows="4" cols="50" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ccc; ' .
        'box-shadow:0 2px 6px rgba(0,0,0,0.07); resize:vertical; font-size:1em;"></textarea>';
    echo '</div>';

    echo '<div style="height:1px; background:linear-gradient(to right, #e0e0e0, #bdbdbd); margin:24px 0;"></div>';

    echo '<div style="display: flex; justify-content: center;">';
    echo '<input type="submit" value="' . get_string('submit', 'edupulse') . '">';
    echo '</div>';

    echo '</form>';
    echo '</div>';
}

echo $OUTPUT->footer();
