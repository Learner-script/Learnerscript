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
use block_learnerscript\form\basicparams_form;
use block_learnerscript\local\ls;
use block_learnerscript\local\schedule;
use html_table;
use html_writer;
use moodle_url;
use plugin_renderer_base;
use tabobject;

/**
 * A Moodle block to create customizable reports.
 *
 * @package   block_learnerscript
 * @copyright 2023 Moodle India Information Solutions Private Limited
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends plugin_renderer_base {
    /**
     * Render report table to display the data
     *
     * @param  reporttable $page
     *
     * @return bool|string
     */
    public function render_reporttable(reporttable $page) {
        $data = $page->export_for_template($this);
        return parent::render_from_template('block_learnerscript/reporttable', $data);
    }
    /**
     * Render report plot options
     * @param \block_learnerscript\output\plotoption $page
     * @return bool|string
     */
    public function render_plotoption(\block_learnerscript\output\plotoption $page) {
        $data = $page->export_for_template($this);
        return parent::render_from_template('block_learnerscript/plotoption', $data);
    }
    /**
     * Render report graph tabs
     * @param  \block_learnerscript\output\plottabs $page
     * @return bool|string
     */
    public function render_plottabs(\block_learnerscript\output\plottabs $page) {
        $data = $page->export_for_template($this);
        return parent::render_from_template('block_learnerscript/plottabs', $data);
    }
    /**
     * Render report filter toggle form
     * @param  \block_learnerscript\output\filtertoggleform $page
     * @return bool|string
     */
    public function render_filtertoggleform(\block_learnerscript\output\filtertoggleform $page) {
        $data = $page->export_for_template($this);
        return parent::render_from_template('block_learnerscript/filtertoggleform', $data);
    }
    /**
     * This functiond displays the report
     * @param  object $report Report data
     * @param  object $context User context
     * @param  object $reportclass Report class
     */
    public function viewreport($report, $context, $reportclass) {
        global $USER;
        $reportid = $report->id;

        if ($report->type !== 'statistics') {
            if (has_capability('block/learnerscript:managereports', $context) ||
                (has_capability('block/learnerscript:manageownreports', $context)) && $report->ownerid == $USER->id) {
                $plotoptions = new \block_learnerscript\output\plotoption(false, $report->id, false, 'viewreport');
                echo $this->render($plotoptions);
            }
            $debug = optional_param('debug', false, PARAM_BOOL);
            if ($debug) {
                $debugsql = true;
            }
        }

        if (!empty($reportclass->basicparams)) {
            $basicparamsform = new basicparams_form(null, $reportclass);
            $basicparamsform->set_data($reportclass->params);
            echo $basicparamsform->display();
        }
        $plottabscontent = '';
        $plotdata = (new ls)->cr_listof_reporttypes($report->id, false, false);
        if (!empty($plotdata)) {
            $params = '';
            if (empty($reportclass->basicparams) || !empty($reportclass->params)) {
                $enableplots = 0;
            } else {
                $enableplots = 1;
            }
            $plottabs = new \block_learnerscript\output\plottabs($plotdata, $report->id, $params, $enableplots);
            $plottabscontent = $this->render($plottabs);
        }

        echo html_writer::start_div('', ['id' => 'viewreport'. $report->id]);
        $filterform = $reportclass->print_filters(true);
        $filterform = new \block_learnerscript\output\filtertoggleform($filterform, $plottabscontent);
        echo $this->render($filterform);

        $plotreportcontainer = '';
        if ($report->disabletable == 1 && empty($plotdata)) {
            $plotreportcontainer = html_writer::div(get_string('nodataavailable', 'block_learnerscript'), 'alert alert-info', []);
        }
        echo html_writer::div(html_writer::empty_tag('img', ['src' => $this->output->image_url('t/dockclose'),
        'alt' => get_string('closegraph', 'block_learnerscript'),
        'title' => get_string('closegraph', 'block_learnerscript'), 'class' => 'icon', ]),
        'plotgraphcontainer hide pull-right', ['data-reportid' => $report->id]) .
        html_writer::div($plotreportcontainer, 'ls-report_graph_container', ['id' => "plotreportcontainer$reportid"]);
        if (!empty($plotdata)) {
            echo '';
        }

        if ($report->disabletable == 0) {
            echo html_writer::start_div('', ['id' => "reportcontainer". $report->id]);
            echo html_writer::end_div();
        }
        echo html_writer::end_div();
    }
    /**
     * Scheduled reports data to display in html table
     * @param  integer $reportid ReportID
     * @param  integer $courseid CourseID
     * @param  boolean $table    Table Head(true)/ Table Body (false)
     * @param  integer $start
     * @param  integer $length
     * @param  string $search
     * @return  array $table => true, table head content
     *                     $table=> false, object with scheduled reports
     *                     if  records not found, dispalying info message.
     */
    public function schedulereportsdata($reportid, $courseid = 1, $table = true, $start = 0, $length = 5, $search = '') {

        $scheduledreports = (new schedule)->schedulereports($reportid, $table, $start, $length, $search);
        if ($table) {
            if (!$scheduledreports['totalschreports']) {
                $return = html_writer::tag('center', get_string('noschedule', 'block_learnerscript'),
                ['class' => 'alert alert-info']);
            } else {
                $table = new html_table();
                $table->head = [get_string('role', 'block_learnerscript'),
                    get_string('exportformat', 'block_learnerscript'),
                    get_string('schedule', 'block_learnerscript'),
                    get_string('action'), ];
                $table->size = ['25%', '20%', '40%', '15%'];
                $table->id = 'scheduledtimings';
                $table->attributes['data-reportid'] = $reportid;
                $table->attributes['data-courseid'] = $courseid;
                $return = html_writer::table($table);
            }
        } else {
            $data = [];
            foreach ($scheduledreports['schreports'] as $sreport) {
                $line = [];

                switch ($sreport->role) {
                    case 'admin':
                        $originalrole = get_string('admin');
                        break;
                    case 'manager':
                        $originalrole = get_string('manager', 'role');
                        break;
                    case 'coursecreator':
                        $originalrole = get_string('coursecreators');
                        break;
                    case 'editingteacher':
                        $originalrole = get_string('defaultcourseteacher');
                        break;
                    case 'teacher':
                        $originalrole = get_string('noneditingteacher');
                        break;
                    case 'student':
                        $originalrole = get_string('defaultcoursestudent');
                        break;
                    case 'guest':
                        $originalrole = get_string('guest');
                        break;
                    case 'user':
                        $originalrole = get_string('authenticateduser');
                        break;
                    case 'frontpage':
                        $originalrole = get_string('frontpageuser', 'role');
                        break;
                    // We should not get here, the role UI should require the name for custom roles!
                    default:
                        $originalrole = $sreport->role;
                        break;
                }

                $line[] = $originalrole;
                $line[] = strtoupper($sreport->exportformat);
                $line[] = (new schedule)->get_formatted($sreport->frequency, $sreport->schedule);
                $buttons = [];
                $buttons[] = html_writer::link(new moodle_url('/blocks/learnerscript/components/scheduler/schedule.php',
                ['id' => $reportid, 'courseid' => $courseid, 'scheduleid' => $sreport->id, 'sesskey' => sesskey()]),
                html_writer::empty_tag('img', ['src' => $this->output->image_url('t/edit'),
                'alt' => get_string('edit'), 'class' => 'iconsmall', 'title' => get_string('edit'), ]));
                $buttons[] = html_writer::link(new moodle_url('/blocks/learnerscript/components/scheduler/schedule.php',
                ['id' => $reportid, 'courseid' => $courseid,
                'scheduleid' => $sreport->id, 'sesskey' => sesskey(), 'delete' => 1, ]),
                html_writer::empty_tag('img', ['src' => $this->output->image_url('t/delete'),
                'alt' => get_string('delete'), 'class' => 'iconsmall', 'title' => get_string('delete'), ]));
                $line[] = implode(' ', $buttons);
                $data[] = $line;
            }
            $return = [
                "recordsTotal" => $scheduledreports['totalschreports'],
                "recordsFiltered" => $scheduledreports['totalschreports'],
                "data" => $data,
            ];
        }
        return $return;
    }

    /**
     * This function render the learnerscript configuration
     * @param  \block_learnerscript\output\lsconfig $page
     * @return boolean
     */
    public function render_lsconfig(\block_learnerscript\output\lsconfig $page) {
        $data = $page->export_for_template($this);
        return parent::render_from_template('block_learnerscript/lsconfig', $data);
    }
}
