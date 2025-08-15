<?php

defined('MOODLE_INTERNAL') || die();

/**
 * Cria uma nova instância do plugin edupulse.
 *
 * @param stdClass $edupulse Dados enviados pelo formulário.
 * @param mod_edupulse_mod_form $mform O formulário usado para enviar os dados.
 * @return int ID da nova instância do edupulse.
 */
function edupulse_add_instance($edupulse, $mform) {
    global $DB;

    $edupulse->timecreated = time();

    // Insere a nova instância e retorna o ID inserido.
    return $DB->insert_record('edupulse', $edupulse);
}

/**
 * Atualiza uma instância existente do plugin edupulse.
 *
 * @param stdClass $edupulse Dados enviados pelo formulário.
 * @param mod_edupulse_mod_form $mform O formulário usado para enviar os dados.
 * @return boolean True em caso de sucesso.
 */
function edupulse_update_instance($edupulse, $mform) {
    global $DB;

    $edupulse->timemodified = time();
    $edupulse->id = $edupulse->instance;

    // Atualiza a instância existente.
    return $DB->update_record('edupulse', $edupulse);
}

/**
 * Remove uma instância existente do plugin edupulse.
 *
 * @param int $id ID da instância do plugin.
 * @return boolean True em caso de sucesso.
 */
function edupulse_delete_instance($id) {
    global $DB;

    if (!$edupulse = $DB->get_record('edupulse', array('id' => $id))) {
        return false;
    }

    // Exclui o registro da instância.
    $DB->delete_records('edupulse', array('id' => $edupulse->id));

    return true;
}


function edupulse_supports($feature) {
    switch($feature) {
        case FEATURE_COMPLETION_TRACKS_VIEWS: return true;
        case FEATURE_COMPLETION_HAS_RULES: return true;
        default: return null;
    }
}


