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
 * Learnerscript, a Moodle block to create customizable reports.
 *
 * @package    block_learnerscript
 * @copyright  2023 Moodle India Information Solutions Private Limited
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define('AJAX_SCRIPT', true);
require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/blocks/learnerscript/lib.php');
use block_learnerscript\local\ls;
use block_learnerscript\local\schedule;

global $CFG, $DB, $USER, $OUTPUT, $PAGE;

$rawjson = file_get_contents('php://input');

$requests = json_decode(clean_text($rawjson, FORMAT_PLAIN), true);

$action = $requests['action'];
$reportid = optional_param('reportid', $requests['reportid'], PARAM_INT);
$courseid = optional_param('courseid', $requests['courseid'], PARAM_INT);
$instance = optional_param('instance', $requests['instance'], PARAM_INT);
$jsonformdata = optional_param('jsonformdata', $requests['jsonformdata'], PARAM_TEXT);
$categoryid = optional_param('categoryid', $requests['categoryid'], PARAM_INT);

$context = context_system::instance();
$ls = new ls();
require_login();
$PAGE->set_context($context);

$scheduling = new schedule();
$learnerscript = $PAGE->get_renderer('block_learnerscript');

switch ($action) {
    case 'schreportform':
        $args = new stdClass();
        $args->reportid = $reportid;
        $args->instance = $instance;
        $args->jsonformdata = $jsonformdata;
        $return = block_learnerscript_schreportform_ajaxform($args);
        break;
    case 'courseactivities':
        if ($courseid > 0) {
            $modinfo = get_fast_modinfo($courseid);
            $return[0] = get_string('select_activity', 'block_learnerscript');
            if (!empty($modinfo->cms)) {
                foreach ($modinfo->cms as $k => $cm) {
                    if ($cm->visible == 1 && $cm->deletioninprogress == 0) {
                        $return[$k] = $cm->name;
                    }
                }
            }
        } else {
            $return = [];
        }
        break;
    case 'categorycourses':
        if ($categoryid > 0) {
            $courses = $DB->get_records_sql_menu("SELECT id, fullname
            FROM {course}
            WHERE category = :categoryid AND visible = :visible",
            ['categoryid' => $categoryid, 'visible' => 1]);
            $return = [0 => get_string('selectcourse', 'block_learnerscript')] + $courses;
        } else {
            $return = ['' => get_string('selectcourse', 'block_learnerscript')];
        }
        break;
    case 'sendreportemail':
        $args = new stdClass();
        $args->reportid = $reportid;
        $args->instance = $instance;
        $args->jsonformdata = $jsonformdata;
        $return = block_learnerscript_sendreportemail_ajaxform($args);
        break;
    case 'disablecolumnstatus':
        $reportname = $DB->get_field('block_learnerscript', 'disabletable', ['id' => $reportid]);
        if ($reportname == 1) {
            $plotdata = (new ls)->cr_listof_reporttypes($reportid, false, false);
            $return = $plotdata[0]['chartid'];
        } else {
            $return = 'table';
        }
        break;
    case  'learnerscriptdata':
        $report = $DB->get_record('block_learnerscript', ['id' => $reportid]);
        if (empty($report->summary)) {
            $report->summary = get_string('report_' . $report->type . '_help', 'block_learnerscript');
        }
        $return = $report;
    break;
}

$json = json_encode($return, JSON_NUMERIC_CHECK);
if ($json) {
    echo $json;
} else {
    echo json_last_error_msg();
}
