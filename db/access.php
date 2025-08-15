<?php

defined('MOODLE_INTERNAL') || die();

$capabilities = array(

    // Capacidade para visualizar suas próprias respostas ao questionário
    'mod/edupulse:viewownresponses' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => array(
            'student' => CAP_ALLOW,
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW,
        ),
    ),

    // Capacidade para visualizar todas as respostas dos alunos
    'mod/edupulse:viewresponses' => array(
        'captype' => 'read',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => array(
            'teacher' => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager' => CAP_ALLOW,
        ),
    ),

    // Capacidade para responder ao questionário
    'mod/edupulse:submitresponse' => array(
        'captype' => 'write',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes' => array(
            'student' => CAP_ALLOW,
        ),
    ),
);