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
 * Show response for the student EduPulse activity.
 *
 * This file handles the display of the EduPulse activity student response.
 *
 * @package    mod_edupulse
 * @copyright  2025 Universidade Federal de Santa Catarina
 * @author     Benjamin Grando Moreira <nivali@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once('lib.php');

// Obtém o ID do módulo de curso.
$id = required_param('id', PARAM_INT);

$cm = get_coursemodule_from_id('edupulse', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
$edupulse = $DB->get_record('edupulse', ['id' => $cm->instance], '*', MUST_EXIST);

require_login($course, true, $cm);

// Configura a página.
$PAGE->set_url('/mod/edupulse/myresponses.php', ['id' => $cm->id]);
$PAGE->set_title(format_string($edupulse->name));
$PAGE->set_heading(format_string($course->fullname));

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('myresponses', 'edupulse') . format_string($edupulse->name));

// Consulta as respostas do usuário autenticado.
$responses = $DB->get_records('edupulse_responses', ['edupulseid' => $edupulse->id, 'userid' => $USER->id]);

if ($responses) {
    echo '<table class="generaltable">';
    echo '<tr>';
    echo '<th>' . get_string('question1', 'edupulse') . '</th>';
    echo '<th>' . get_string('question2', 'edupulse') . '</th>';
    echo '<th>' . get_string('ratingquestion', 'edupulse') . '</th>';
    echo '<th>' . get_string('date', 'edupulse') . '</th>';
    echo '</tr>';
    foreach ($responses as $response) {
        echo '<tr>';
        echo '<td>' . format_text($response->response1) . '</td>';
        echo '<td>' . format_text($response->response2) . '</td>';
        echo '<td>';
        switch ($response->rating) {
            case 1:
                echo get_string('verydissatisfied', 'edupulse');
                break;
            case 2:
                echo get_string('dissatisfied', 'edupulse');
                break;
            case 3:
                echo get_string('neutral', 'edupulse');
                break;
            case 4:
                echo get_string('satisfied', 'edupulse');
                break;
            case 5:
                echo get_string('verysatisfied', 'edupulse');
                break;
            default:
                echo get_string('invalidvalue', 'edupulse');
                break;
        }
        echo '</td>';
        echo '<td>' . userdate($response->timecreated) . '</td>';
        echo '</tr>';
    }
    echo '</table>';
} else {
    echo get_string('noresponses', 'edupulse');
}

echo $OUTPUT->footer();
