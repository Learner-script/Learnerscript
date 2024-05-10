<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace block_learnerscript\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');
use moodleform;

/**
 * A Moodle block to create customizable reports.
 *
 * @package    block_learnerscript
 * @copyright  2023 Moodle India Information Solutions Private Limited
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class basicparams_form extends moodleform {

    /**
     * Form definition
     */
    public function definition() {
        global $COURSE;

        $mform = & $this->_form;
        $mform->_attributes['class'] = "mform basicparamsform" . $this->_customdata->config->id;

        $mform->_attributes['class'] .= " basicparamsform";

        $this->_customdata->add_basicparams_elements($mform);
        $mform->addElement('hidden', 'reportid', $this->_customdata->config->id);
        $mform->setDefault('reportid', $this->_customdata->config->id);
        $mform->setType('reportid', PARAM_INT);

        $mform->addElement('hidden', 'id', $this->_customdata->config->id);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'courseid', $COURSE->id);
        $mform->setType('courseid', PARAM_INT);

        $mform->addElement('submit',  'filter_apply',  get_string('getreport', 'block_learnerscript'));
    }
}
