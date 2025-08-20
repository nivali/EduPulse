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
 * Form for creating and editing an EduPulse activity.
 *
 * @package    mod_edupulse
 * @copyright  2025 Universidade Federal de Santa Catarina
 * @author     Benjamin Grando Moreira <nivali@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/moodleform_mod.php');

/**
 * Defines the form used to create/edit an EduPulse activity.
 *
 * @package    mod_edupulse
 */
class mod_edupulse_mod_form extends moodleform_mod {

    /**
     * Defines the elements of the form.
     *
     * @return void
     */
    public function definition() {
        $mform = $this->_form;

        // Elemento de nome - obrigatório.
        $mform->addElement('text', 'name', get_string('edupulsename', 'edupulse'), ['size' => '64']);
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
}
