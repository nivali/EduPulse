<?php

require_once(dirname(__FILE__) . '/../../config.php');
require_once('lib.php');

$id = required_param('id', PARAM_INT);

$cm = get_coursemodule_from_id('edupulse', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
$edupulse = $DB->get_record('edupulse', array('id' => $cm->instance), '*', MUST_EXIST);

require_login($course, true, $cm);

$context = context_module::instance($cm->id);
require_capability('mod/edupulse:viewresponses', $context);

$PAGE->set_url('/mod/edupulse/responses.php', array('id' => $cm->id));
$PAGE->set_title(format_string($edupulse->name));
$PAGE->set_heading(format_string($course->fullname));

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('responsesfor', 'edupulse') . format_string($edupulse->name));

// Consulta as respostas de todos os alunos
$responses = $DB->get_records('edupulse_responses', array('edupulseid' => $edupulse->id));

if ($responses) {
    echo '<table class="generaltable">';
    echo '<tr><th>'. get_string('question1', 'edupulse') .'</th><th>'. get_string('question2', 'edupulse') .'</th><th>'. get_string('ratingquestion', 'edupulse') .'</th><th>'.get_string('date','edupulse').'</th></tr>';
    foreach ($responses as $response) {
        $user = $DB->get_record('user', array('id' => $response->userid), 'firstname, lastname');
        $username = fullname($user);
        echo '<tr>';
        //echo '<td>' . $username . '</td>';
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

// Coleta os dados de satisfação
$ratings = array(1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0);
foreach ($responses as $response) {
    if (isset($ratings[$response->rating])) {
        $ratings[$response->rating]++;
    }
}
?>
<div style="max-width:600px; margin:40px auto;">
    <h3><?php echo get_string('satisfactiondistribution', 'edupulse'); ?></h3>
    <canvas id="satisfacaoChart"></canvas>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('satisfacaoChart').getContext('2d');
    const satisfacaoChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [
                '<?php echo get_string('verydissatisfied', 'edupulse'); ?>',
                '<?php echo get_string('dissatisfied', 'edupulse'); ?>',
                '<?php echo get_string('neutral', 'edupulse'); ?>',
                '<?php echo get_string('satisfied', 'edupulse'); ?>',
                '<?php echo get_string('verysatisfied', 'edupulse'); ?>'
            ],
            datasets: [{
                label: '<?php echo get_string('numberofresponses', 'edupulse'); ?>',
                data: [
                    <?php echo $ratings[1]; ?>,
                    <?php echo $ratings[2]; ?>,
                    <?php echo $ratings[3]; ?>,
                    <?php echo $ratings[4]; ?>,
                    <?php echo $ratings[5]; ?>
                ],
                backgroundColor: [
                    '#e53935', '#fb8c00', '#fbc02d', '#43a047', '#00897b'
                ]
            }]
        },
        options: {
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>

<?php
echo $OUTPUT->footer();
?>