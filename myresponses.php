<?php

require_once(dirname(__FILE__) . '/../../config.php');
require_once('lib.php');

// Obtém o ID do módulo de curso
$id = required_param('id', PARAM_INT);

$cm = get_coursemodule_from_id('edupulse', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$edupulse = $DB->get_record('edupulse', array('id' => $cm->instance), '*', MUST_EXIST);

require_login($course, true, $cm);

// Configura a página
$PAGE->set_url('/mod/edupulse/myresponses.php', array('id' => $cm->id));
$PAGE->set_title(format_string($edupulse->name));
$PAGE->set_heading(format_string($course->fullname));

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('myresponses', 'edupulse') . format_string($edupulse->name));

// Consulta as respostas do usuário autenticado
$responses = $DB->get_records('edupulse_responses', array('edupulseid' => $edupulse->id, 'userid' => $USER->id));

if ($responses) {
    echo '<table class="generaltable">';
    echo '<tr><th>'. get_string('question1', 'edupulse') .'</th><th>'. get_string('question2', 'edupulse') .'</th><th>'.get_string('ratingquestion','edupulse').'</th><th>'.get_string('date','edupulse').'</th></tr>';
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
?>