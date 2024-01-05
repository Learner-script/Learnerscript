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

/** Modules filter */
class plugin_modules extends pluginbase {

    /** @var mixed $singleselection  */
    public $singleselection;

    /** Modules filter init function */
    public function init() {
        $this->form = false;
        $this->unique = true;
        $this->singleselection = true;
        $this->fullname = get_string('filtermodules', 'block_learnerscript');
        $this->reporttypes = ['listofactivities', 'useractivities', 'student_performance',
        'coursesoverview', 'popularresources', 'resources_accessed', 'gradedactivity', 'upcomingactivities', ];
    }

    /**
     * Summary
     * @param  object $data
     * @return string
     */
    public function summary($data) {
        return get_string('filtermodules_summary', 'block_learnerscript');
    }

    /** Execute
     * @param  string $finalelements Final elements
     * @param  object $data          Filter data
     * @return string
     */
    public function execute($finalelements, $data) {

        $filtercourses = optional_param('filter_modules', 0, PARAM_INT);
        if (!$filtercourses) {
            return $finalelements;
        }

        if ($this->report->type != 'sql') {
            return [$filtercourses];
        }
        return $finalelements;
    }

    /**
     * Filter data
     * @param  boolean $selectoption Filter select option
     * @return array
     */
    public function filter_data($selectoption = true) {
        global $DB;

        if ($selectoption) {
            $modulesoptions[0] = $this->singleselection ?
            get_string('filter_module', 'block_learnerscript') :
            get_string('select') .' '. get_string('modules', 'block_learnerscript');
        }
        if ($this->report->type == 'gradedactivity') {
            $modules = $DB->get_records_sql('SELECT DISTINCT m.id, m.name FROM {modules} m
                                               JOIN {grade_items} gi ON gi.itemmodule = m.name
                                              WHERE m.visible = :visible ', ['visible' => 1]);
        } else if ($this->report->type == 'resources_accessed') {
            $modules = $DB->get_records_sql("SELECT id, name
            FROM {modules}
            WHERE visible = :visible", ['visible' => 1]);
            $resources = [];
            foreach ($modules as $module) {
                $resourcearchetype = plugin_supports('mod', $module->name, FEATURE_MOD_ARCHETYPE);
                if ($resourcearchetype) {
                    $resources[] = $module->id;
                }
            }
            list($rsql, $params) = $DB->get_in_or_equal($resources, SQL_PARAMS_NAMED);
            $params['visible'] = 1;
            $modules = $DB->get_records_sql("SELECT id, name
            FROM {modules}
            WHERE visible = :visible AND id $rsql", $params);
        } else {
            $modules = $DB->get_records_sql('SELECT id, name
            FROM {modules}
            WHERE visible = :visible', ['visible' => 1]);
        }
        if (!empty($modules)) {
            foreach ($modules as $module) {
                $modulesoptions[$module->id] = get_string('pluginname', $module->name);
            }
        }
        return $modulesoptions;
    }

    /**
     * Selected filter data
     * @param  boolean $selected Selected filter value
     * @return string
     */
    public function selected_filter($selected) {
        $filterdata = $this->filter_data();
        return $filterdata[$selected];
    }

    /**
     * Print filter
     * @param  object $mform Form data
     */
    public function print_filter(&$mform) {
        $modulesoptions = $this->filter_data();
        $select = $mform->addElement('select', 'filter_modules', get_string('modules', 'block_learnerscript'),
        $modulesoptions, ['data-select2' => 1]);
        $select->setHiddenLabel(true);
        $mform->setType('filter_modules', PARAM_INT);
    }
}
