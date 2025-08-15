<?php

require_once(dirname(__FILE__) . '/../../config.php');
require_once('lib.php');

$id = optional_param('id', 0, PARAM_INT);
$instance = optional_param('instance', 0, PARAM_INT);

if ($id) {
    $cm = get_coursemodule_from_id('edupulse', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $edupulse = $DB->get_record('edupulse', array('id' => $cm->instance), '*', MUST_EXIST);
} else if ($instance) {
    $edupulse = $DB->get_record('edupulse', array('id' => $instance), '*', MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $edupulse->course), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('edupulse', $edupulse->id, $course->id, false, MUST_EXIST);
} else {
    throw new moodle_exception('missingparam', 'edupulse');
}

require_login($course, true, $cm);

$context = context_module::instance($cm->id);
$PAGE->set_url('/mod/edupulse/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($edupulse->name));
$PAGE->set_heading(format_string($course->fullname));

echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($edupulse->name));

// Verifica se o usuário tem permissão para ver as respostas dos outros
if (has_capability('mod/edupulse:viewresponses', $context)) {
    echo $OUTPUT->notification('<p><a href="responses.php?id=' . $cm->id . '">' . get_string('viewresponses', 'edupulse') . '</a></p>', null);
}

// Processamento da submissão do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response1 = clean_param(optional_param('response1', '', PARAM_TEXT), PARAM_TEXT);
    $response2 = clean_param(optional_param('response2', '', PARAM_TEXT), PARAM_TEXT);
    $rating = clean_param(optional_param('rating', '', PARAM_INT), PARAM_INT);

    if (empty($rating)) {
        echo $OUTPUT->notification(get_string('notifyproblem', 'edupulse'), 'notifyproblem');
    } else {
        // Insere as respostas no banco de dados
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

// Verifica se o usuário já respondeu ao questionário
$response = $DB->get_record('edupulse_responses', array('edupulseid' => $edupulse->id, 'userid' => $USER->id));

if ($response) {
    // Mensagem informando que o usuário já respondeu
    echo $OUTPUT->notification('<p>' . get_string('alreadyanswered', 'edupulse') . '</p><p><a href="myresponses.php?id=' . $cm->id . '">' . get_string('viewyourresponse', 'edupulse') . '</a></p>', 'notifysuccess');
} else {
    // Exibição do formulário do EduPulse
    echo '<div style="display: flex; justify-content: center;">';
    echo '<form method="post" action="" style="width:100%; max-width:600px;">';

    // Botões de rádio para avaliação
    echo '<div style="margin-bottom: 1em;">';
    echo '<p>' . get_string('ratingquestion', 'edupulse') . '</p>';
    echo '<div style="display: flex; gap: 20px;">';
    echo '<label style="color: #e53935;"><input type="radio" name="rating" value="1" style="accent-color: #e53935;"> ' . get_string('verydissatisfied', 'edupulse') . '</label>';
    echo '<label style="color: #fb8c00;"><input type="radio" name="rating" value="2" style="accent-color: #fb8c00;"> ' . get_string('dissatisfied', 'edupulse') . '</label>';
    echo '<label style="color: #fbc02d;"><input type="radio" name="rating" value="3" style="accent-color: #fbc02d;"> ' . get_string('neutral', 'edupulse') . '</label>';
    echo '<label style="color: #43a047;"><input type="radio" name="rating" value="4" style="accent-color: #43a047;"> ' . get_string('satisfied', 'edupulse') . '</label>';
    echo '<label style="color: #00897b;"><input type="radio" name="rating" value="5" style="accent-color: #00897b;"> ' . get_string('verysatisfied', 'edupulse') . '</label>';
    echo '</div>';
    echo '</div>';

    echo '<div style="height:1px; background:linear-gradient(to right, #e0e0e0, #bdbdbd); margin:24px 0;"></div>';

    // Questões textuais
    echo '<div>';
    echo '<label for="response1">' . get_string('question1', 'edupulse') . '</label><br/>';
    echo '<textarea name="response1" rows="4" cols="50" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ccc; box-shadow:0 2px 6px rgba(0,0,0,0.07); resize:vertical; font-size:1em;"></textarea>';
    echo '</div>';

    echo '<div style="height:1px; background:linear-gradient(to right, #e0e0e0, #bdbdbd); margin:24px 0;"></div>';

    echo '<div>';
    echo '<label for="response2">' . get_string('question2', 'edupulse') . '</label><br/>';
    echo '<textarea name="response2" rows="4" cols="50" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ccc; box-shadow:0 2px 6px rgba(0,0,0,0.07); resize:vertical; font-size:1em;"></textarea>';
    echo '</div>';

    echo '<div style="height:1px; background:linear-gradient(to right, #e0e0e0, #bdbdbd); margin:24px 0;"></div>';

    echo '<div style="display: flex; justify-content: center;">';
    echo '<input type="submit" value="' . get_string('submit', 'edupulse') . '">';
    echo '</div>';

    echo '</form>';
    echo '</div>';
}

echo $OUTPUT->footer();
?>