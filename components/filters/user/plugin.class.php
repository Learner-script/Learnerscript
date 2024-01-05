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

/**
 * A Moodle block to create customizable reports.
 *
 * @package   block_learnerscript
 * @copyright 2023 Moodle India
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace block_learnerscript\lsreports;
use block_learnerscript\local\pluginbase;
use block_learnerscript\local\ls as ls;
use context_course;

/** User filter */
class plugin_user extends pluginbase {

    /** User filter init function */
    public function init() {
        $this->form = false;
        $this->unique = true;
        $this->fullname = get_string('filteruser', 'block_learnerscript');
        $this->reporttypes = ['sql'];
    }

    /**
     * Summary
     * @param  object $data
     * @return string
     */
    public function summary($data) {
        return get_string('filteruser_summary', 'block_learnerscript');
    }

    /** Execute
     * @param  string $finalelements Final elements
     * @param  object $data          Filter data
     * @return string
     */
    public function execute($finalelements, $data) {
        $filteruser = optional_param('filter_user', 0, PARAM_INT);
        if (!$filteruser) {
            return $finalelements;
        }

        if ($this->report->type != 'sql') {
            return [$filteruser];
        } else {
            if (preg_match("/%%FILTER_COURSEUSER:([^%]+)%%/i", $finalelements, $output)) {
                $replace = ' AND ' . $output[1] . ' = ' . $filteruser;
                return str_replace('%%FILTER_COURSEUSER:' . $output[1] . '%%', $replace, $finalelements);
            }
        }
        return $finalelements;
    }

    /**
     * Filter data
     * @param  boolean $selectoption Filter select option
     * @param array $request Filter request parameters
     * @return array
     */
    public function filter_data($selectoption = true, $request = []) {
        global $DB, $COURSE;

        $reportclassname = 'block_learnerscript\lsreports\report_' . $this->report->type;
        $reportclass = new $reportclassname($this->report);

        if ($this->report->type != 'sql') {
            $components = (new ls)->cr_unserialize($this->report->components);
            $conditions = $components['conditions'];
            $userlist = $reportclass->elements_by_conditions($conditions);
        } else {
            $coursecontext = context_course::instance($COURSE->id);
            $userlist = array_keys(get_users_by_capability($coursecontext, 'moodle/user:viewdetails'));
        }

        $useroptions = [];
        if ($selectoption) {
            $useroptions[0] = get_string('filter_user', 'block_learnerscript');
        }

        if (!empty($userlist)) {
            list($usql, $params) = $DB->get_in_or_equal($userlist, SQL_PARAMS_NAMED);
            $users = $DB->get_records_select('user', "id $usql", $params);

            foreach ($users as $c) {
                $useroptions[$c->id] = format_string($c->lastname . ' ' . $c->firstname);
            }
        }
        return $useroptions;
    }

    /**
     * Selected filter data
     * @param  boolean $selected Selected filter value
     * @param array $request Filter request parameters
     * @return array
     */
    public function selected_filter($selected, $request) {
        $filterdata = $this->filter_data(true, $request);
        return $filterdata[$selected];
    }

    /**
     * Print filter
     * @param  object $mform Form data
     */
    public function print_filter(&$mform) {
        $useroptions = $this->filter_data();
        $select = $mform->addElement('select', 'filter_user', get_string('user'), $useroptions);
        $select->setHiddenLabel(true);
        $mform->setType('filter_user', PARAM_INT);
    }

}
