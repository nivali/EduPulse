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
 * Display all student responses for the EduPulse activity.
 *
 * This file shows the list of responses submitted by students for a given EduPulse
 * activity instance, along with an aggregated satisfaction distribution chart.
 *
 * @package    mod_edupulse
 * @category   output
 * @copyright  2025 Universidade Federal de Santa Catarina
 * @author     Benjamin Grando Moreira <nivali@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../config.php');
require_once('lib.php');

$id = required_param('id', PARAM_INT);

$cm = get_coursemodule_from_id('edupulse', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
$edupulse = $DB->get_record('edupulse', ['id' => $cm->instance], '*', MUST_EXIST);

require_login($course, true, $cm);

$context = context_module::instance($cm->id);
require_capability('mod/edupulse:viewresponses', $context);

$PAGE->set_url('/mod/edupulse/responses.php', ['id' => $cm->id]);
$PAGE->set_title(format_string($edupulse->name));
$PAGE->set_heading(format_string($course->fullname));

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('responsesfor', 'edupulse') . format_string($edupulse->name));

// Consulta as respostas de todos os alunos.
$responses = $DB->get_records('edupulse_responses', ['edupulseid' => $edupulse->id]);

if ($responses) {
    echo '<table class="generaltable">';
    echo '<tr>';
    echo '<th>' . get_string('question1', 'edupulse') . '</th>';
    echo '<th>' . get_string('question2', 'edupulse') . '</th>';
    echo '<th>' . get_string('ratingquestion', 'edupulse') . '</th>';
    echo '<th>' . get_string('date', 'edupulse') . '</th>';
    echo '</tr>';
    foreach ($responses as $response) {
        $user = $DB->get_record('user', ['id' => $response->userid], 'firstname, lastname');
        $username = fullname($user);
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
    echo get_string('noresponsesfound', 'edupulse');
}

// Coleta os dados de satisfação.
$ratings = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
foreach ($responses as $response) {
    if (isset($ratings[$response->rating])) {
        $ratings[$response->rating]++;
    }
}

// Exibe o gráfico de distribuição de satisfação.
echo '<div style="max-width:600px; margin:40px auto;">';
echo '<h3>' . get_string('satisfactiondistribution', 'edupulse') . '</h3>';
echo '<canvas id="satisfacaoChart"></canvas>';
echo '</div>';

echo '<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>';
echo '<script>
    const ctx = document.getElementById("satisfacaoChart").getContext("2d");
    const satisfacaoChart = new Chart(ctx, {
        type: "bar",
        data: {
            labels: [
                "' . get_string('verydissatisfied', 'edupulse') . '",
                "' . get_string('dissatisfied', 'edupulse') . '",
                "' . get_string('neutral', 'edupulse') . '",
                "' . get_string('satisfied', 'edupulse') . '",
                "' . get_string('verysatisfied', 'edupulse') . '"
            ],
            datasets: [{
                label: "' . get_string('numberofresponses', 'edupulse') . '",
                data: [
                    ' . $ratings[1] . ',
                    ' . $ratings[2] . ',
                    ' . $ratings[3] . ',
                    ' . $ratings[4] . ',
                    ' . $ratings[5] . '
                ],
                backgroundColor: [
                    "#e53935", "#fb8c00", "#fbc02d", "#43a047", "#00897b"
                ]
            }]
        },
        options: {
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>';

echo $OUTPUT->footer();
