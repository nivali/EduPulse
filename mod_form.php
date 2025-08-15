<?php

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');

class mod_edupulse_mod_form extends moodleform_mod {

    public function definition() {
        $mform = $this->_form;

        // Elemento de nome - obrigatório.
        $mform->addElement('text', 'name', get_string('edupulsename', 'edupulse'), array('size' => '64'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        // Elemento de introdução - editor padrão.
        $this->standard_intro_elements(get_string('intro', 'edupulse'));
        
        // Elementos padrão do curso.
        $this->standard_coursemodule_elements();

        // Botões de ação (salvar/cancelar).
        $this->add_action_buttons();
    }

    public function data_preprocessing(&$default_values) {
        parent::data_preprocessing($default_values);
    }
}