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
 * A Moodle block for creating customizable reports
 *
 * @package   block_learnerscript
 * @copyright 2023 Moodle India
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace block_learnerscript\lsreports;
use block_learnerscript\local\pluginbase;
use block_learnerscript\local\reportbase;
use block_learnerscript\local\ls;
use context_system;
use html_writer;
/** Courses Columns */
class plugin_coursescolumns extends pluginbase {

    /** @var string $reportinstance  */
    public $reportinstance;

    /** @var string $role  */
    public $role;

    /** @var array $reportfilterparams  */
    public $reportfilterparams;

    /**
     * Course columns init function
     */
    public function init() {
        $this->fullname = get_string('coursescolumns', 'block_learnerscript');
        $this->type = 'undefined';
        $this->form = true;
        $this->reporttypes = ['courses'];
    }
    /**
     * Course column summary
     * @param object $data Course column name
     */
    public function summary($data) {
        return format_string($data->columname);
    }
    /**
     * This function return field column format
     * @param object $data Field data
     */
    public function colformat($data) {
        $align = (isset($data->align)) ? $data->align : '';
        $size = (isset($data->size)) ? $data->size : '';
        $wrap = (isset($data->wrap)) ? $data->wrap : '';
        return [$align, $size, $wrap];
    }
    /**
     * This function executes the columns data
     * @param object $data Columns data
     * @param object $row Row data
     * @param string $reporttype Report type
     * @return object|string
     */
    public function execute($data, $row, $reporttype = 'table') {
        global $DB, $CFG, $USER, $OUTPUT;
        $context = context_system::instance();
        $usercoursesreportid = $DB->get_field('block_learnerscript', 'id',
        ['type' => 'usercourses', 'name' => 'Learners activity summary'], IGNORE_MULTIPLE);
        $competencyreportid = $DB->get_field('block_learnerscript', 'id',
        ['type' => 'coursecompetency'], IGNORE_MULTIPLE);
        $searchicon = '<img class = "searchicon" src = "'.$CFG->wwwroot.'/blocks/reportdashboard/pix/courseprofile/search.png" />';
        switch ($data->column) {
            case 'progress':
                if (!isset($row->progress) && isset($data->subquery)) {
                    $progress = $DB->get_field_sql($data->subquery);
                } else {
                    $progress = $row->{$data->column};
                }
                if ($progress == "") {
                        $progress = '0.00';
                }
                $progress = round($progress, 2);
                $progresscheckpermissions = empty($usercoursesreportid) ? false :
                (new reportbase($usercoursesreportid))->check_permissions($context, $this->reportclass->userid);
                if (empty($usercoursesreportid) || empty($progresscheckpermissions)) {
                    $avgcompletedlink = $progress;
                } else {
                    $avgcompletedlink = $progress;
                }
                $row->{$data->column} = html_writer::div($avgcompletedlink . '%', "spark-report",
                                ['id' => html_writer::random_id(),
                                'data-sparkline' => "$progress; progressbar",
                                'data-labels' => 'inprogress, completed', ]);
                break;
            case 'activities':
                if (!isset($row->activities) && isset($data->subquery)) {
                    $activities = $DB->get_field_sql($data->subquery);
                } else {
                    $activities = $row->{$data->column};
                }
                $listofactivitiesreportid = $DB->get_field('block_learnerscript', 'id',
                ['type' => 'courseactivities'], IGNORE_MULTIPLE);
                $checkpermissions = empty($listofactivitiesreportid) ? false :
                (new reportbase($listofactivitiesreportid))->check_permissions($context, $USER->id);
                if (empty($listofactivitiesreportid) || empty($checkpermissions)) {
                    $row->{$data->column} = $activities;
                } else {
                    $row->{$data->column} = html_writer::link(new \moodle_url($CFG->wwwroot .
                    '/blocks/learnerscript/viewreport.php',
                    ['id' => $listofactivitiesreportid, 'filter_courses' => $row->id]),
                    $activities.$searchicon, ["target" => "_blank"]);
                }

            break;
            case 'competencies':
                if (!isset($row->competencies) && isset($data->subquery)) {
                    $competencies = $DB->get_field_sql($data->subquery);
                } else {
                    $competencies = $row->{$data->column};
                }
                $enrolcheckpermissions = empty($competencyreportid) ? false :
                (new reportbase($competencyreportid))->check_permissions($context, $USER->id);
                if (empty($competencyreportid) || empty($enrolcheckpermissions)) {
                    $row->{$data->column} = $competencies;
                } else {
                    $row->{$data->column} = html_writer::link(new \moodle_url($CFG->wwwroot .
                    '/blocks/learnerscript/viewreport.php',
                    ['id' => $competencyreportid, 'filter_courses' => $row->id,
                    'filter_status' => get_string('all', 'block_learnerscript')]),
                    $competencies.$searchicon, ["target" => "_blank"]);
                }

            break;
            case 'enrolments':
                if (!isset($row->enrolments) && isset($data->subquery)) {
                    $enrolments = $DB->get_field_sql($data->subquery);
                } else {
                    $enrolments = $row->{$data->column};
                }
                $enrolcheckpermissions = empty($usercoursesreportid) ? false :
                (new reportbase($usercoursesreportid))->check_permissions($context, $USER->id);
                if (empty($usercoursesreportid) || empty($enrolcheckpermissions)) {
                    $row->{$data->column} = $enrolments;
                } else {
                    $row->{$data->column} = html_writer::link(new \moodle_url($CFG->wwwroot .
                    '/blocks/learnerscript/viewreport.php',
                    ['id' => $usercoursesreportid, 'filter_courses' => $row->id,
                    'filter_status' => get_string('all', 'block_learnerscript')]),
                    $enrolments.$searchicon, ["target" => "_blank"]);
                }

            break;
            case 'completed':
                if (!isset($row->completed) && isset($data->subquery)) {
                    $completed = $DB->get_field_sql($data->subquery);
                } else {
                    $completed = $row->{$data->column};
                }

                $comcheckpermissions = empty($usercoursesreportid) ? false :
                (new reportbase($usercoursesreportid))->check_permissions($context, $USER->id);
                if (empty($usercoursesreportid) || empty($comcheckpermissions)) {
                    $row->{$data->column} = $completed;
                } else {
                    $row->{$data->column} = html_writer::link(new \moodle_url($CFG->wwwroot .
                    '/blocks/learnerscript/viewreport.php',
                    ['id' => $usercoursesreportid, 'filter_courses' => $row->id,
                    'filter_status' => get_string('completed', 'block_learnerscript')]),
                    $completed.$searchicon, ["target" => "_blank"]);
                }
            break;
            case 'badges':
                if (!isset($row->badges) && isset($data->subquery)) {
                    $badges = $DB->get_field_sql($data->subquery);
                } else {
                    $badges = $row->{$data->column};
                }
                 $row->{$data->column} = !empty($badges) ? $badges : 0;
            break;
            case 'highgrade':
                if (!isset($row->highgrade) && isset($data->subquery)) {
                    $highgrade = $DB->get_field_sql($data->subquery);
                } else {
                    $highgrade = $row->{$data->column};
                }
                if ($reporttype == 'table') {
                    $row->{$data->column} = !empty($highgrade) ? round($highgrade, 2) : '--';
                } else {
                    $row->{$data->column} = !empty($highgrade) ? round($highgrade, 2) : 0;
                }
            break;
            case 'lowgrade':
                if (!isset($row->lowgrade) && isset($data->subquery)) {
                    $lowgrade = $DB->get_field_sql($data->subquery);
                } else {
                    $lowgrade = $row->{$data->column};
                }
                if ($reporttype == 'table') {
                    $row->{$data->column} = !empty($lowgrade) ? round($lowgrade, 2) : '--';
                } else {
                    $row->{$data->column} = !empty($lowgrade) ? round($lowgrade, 2) : 0;
                }
            break;
            case 'avggrade':
                if (!isset($row->avggrade) && isset($data->subquery)) {
                    $avggrade = $DB->get_field_sql($data->subquery);
                } else {
                    $avggrade = $row->{$data->column};
                }
                if ($reporttype == 'table') {
                    $row->{$data->column} = !empty($avggrade) ? round($avggrade, 2) : '--';
                } else {
                    $row->{$data->column} = !empty($avggrade) ? round($avggrade, 2) : 0;
                }
            break;
            case 'totaltimespent':
                if (!isset($row->totaltimespent) && isset($data->subquery)) {
                    $totaltimespent = $DB->get_field_sql($data->subquery);
                } else {
                    $totaltimespent = $row->{$data->column};
                }
                if ($reporttype == 'table') {
                    $row->{$data->column} = !empty($totaltimespent) ? (new ls)->strtime($totaltimespent) : '--';
                } else {
                    $row->{$data->column} = !empty($totaltimespent) ? $totaltimespent : 0;
                }
                break;
            case 'numviews':
                $reportid = $DB->get_field('block_learnerscript', 'id', ['type' => 'courseviews'], IGNORE_MULTIPLE);
                $comcheckpermissions = empty($reportid) ? false :
                (new reportbase($reportid))->check_permissions($context, $USER->id);
                if (empty($reportid) || empty($comcheckpermissions)) {
                    $row->{$data->column} = '--';
                } else {
                    return html_writer::link(new \moodle_url($CFG->wwwroot . '/blocks/learnerscript/viewreport.php',
                    ['id' => $reportid, 'filter_courses' => $row->id,
                    'filter_status' => get_string('completed', 'block_learnerscript')]),
                    $OUTPUT->pix_icon('views', '', 'block_reportdashboard', ['target' => '_blank']));
                }
            break;
            case 'status':
                $coursestatus = $DB->get_field_sql('SELECT visible FROM {course} WHERE id = :rowid', ['rowid' => $row->id]);
                if ($coursestatus == 1) {
                    $coursestat = '<span class="label label-success">' . get_string('active') .'</span>';
                } else if ($coursestatus == 0) {
                    $coursestat = '<span class="label label-warning">' . get_string('inactive') .'</span>';
                }
                $row->{$data->column} = $coursestat;
                break;
            case 'enrolmethods':
                if (!isset($row->enrolmethods) && isset($data->subquery)) {
                    $enrolmethods = $DB->get_field_sql($data->subquery);
                } else {
                    $enrolmethods = $row->{$data->column};
                }
                $row->{$data->column} = !empty($enrolmethods) ? $enrolmethods : '--';
            break;
            default:
                return (isset($row->{$data->column})) ? $row->{$data->column} : '--';
            break;
        }
            return (isset($row->{$data->column})) ? $row->{$data->column} : '--';
    }
}
