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

namespace block_learnerscript\output;

use renderable;
use renderer_base;
use templatable;
use stdClass;
use context_system;
use block_learnerscript\local\ls as ls;

/**
 * A Moodle block to create customizable reports.
 *
 * @package   block_learnerscript
 * @copyright 2023 Moodle India Information Solutions Private Limited
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class plotoption implements renderable, templatable {
    /**
     * @var array $plots
     */
    public $plots = [];

    /**
     * @var int $reportid
     */
    public $reportid;
    /**
     * @var $calcbutton
     */
    public $calcbutton;
    /**
     * @var $active
     */
    public $active;
    /**
     * @var $reports
     */
    public $reports;

    /**
     * Construct
     * @param  array $plots      Report graph plots
     * @param  int $reportid   Report ID
     * @param  int $calcbutton Calculation button
     * @param  int $active     Report status
     */
    public function __construct($plots, $reportid, $calcbutton, $active) {
        global $DB, $SESSION;
        $this->plots = $plots;
        $this->reportid = $reportid;
        $this->calcbutton = $calcbutton;
        $this->active = $active;
        $context = context_system::instance();
        if (!empty($SESSION->role) && !has_capability('block/learnerscript:managereports', $context)) {
            $reports = (new ls)->listofreportsbyrole();
        } else {
            $reportlist = $DB->get_records('block_learnerscript', ['global' => 1,
                        'visible' => 1], '', '*', IGNORE_MISSING);
            $reports = [];
            foreach ($reportlist as $report) {
                if ($report->type == 'statistics') {
                    continue;
                }
                $reports[] = ['id' => $report->id, 'name' => $report->name];
            }
        }
        $this->reports = $reports;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     * @param  renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        global $CFG;
        $data = new stdClass();
        $ls = new ls();
        if ($this->active == 'viewreport') {
            $data->searchenable = true;
        }
        $activetab = $this->active;
        $data->plots = $this->plots;
        $data->reportid = $this->reportid;
        $data->calcbutton = $this->calcbutton;
        $data->permissions = 'permissions';
        $data->editicon = 'edit_icon';
        $data->schreportform = 'schreportform';
        $data->addgraphs = 'addgraphs';
        $data->design = 'design';
        $data->viewreport = 'viewreport';
        $data->searchreport = 'searchreport';
        $data->reports = $this->reports;

        $paramcourses = optional_param('filter_courses', 0, PARAM_INT);
        $paramcoursecategories = optional_param('filter_coursecategories', 0, PARAM_INT);
        $paramusers = optional_param('filter_users', 0, PARAM_INT);
        $parammodules = optional_param('filter_modules', 0, PARAM_INT);
        $paramactivities = optional_param('filter_activities', 0, PARAM_INT);
        $paramstatus = optional_param('filter_status', '', PARAM_TEXT);
        $paramstartdate = optional_param('lsfstartdate', 0, PARAM_INT);
        $paramenddate = optional_param('lsfenddate', 0, PARAM_INT);
        $urlfilterparams = ['filter_courses' => $paramcourses,
                    'filter_coursecategories' => $paramcoursecategories,
                    'filter_users' => $paramusers, 'filter_modules' => $parammodules,
                    'filter_activities' => $paramactivities, 'filter_status' => $paramstatus,
                    'lsfstartdate' => $paramstartdate, 'lsfenddate' => $paramenddate, ];
        $urlrequests = array_filter($urlfilterparams);
        $filterparams = '';
        foreach ($urlrequests as $key => $querystring) {
            $filterparams .= '&' . $key . '=' . $querystring;
        }
        $querystringparams = 'id=' . $this->reportid . $filterparams;
        $data->params = $querystringparams;
        unset($data->{$activetab});
        $data->{$activetab} = get_string('activetab', 'block_learnerscript', $activetab);
        $properties = new stdClass();
        $properties->courseid = SITEID;
        $properties->cmid = 0;
        $reportclass = $ls->create_reportclass($this->reportid, $properties);
        $data->permissionsavailable = false;
        if (in_array('permissions', $reportclass->components)) {
            $data->permissionsavailable = true;
        }
        if (isset($reportclass->componentdata->customsql->config->type)
        && (($reportclass->componentdata->customsql->config->type == 'sql')
        || ($reportclass->componentdata->customsql->config->type == 'statistics'))) {
            $data->permissionsavailable = false;
        }
        $data->enableschedule = ($reportclass->parent === true
        && $reportclass->config->type != 'statistics') ? true : false;
            return $data;
    }
}
