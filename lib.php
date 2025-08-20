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
 * setup an EduPulse activity.
 *
 * This file handles to install and desintall the EduPulse.
 *
 * @package    mod_edupulse
 * @copyright  2025 Universidade Federal de Santa Catarina
 * @author     Benjamin Grando Moreira <nivali@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * Creates a new instance of the EduPulse plugin.
 *
 * @param stdClass $edupulse Data submitted from the form.
 * @param mod_edupulse_mod_form $mform The form used to submit the data.
 * @return int ID of the newly created EduPulse instance.
 */
function edupulse_add_instance($edupulse, $mform) {
    global $DB;

    $edupulse->timecreated = time();

    // Inserts the new instance and returns the inserted ID.
    return $DB->insert_record('edupulse', $edupulse);
}

/**
 * Updates an existing instance of the EduPulse plugin.
 *
 * @param stdClass $edupulse Data submitted from the form.
 * @param mod_edupulse_mod_form $mform The form used to submit the data.
 * @return bool True on success.
 */
function edupulse_update_instance($edupulse, $mform) {
    global $DB;

    $edupulse->timemodified = time();
    $edupulse->id = $edupulse->instance;

    // Updates the existing instance.
    return $DB->update_record('edupulse', $edupulse);
}

/**
 * Deletes an existing instance of the EduPulse plugin.
 *
 * @param int $id ID of the plugin instance.
 * @return bool True on success.
 */
function edupulse_delete_instance($id) {
    global $DB;

    if (!$edupulse = $DB->get_record('edupulse', ['id' => $id])) {
        return false;
    }

    // Deletes the instance record.
    $DB->delete_records('edupulse', ['id' => $edupulse->id]);

    return true;
}

/**
 * Defines the features supported by the EduPulse module.
 *
 * @param string $feature The feature to check.
 * @return mixed True if the feature is supported, null otherwise.
 */
function edupulse_supports($feature) {
    switch ($feature) {
        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return true;
        case FEATURE_COMPLETION_HAS_RULES:
            return true;
        default:
            return null;
    }
}
