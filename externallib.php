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

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/blocks/learnerscript/lib.php');
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;
use block_learnerscript\local\ls as ls;
use block_learnerscript\local\reportbase as reportbase;
use block_learnerscript\local\schedule;
use block_learnerscript_license_setting as lssetting;

/**
 * Learnerscript external functions
 *
 * @package    block_learnerscript
 * @copyright  2023 Moodle India Information Solutions Private Limited
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_learnerscript_external extends external_api {
    /**
     * Roles wise users parameters description
     * @return external_function_parameters
     */
    public static function rolewiseusers_parameters() {
        return new external_function_parameters(
            [
                'roleid' => new external_value(PARAM_INT, 'role id of report', VALUE_DEFAULT),
                'term' => new external_value(PARAM_TEXT, 'Current search term in search box', VALUE_DEFAULT),
                'contextlevel' => new external_value(PARAM_INT, 'contextlevel of role', VALUE_DEFAULT),
                'page' => new external_value(PARAM_INT, 'Current page number to request', VALUE_DEFAULT),
                '_type' => new external_value(PARAM_RAW, 'A "request type" will be usually a query', VALUE_DEFAULT),
                'reportid' => new external_value(PARAM_RAW, 'Report id of report', VALUE_DEFAULT),
                'action' => new external_value(PARAM_TEXT, 'action', VALUE_DEFAULT),
                'maximumselectionlength' => new external_value(PARAM_INT, 'maximum selection length to search', VALUE_DEFAULT),
                'courses' => new external_value(PARAM_INT, 'Course id of report', VALUE_DEFAULT),
            ]
        );
    }
    /**
     * Displaying list of users based on the selected role and search string.
     *
     * @param int $roleid Role ID
     * @param string $term Search text
     * @param int $contextlevel Role contextlevel
     * @param int $page Page
     * @param string $type Type of the filter
     * @param int $reportid Report ID
     * @param string $action Action
     * @param int $maximumselectionlength Maximum length of the entered string
     * @param array $courses Courses list
     */
    public static function rolewiseusers($roleid, $term, $contextlevel, $page,
    $type, $reportid, $action, $maximumselectionlength, $courses) {
        global $DB, $CFG;
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('block/learnerscript:managereports', $context);
        $pagevariables = get_pagevariables();
        $pagevariables->set_context($context);
        $roles = $roleid;
        // We always must pass webservice params through validate_parameters.
        self::validate_parameters(self::rolewiseusers_parameters(), ['roleid' => $roleid, 'term' => $term,
        'contextlevel' => $contextlevel, 'page' => $page, '_type' => $type, 'reportid' => $reportid,
        'action' => $action, 'maximumselectionlength' => $maximumselectionlength, 'courses' => $courses, ]);

        if ((has_capability('block/learnerscript:managereports', $context) ||
            has_capability('block/learnerscript:manageownreports', $context) ||
            is_siteadmin()) && !empty($roles)) {
            if ($roles == -1) {
                $siteadmins = $CFG->siteadmins;
                $adminssql = "SELECT u.id, CONCAT(u.firstname, ' ' , u.lastname) AS fullname
                                    FROM {user} u
                                   WHERE 1 = 1
                                     AND u.id IN ($siteadmins)";

                $admins = $DB->get_records_sql($adminssql, ['siteadmins' => 'siteadmins']);
                $userlist = [];
                foreach ($admins as $admin) {
                    $userlist[] = ['id' => $admin->id, 'text' => $admin->fullname];
                }
            } else {
                $userlist = (new schedule)->rolewiseusers($roles, $term, $page, $reportid, $contextlevel);
            }
            $termsdata = [];
            $termsdata['total_count'] = count($userlist);
            $termsdata['incomplete_results'] = false;
            $termsdata['items'] = $userlist;
            $return = $termsdata;
        } else {
            $termsdata = [];
            $termsdata['error'] = true;
            $termsdata['type'] = 'Warning';
            if (empty($roles)) {
                $termsdata['cap'] = false;
                $termsdata['msg'] = get_string('missingparam', 'block_learnerscript', 'Role');
            } else {
                $termsdata['cap'] = true;
                $termsdata['msg'] = get_string('badpermissions', 'block_learnerscript');
            }
            $termsdata['total_count'] = 0;
            $termsdata['incomplete_results'] = false;
            $termsdata['items'] = [];
            $return = $termsdata;
        }
        $data = json_encode($return);
        return $data;
    }
    /**
     * Roles wise users
     * @return external_description
     */
    public static function rolewiseusers_returns() {
        return new external_value(PARAM_RAW, 'data');
    }
    /**
     * User roles parameters
     * @return external_function_parameters
     */
    public static function roleusers_parameters() {
        return new external_function_parameters(
            [
                'reportid' => new external_value(PARAM_INT, 'Report id of report', VALUE_DEFAULT),
                'scheduleid' => new external_value(PARAM_INT, 'selected schedule for report', VALUE_DEFAULT),
                'selectedroleid' => new external_value(PARAM_RAW, 'selected role for report', VALUE_DEFAULT),
                'roleid' => new external_value(PARAM_RAW, 'roleid for report', VALUE_DEFAULT),
                'contextlevel' => new external_value(PARAM_INT, 'contextlevel of role', VALUE_DEFAULT),
                'term' => new external_value(PARAM_TEXT, 'Current search term in search box', VALUE_DEFAULT),
                '_type' => new external_value(PARAM_TEXT, 'A "request type" will be usually a query', VALUE_DEFAULT),
                'bullkselectedusers' => new external_value(PARAM_RAW, 'bulk users selected', VALUE_DEFAULT),
            ]
        );
    }
    /**
     * Displays the list of users based on selected roles
     * @param int $reportid Scheduled report ID
     * @param int $scheduleid Schedule ID
     * @param int $selectedroleid Selected role id to share the scheduled report
     * @param int $roleid Roled ID
     * @param int $contextlevel Contextlevel of the selected role
     * @param string $term Search text
     * @param string $type Type of the report
     * @param string $bullkselectedusers Selected users
     */
    public static function roleusers($reportid, $scheduleid, $selectedroleid,
    $roleid, $contextlevel, $term, $type, $bullkselectedusers) {
        global $DB, $CFG;
        $roleid = json_decode($roleid);
        $bullkselectedusers = json_decode($bullkselectedusers);
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('block/learnerscript:managereports', $context);
        // We always must pass webservice params through validate_parameters.
        self::validate_parameters(self::roleusers_parameters(), ['reportid' => $reportid, 'scheduleid' => $scheduleid,
        'selectedroleid' => $selectedroleid, 'roleid' => $roleid, 'contextlevel' => $contextlevel, 'term' => $term,
        'type' => $type, 'bullkselectedusers' => $bullkselectedusers, ]);

        if ((has_capability('block/learnerscript:managereports', $context) ||
            has_capability('block/learnerscript:manageownreports', $context) ||
            is_siteadmin()) && !empty($reportid) && !empty($type) && !empty($roleid)) {
            if ($roleid == -1) {
                 $escselsql = "";
                if ($bullkselectedusers) {
                    $bullkselectedusersdata = implode(',', $bullkselectedusers);
                    $escselsql = " AND u.id NOT IN ($bullkselectedusersdata) ";
                }
                $siteadmins = $CFG->siteadmins;
                $adminssql = "SELECT u.id, CONCAT(u.firstname, ' ' , u.lastname) AS fullname
                                    FROM {user} u
                                   WHERE 1 = 1
                                     AND u.id IN ($siteadmins) $escselsql";
                $admins = $DB->get_records_sql($adminssql, ['siteadmins' => 'siteadmins']);
                $userslist = [];
                foreach ($admins as $admin) {
                    $userslist[] = ['id' => $admin->id, 'fullname' => $admin->fullname];
                }
            } else {
                $userslist = (new schedule)->schroleusers($reportid, $scheduleid, $type,
                                                    $roleid, $term, $bullkselectedusers, $contextlevel);
            }
            $termsdata = [];
            $termsdata['total_count'] = count($userslist);
            $termsdata['incomplete_results'] = false;
            $termsdata['items'] = $userslist;
            $return = $termsdata;
        } else {
            $termsdata = [];
            $termsdata['error'] = true;
            $termsdata['type'] = 'Warning';
            if (empty($reportid)) {
                $termsdata['cap'] = false;
                $termsdata['msg'] = get_string('missingparam', 'block_learnerscript', 'ReportID');
            } else if (empty($type)) {
                $termsdata['cap'] = false;
                $termsdata['msg'] = get_string('missingparam', 'block_learnerscript', 'Type');
            } else if (empty($roles)) {
                $termsdata['cap'] = false;
                $termsdata['msg'] = get_string('missingparam', 'block_learnerscript', 'Role');
            } else {
                $termsdata['cap'] = true;
                $termsdata['msg'] = get_string('badpermissions', 'block_learnerscript');
            }
            $return = $termsdata;
        }
        $data = json_encode($return);
        return $data;
    }
    /**
     * User roles
     * @return external_description
     */
    public static function roleusers_returns() {
        return new external_value(PARAM_RAW, 'data');
    }
    /**
     * View Schedule Users parameters description
     * @return external_function_parameters
     */
    public static function viewschuserstable_parameters() {
        return new external_function_parameters(
            [
                'reportid' => new external_value(PARAM_INT, 'Report id of report', VALUE_DEFAULT),
                'scheduleid' => new external_value(PARAM_INT, 'selected schedule for report', VALUE_DEFAULT),
                'schuserslist' => new external_value(PARAM_RAW, 'list of scheduled users', VALUE_DEFAULT),
            ]
        );
    }
    /**
     * View Schedule Users
     * @param int $reportid Report ID
     * @param int $scheduleid Report scheduled ID
     * @param string $schuserslist Scheduled users list
     */
    public static function viewschuserstable($reportid, $scheduleid, $schuserslist) {
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('block/learnerscript:managereports', $context);
        self::validate_parameters(self::viewschuserstable_parameters(), ['reportid' => $reportid,
        'scheduleid' => $scheduleid, 'schuserslist' => $schuserslist, ]);

        if ((has_capability('block/learnerscript:managereports', $context) ||
            has_capability('block/learnerscript:manageownreports', $context) ||
            is_siteadmin()) && !empty($schuserslist)) {
            $stable = new stdClass();
            $stable->table = true;
            $return = (new schedule)->viewschusers($reportid, $scheduleid, $schuserslist, $stable);
        } else {
            $termsdata = [];
            $termsdata['error'] = true;
            $termsdata['type'] = 'Warning';
            if (empty($schuserslist)) {
                $termsdata['cap'] = false;
                $termsdata['msg'] = get_string('missingparam', 'block_learnerscript', 'Schedule Users List');
            } else {
                $termsdata['cap'] = true;
                $termsdata['msg'] = get_string('badpermissions', 'block_learnerscript');
            }
            $return = $termsdata;
        }
        $data = json_encode($return);
        return $data;
    }
    /**
     * View Schedule Users
     * @return external_description
     */
    public static function viewschuserstable_returns() {
        return new external_value(PARAM_RAW, 'data');
    }
    /**
     * Manage Schedule Users description
     */
    public static function manageschusers_is_allowed_from_ajax() {
        return true;
    }
    /**
     * Manage Schedule Users parameters description
     * @return external_function_parameters
     */
    public static function manageschusers_parameters() {
        return new external_function_parameters(
            [
                'reportid' => new external_value(PARAM_INT, 'report id of report', VALUE_DEFAULT),
                'scheduleid' => new external_value(PARAM_RAW, 'schedule id', VALUE_DEFAULT),
                'schuserslist' => new external_value(PARAM_RAW, '', VALUE_DEFAULT),
                'selectedroleid' => new external_value(PARAM_RAW, 'selected role id', VALUE_DEFAULT),
                'reportinstance' => new external_value(PARAM_INT, 'report instance', VALUE_DEFAULT),

            ]
        );
    }
    /**
     * Manage Schedule Users description
     * @param int $reportid Report ID
     * @param int $scheduleid Schedule ID
     * @param array $schuserslist Scheduled users list
     * @param int $selectedroleid Scheduled report ID
     * @param string $reportinstance Report instance type
     */
    public static function manageschusers($reportid, $scheduleid, $schuserslist, $selectedroleid, $reportinstance) {
        global $OUTPUT;
        $pagevariables = get_pagevariables();
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('block/learnerscript:managereports', $context);
        $pagevariables->set_context($context);

        // We always must pass webservice params through validate_parameters.
        self::validate_parameters(self::manageschusers_parameters(), ['reportid' => $reportid, 'scheduleid' => $scheduleid,
        'schuserslist' => $schuserslist, 'selectedroleid' => $selectedroleid, 'reportinstance' => $reportinstance, ]);

        if ((has_capability('block/learnerscript:managereports', $context) ||
            has_capability('block/learnerscript:manageownreports', $context) ||
            is_siteadmin()) && !empty($reportid)) {
            $roleslist = (new schedule)->reportroles($selectedroleid, $reportid);
            $selectedusers = (new schedule)->selectesuserslist($schuserslist);
            $reqimage = $OUTPUT->image_url('req');
            $scheduledata = new \block_learnerscript\output\scheduledusers($reportid,
            $reqimage, $roleslist, $selectedusers, $scheduleid, $reportinstance);
            $learnerscript = $pagevariables->get_renderer('block_learnerscript');
            $return = $learnerscript->render($scheduledata);
        } else {
            $termsdata = [];
            $termsdata['error'] = true;
            $termsdata['type'] = 'Warning';
            if (empty($reportid)) {
                $termsdata['cap'] = false;
                $termsdata['msg'] = get_string('missingparam', 'block_learnerscript', 'ReportID');
            } else {
                $termsdata['cap'] = true;
                $termsdata['msg'] = get_string('badpermissions', 'block_learnerscript');
            }
            $return = $termsdata;
        }
        $data = json_encode($return);
        return $data;
    }
    /**
     * Manage Schedule Users description returns
     * @return external_description
     */
    public static function manageschusers_returns() {
        return new external_value(PARAM_RAW, 'data');
    }
    /**
     * Schedule Report Form parameters description
     * @return external_function_parameters
     */
    public static function schreportform_parameters() {
        return new external_function_parameters(
            [
                'reportid' => new external_value(PARAM_INT, 'report id of report', VALUE_DEFAULT),
                'instance' => new external_value(PARAM_INT, 'Instance', VALUE_DEFAULT),
                'schuserslist' => new external_value(PARAM_RAW, 'List of scheduled users', VALUE_DEFAULT),
            ]
        );
    }
    /**
     * Schedule Report Form
     * @param int $reportid Report ID
     * @param int $instance Report instance
     * @param string $schuserslist Scheduled users list
     */
    public static function schreportform($reportid, $instance, $schuserslist) {
        global $CFG, $DB;
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('block/learnerscript:managereports', $context);

        $pagevariables = get_pagevariables();
        $pagevariables->set_context(context_system::instance());

        // We always must pass webservice params through validate_parameters.
        self::validate_parameters(self::schreportform_parameters(), ['reportid' => $reportid,
        'instance' => $instance, 'schuserslist' => $schuserslist, ]);

        if ((has_capability('block/learnerscript:managereports', $context) ||
            has_capability('block/learnerscript:manageownreports', $context) ||
            is_siteadmin()) && !empty($reportid)) {
            require_once($CFG->dirroot . '/blocks/learnerscript/components/scheduler/schedule_form.php');
            $roleslist = (new schedule)->reportroles('', $reportid);
            list($schusers, $schusersids) = (new schedule)->userslist($reportid, $scheduleid);
            $exportoptions = (new ls)->cr_get_export_plugins();
            $frequencyselect = (new schedule)->get_options();
            $scheduledreport = $DB->get_record('block_ls_schedule', ['id' => $scheduleid]);
            if (!empty($scheduledreport)) {
                $schedulelist = (new schedule)->getschedule($scheduledreport->frequency);
            } else {
                $schedulelist = [null => get_string('selectall', 'block_reportdashboard')];
            }
            $scheduleform = new scheduled_reports_form($CFG->wwwroot . '/blocks/learnerscript/components/scheduler/schedule.php',
            ['id' => $reportid, 'scheduleid' => $scheduleid, 'AjaxForm' => true, 'roles_list' => $roleslist,
                'schusers' => $schusers, 'schusersids' => $schusersids, 'exportoptions' => $exportoptions,
                'schedule_list' => $schedulelist, 'frequencyselect' => $frequencyselect, 'instance' => $instance, ]);
            $return = $scheduleform->render();
        } else {
            $termsdata = [];
            $termsdata['error'] = true;
            $termsdata['type'] = 'Warning';
            if (empty($reportid)) {
                $termsdata['cap'] = false;
                $termsdata['msg'] = get_string('missingparam', 'block_learnerscript', 'ReportID');
            } else {
                $termsdata['cap'] = true;
                $termsdata['msg'] = get_string('badpermissions', 'block_learnerscript');
            }
            $return = $termsdata;
        }
        $data = json_encode($return);
        return $data;
    }
    /**
     * Schedule Report Form description returns
     * @return external_description
     */
    public static function schreportform_returns() {
        return new external_value(PARAM_RAW, 'data');
    }
    /**
     * Scheduled Timings parameters description
     * @return external_function_parameters
     */
    public static function scheduledtimings_parameters() {
        return new external_function_parameters(
            [
                'action' => new external_value(PARAM_TEXT, 'action', VALUE_DEFAULT),
                'reportid' => new external_value(PARAM_INT, 'report id of report', VALUE_DEFAULT),
                'search' => new external_value(PARAM_TEXT, 'search value', VALUE_DEFAULT),
                'length' => new external_value(PARAM_INT, 'length of string', VALUE_DEFAULT),
                'courseid' => new external_value(PARAM_INT, 'The id for the course', VALUE_DEFAULT),
            ]
        );
    }
    /**
     * Scheduled Timings description
     * @param int $reportid Report ID
     * @param int $courseid Course ID
     * @param int $start Page start
     * @param int $length Length of scheduled records
     * @param string $search Search text
     */
    public static function scheduledtimings($reportid, $courseid, $start, $length, $search) {
        $pagevariables = get_pagevariables();
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('block/learnerscript:managereports', $context);
         // We always must pass webservice params through validate_parameters.
        self::validate_parameters(self::scheduledtimings_parameters(), ['reportid' => $reportid,
        'courseid' => $courseid, 'start' => $start, 'length' => $length, 'search' => $search, ]);

        $learnerscript = $pagevariables->get_renderer('block_learnerscript');
        if ((has_capability('block/learnerscript:managereports', $context) ||
            has_capability('block/learnerscript:manageownreports', $context) ||
            is_siteadmin()) && !empty($reportid)) {
            $return = $learnerscript->schedulereportsdata($reportid, $courseid, false, $start, $length, $search['value']);
        } else {
            $termsdata = [];
            $termsdata['error'] = true;
            $termsdata['type'] = 'Warning';
            if (empty($reportid)) {
                $termsdata['cap'] = false;
                $termsdata['msg'] = get_string('missingparam', 'block_learnerscript', 'ReportID');
            } else {
                $termsdata['cap'] = true;
                $termsdata['msg'] = get_string('badpermissions', 'block_learnerscript');
            }
            $return = $termsdata;
        }
        $data = json_encode($return);
        return $data;
    }
    /**
     * Scheduled Timings description
     * @return external_description
     */
    public static function scheduledtimings_returns() {
        return new external_value(PARAM_RAW, 'data');
    }
    /**
     * Generate Plotgraph parameters description
     * @return external_function_parameters
     */
    public static function generate_plotgraph_parameters() {
        return new external_function_parameters(
            [
                'reportid' => new external_value(PARAM_INT, 'report id of report', VALUE_DEFAULT),
                'courseid' => new external_value(PARAM_INT, 'course id of course', VALUE_DEFAULT),
                'cmid' => new external_value(PARAM_INT, 'The course module id for the course', VALUE_DEFAULT),
                'status' => new external_value(PARAM_TEXT, 'status', VALUE_DEFAULT),
                'userid' => new external_value(PARAM_RAW, 'user id', VALUE_DEFAULT),
                'lsfstartdate' => new external_value(PARAM_RAW, 'start date for date filter', VALUE_DEFAULT),
                'lsfenddate' => new external_value(PARAM_RAW, 'end date for date filter', VALUE_DEFAULT),
                'reporttype' => new external_value(PARAM_RAW, 'type of report', VALUE_DEFAULT),
                'action' => new external_value(PARAM_RAW, 'action', VALUE_DEFAULT),
                'singleplot' => new external_value(PARAM_RAW, 'single plot', VALUE_DEFAULT),
                'cols' => new external_value(PARAM_RAW, 'columns', VALUE_DEFAULT),
                'instanceid' => new external_value(PARAM_RAW, 'id of instance', VALUE_DEFAULT),
                'container' => new external_value(PARAM_RAW, 'container', VALUE_DEFAULT),
                'filters' => new external_value(PARAM_RAW, 'applied filters', VALUE_DEFAULT),
                'basicparams' => new external_value(PARAM_RAW, 'basic params required to generate graph', VALUE_DEFAULT),
                'columnDefs' => new external_value(PARAM_RAW, 'column definitions', VALUE_DEFAULT),
                'reportdashboard' => new external_value(PARAM_RAW, 'report dashboard', VALUE_DEFAULT, true),
            ]
        );
    }
    /**
     * Generate Plotgraph description
     * @param int $reportid Report ID
     * @param int $courseid Course ID
     * @param int $cmid Course module ID
     * @param int $status Report status
     * @param int $userid User ID
     * @param int $lsfstartdate Start date
     * @param int $lsfenddate End date
     * @param string $reporttype Report type
     * @param string $action Action
     * @param int $singleplot Singleplot
     * @param array $cols Report columns
     * @param int $instanceid Report instance ID
     * @param int $container Report container
     * @param string $filters Report filters list
     * @param string $basicparams Mandatory filters list
     * @param array $columndefs Column definations
     * @param boolean $reportdashboard Reportdashboard
     */
    public static function generate_plotgraph($reportid, $courseid, $cmid, $status, $userid,
        $lsfstartdate, $lsfenddate, $reporttype, $action, $singleplot, $cols, $instanceid,
        $container, $filters, $basicparams, $columndefs, $reportdashboard) {
        global $DB;
        $ls = new ls();
        $pagevariables = get_pagevariables();
        // We always must pass webservice params through validate_parameters.
        self::validate_parameters(self::generate_plotgraph_parameters(), ['reportid' => $reportid,
        'courseid' => $courseid, 'cmid' => $cmid, 'status' => $status, 'userid' => $userid,
        'lsfstartdate' => $lsfstartdate, 'lsfenddate' => $lsfenddate, 'reporttype' => $reporttype,
        'action' => $action, 'singleplot' => $singleplot, 'cols' => $cols, 'instanceid' => $instanceid,
        'container' => $container, 'filters' => $filters, 'basicparams' => $basicparams,
        'columnDefs' => $columndefs, 'reportdashboard' => $reportdashboard, ]);

        $context = context_system::instance();
        self::validate_context($context);
        require_capability('block/learnerscript:managereports', $context);

        $filters = json_decode($filters, true);
        $basicparams = json_decode($basicparams, true);
        if (empty($basicparams)) {
            $basicparams = [];
        }
        $pagevariables->set_context(context_system::instance());
        $learnerscript = $pagevariables->get_renderer('block_learnerscript');

        if (!$report = $DB->get_record('block_learnerscript', ['id' => $reportid])) {
            throw new moodle_exception('reportdoesnotexists', 'block_learnerscript');
        }

        $properties = new stdClass();
        $properties->lsstartdate = !empty($filters['lsfstartdate']) ? $filters['lsfstartdate'] : 0;
        $properties->lsenddate   = !empty($filters['lsfenddate']) ? $filters['lsfenddate'] : time();
        $reportclass = $ls->create_reportclass($reportid, $properties);
        $reportclass->params = array_merge( $filters, (array)$basicparams);
        $reportclass->cmid = $cmid;
        $reportclass->courseid = isset($courseid) ? $courseid :
        (isset($reportclass->params['filter_courses']) ? $reportclass->params['filter_courses'] : SITEID);
        $reportclass->status = $status;
        $reporttype = !empty($reporttype) ? $reporttype : 'table';
        if ($reporttype != 'table') {
            $reportclass->start = 0;
            $reportclass->length = -1;
            $reportclass->reporttype = $reporttype;
        }
        if ($reportdashboard && $report->type == 'statistics') {
            $reportdatatable = false;
        } else {
            $reportdatatable = true;
        }

        $reportclass->create_report();

        if ($reportdatatable && $reporttype == 'table') {
            $datacolumns = [];
            $columndefs = [];
            $i = 0;
            $re = [];
            if (!empty($reportclass->orderable)) {
                $re = array_diff(array_keys($reportclass->finalreport->table->head), $reportclass->orderable);
            }
            if (empty($reportclass->finalreport->table->data)) {
                $return['tdata'] = html_writer::div(get_string("nodataavailable", "block_learnerscript"),
                                    'alert alert-info', []);
                $return['reporttype'] = 'table';
                $return['emptydata'] = 1;
                $reporttitle = get_string('report_' . $report->type, 'block_learnerscript');
                $return['reportname'] = (new ls)->get_reporttitle($reporttitle, $basicparams);
            } else {
                foreach ($reportclass->finalreport->table->head as $key => $value) {
                    $datacolumns[]['data'] = $value;
                    $columndef = new stdClass();
                    $align = isset($reportclass->finalreport->table->align[$i]) ?
                    $reportclass->finalreport->table->align[$i] : 'left';
                    $wrap = isset($reportclass->finalreport->table->wrap[$i])
                    && ($reportclass->finalreport->table->wrap[$i] == 'wrap') ? 'break-all' : 'normal';
                    $width = isset($reportclass->finalreport->table->size[$i])
                    ? $reportclass->finalreport->table->size[$i] : '';
                    $columndef->className = 'dt-body-' . $align;
                    $columndef->targets = $i;
                    $columndef->wrap = $wrap;
                    $columndef->width = $width;
                    if (!empty($re[$i]) && $re[$i]) {
                        $columndef->orderable = false;
                    } else {
                        $columndef->orderable = true;
                    }
                    $columndefs[] = $columndef;
                    $i++;
                }
                $export = explode(',', $reportclass->config->export);
                if (!empty($reportclass->finalreport->table->head)) {
                    $tablehead = (new ls)->report_tabledata($reportclass->finalreport->table);
                    $reporttable = new \block_learnerscript\output\reporttable($reportclass,
                        $tablehead,
                        $reportclass->finalreport->table->id,
                        $export,
                        $reportid,
                        $reportclass->sql,
                        $report->type,
                        false,
                        false,
                        $instanceid
                    );
                    $return = [];
                    foreach ($reportclass->finalreport->table->data as $key => $value) {
                        $data[$key] = array_values($value);
                    }
                    $return['tdata'] = $learnerscript->render($reporttable);
                    $return['data'] = [
                                            "draw" => true,
                                            "recordsTotal" => $reportclass->totalrecords,
                                            "recordsFiltered" => $reportclass->totalrecords,
                                            "data" => $data,
                        ];
                    $reporttitle = get_string('report_' . $report->type, 'block_learnerscript');
                    $return['reportname'] = (new ls)->get_reporttitle($reporttitle, $basicparams);
                    $return['columnDefs'] = $columndefs;
                    $return['reporttype'] = 'table';
                    $return['emptydata'] = 0;
                } else {
                    $return['emptydata'] = 1;
                    $return['reporttype'] = 'table';
                    $return['tdata'] = html_writer::div(get_string("nodataavailable", "block_learnerscript"),
                    'alert alert-info', []);
                }
            }
        } else {
            if ($report->type != 'statistics') {
                $seriesvalues = (isset($reportclass->componentdata['plot']['elements'])) ?
                $reportclass->componentdata['plot']['elements'] : [];
                $i = 0;
                $reporttitle = get_string('report_' . $report->type, 'block_learnerscript');
                $return['reportname'] = (new ls)->get_reporttitle($reporttitle, $basicparams);
                foreach ($seriesvalues as $g) {
                    if (($reporttype != '' && $g['id'] == $reporttype) || $i == 0) {
                        $return['plot'] = (new ls)->generate_report_plot($reportclass, $g);
                        if ($reporttype != '' && $g['id'] == $reporttype) {
                            break;
                        }
                    }
                    $return['plotoptions'][] = ['id' => $g['id'],
                    'title' => $g['formdata']->chartname, 'pluginname' => $g['pluginname'], ];
                    $i++;
                }
            } else {
                if ($reporttype == 'pie') {
                    foreach ($reportclass->finalreport->table->data[0] as $k => $r) {
                        $r = strip_tags($r);
                        if (is_numeric($r)) {
                            $piedata[] = ['name' => $reportclass->finalreport->table->head[$k], 'y' => $r];
                        }
                    }
                } else if ($reporttype == 'solidgauge') {
                    $radius = 112;
                    $innerradius = 88;
                    $colors = ['#90ed7d', 'rgb(67, 67, 72)', 'rgb(124, 181, 236)'];
                    foreach ($reportclass->finalreport->table->data[0] as $k => $r) {
                        $r = strip_tags($r);
                        $radius = $radius - 25;
                        $innerradius = $innerradius - 25;
                        if (is_numeric($r)) {
                            $piedata[] = ['name' => $reportclass->finalreport->table->head[$k],
                            'data' => [[ 'color' => $colors[$k], 'radius' => $radius.'%',
                            'innerradius' => $innerradius.'%' , 'y' => $r, ], ], ];
                        }
                    }
                } else {
                    $i = 0;
                    $categorydata = [];
                    if (!empty($reportclass->finalreport->table->data[0])) {
                        foreach ($reportclass->finalreport->table->data[0] as $k => $r) {
                                $r = strip_tags($r);
                                $r = is_numeric($r) ? $r : $r;
                                $seriesdata[] = $reportclass->finalreport->table->head[$k];
                                $graphdata[$i][] = $r;
                                $categorydata[] = $reportclass->finalreport->table->head[$k];
                                $i++;
                        }
                    }
                    $comdata = [];
                    $comdata['dataLabels'] = ['enabled' => 1];
                    $comdata['borderRadius'] = 5;
                    if (!empty($graphdata)) {
                        $i = 0;
                        foreach ($graphdata as $key => $value) {
                            if ($reporttype == 'table') {
                                $comdata['data'][] = [$value[0]];
                            } else {
                                $comdata['data'][] = ['y' => $value[0], 'label' => $value[0]];
                            }
                            $i++;
                        }
                        $piedata = [$comdata];
                    } else {
                        $piedata = $comdata;
                    }
                }
                $return['plot'] = ['type' => $reporttype,
                                    'containerid' => 'reportcontainer' . $instanceid . '',
                                    'name' => $report->name,
                                    'categorydata' => $categorydata,
                                    'tooltip' => '{point.y}',
                                    'datalabels' => 1,
                                    'showlegend' => 0,
                                    'id' => '{point.y}',
                                    'height' => '210',
                                    'data' => $piedata, ];
                $return['plotoptions'][] = ['id' => random_string(5), 'title' => $report->name, 'pluginname' => $reporttype];
            }
        }
        if ($reporttype == 'table') {
            $data = json_encode($return, JSON_PRESERVE_ZERO_FRACTION);
        } else {
            $data = json_encode($return, JSON_NUMERIC_CHECK);
        }
        return $data;
    }
    /**
     * Generate Plotgraph description
     * @return external_description
     */
    public static function generate_plotgraph_returns() {
        return new external_value(PARAM_RAW, 'data');
    }
    /**
     * Frequency Schedule parameters description
     * @return external_function_parameters
     */
    public static function frequency_schedule_parameters() {
        return new external_function_parameters(
            [
                'frequency' => new external_value(PARAM_INT, 'schedule frequency', VALUE_DEFAULT),
            ]
        );
    }
    /**
     * Frequency Schedule
     * @param string $frequency Report schedule frequency
     */
    public static function frequency_schedule($frequency) {
        $return = (new schedule)->getschedule($frequency);
        // We always must pass webservice params through validate_parameters.
        self::validate_parameters(self::frequency_schedule_parameters(), ['frequency' => $frequency]);

        $context = context_system::instance();
        self::validate_context($context);
        require_capability('block/learnerscript:managereports', $context);
        if (empty($return)) {
            $return = [null => get_string('selectall', 'block_reportdashboard')];
        }
        $data = json_encode($return);
        return $data;
    }
    /**
     * Frequency Schedule description
     * @return external_description
     */
    public static function frequency_schedule_returns() {
        return new external_value(PARAM_RAW, 'data');
    }
    /**
     * Report Object paramerters description
     * @return external_function_parameters
     */
    public static function reportobject_parameters() {
        return new external_function_parameters(
            [
                'reportid' => new external_value(PARAM_INT, 'The context id for the course', VALUE_DEFAULT),
            ]
        );
    }
    /**
     * Report Object
     * @param int $reportid Report ID
     */
    public static function reportobject($reportid) {
        global $DB, $CFG;

        self::validate_parameters(self::reportobject_parameters(), ['reportid' => $reportid]);

        $context = context_system::instance();
        self::validate_context($context);
        require_capability('block/learnerscript:managereports', $context);

        if (!$report = $DB->get_record('block_learnerscript', ['id' => $reportid])) {
            throw new moodle_exception('reportdoesnotexists', 'block_learnerscript');
        }
        require_once($CFG->dirroot . '/blocks/learnerscript/reports/' . $report->type . '/report.class.php');
        $reportclassname = 'block_learnerscript\lsreports\report_' . $report->type;
        $reportclass = new $reportclassname($report);
        $reportclass->create_report();
        $return = (new ls)->cr_unserialize($reportclass->config->components);
        $data = json_encode($return);
        return $data;
    }
    /**
     * Report Object description
     * @return external_description
     */
    public static function reportobject_returns() {
        return new external_value(PARAM_RAW, 'data');
    }
    /**
     * [Delete Component description]
     * @return external_function_parameters
     */
    public static function deletecomponenet_parameters() {
        return new external_function_parameters(
            [
                'reportid' => new external_value(PARAM_INT, 'ReportID', VALUE_DEFAULT),
                'action' => new external_value(PARAM_TEXT, 'The context id for the course', VALUE_DEFAULT),
                'comp' => new external_value(PARAM_RAW, 'The context id for the course', VALUE_DEFAULT),
                'pname' => new external_value(PARAM_RAW, 'The context id for the course', VALUE_DEFAULT),
                'cid' => new external_value(PARAM_RAW, 'The context id for the course', VALUE_DEFAULT),
                'delete' => new external_value(PARAM_INT, 'Confirm Delete', VALUE_DEFAULT),
            ]
        );

    }
    /**
     * Delete report graph component
     * @param int $reportid Report ID
     * @param int $action Action
     * @param string $comp Graph component
     * @param string $pname Plot name
     * @param int $cid Graph element ID
     * @param int $delete Delete graph
     */
    public static function deletecomponenet($reportid, $action, $comp, $pname, $cid, $delete) {
        global $DB;
        if (!$report = $DB->get_record('block_learnerscript', ['id' => $reportid])) {
            throw new moodle_exception('reportdoesnotexists', 'block_learnerscript');
        }

        // We always must pass webservice params through validate_parameters.
        self::validate_parameters(self::deletecomponenet_parameters(), ['reportid' => $reportid,
        'action' => $action, 'comp' => $comp, 'pname' => $pname, 'cid' => $cid, 'delete' => $delete, ]);

        $context = context_system::instance();
        self::validate_context($context);
        require_capability('block/learnerscript:managereports', $context);

        $components = (new ls)->cr_unserialize($report->components);
        $elements = isset($components[$comp]['elements']) ? $components[$comp]['elements'] : [];
        if (count($elements) == 1 && $report->disabletable == 1) {
            $success['success'] = true;
            $success['disabledelete'] = true;
        } else {
            foreach ($elements as $index => $e) {
                if ($e['id'] == $cid) {
                    if ($delete) {
                        unset($elements[$index]);
                        break;
                    }
                    $moveup = '';
                    $newindex = ($moveup) ? $index - 1 : $index + 1;
                    $tmp = $elements[$newindex];
                    $elements[$newindex] = $e;
                    $elements[$index] = $tmp;
                    break;
                }
            }
            $components[$comp]['elements'] = $elements;
            $report->components = (new ls)->cr_serialize($components);
            try {
                $DB->update_record('block_learnerscript', $report);
                $success['success'] = true;
                $success['disabledelete'] = false;
            } catch (exception $e) {
                $success['success'] = false;
                $success['disabledelete'] = false;
            }
        }
        return $success;
    }
    /**
     * Delete component
     * @return external_description
     */
    public static function deletecomponenet_returns() {
        return new external_single_structure(
            [
                'success' => new external_value(PARAM_RAW, 'success message'),
                'disabledelete' => new external_value(PARAM_RAW, 'message'),
            ]
        );
    }
    /**
     * Report Filter Form ajax
     */
    public static function reportfilterform_is_allowed_from_ajax() {
        return true;
    }
    /**
     * Report Filter Form parameters description
     * @return external_function_parameters
     */
    public static function reportfilterform_parameters() {
        return new external_function_parameters(
            [
                'action' => new external_value(PARAM_TEXT, 'The context id for the course', VALUE_DEFAULT),
                'reportid' => new external_value(PARAM_INT, 'ReportID', VALUE_DEFAULT),
                'instance' => new external_value(PARAM_INT, 'instanceID', VALUE_DEFAULT),
            ]
        );

    }
    /**
     * Report Filter Form
     * @param int $action Action
     * @param object $reportid Report ID
     * @param int $instance Report instance
     */
    public static function reportfilterform($action, $reportid, $instance) {
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('block/learnerscript:viewreports', $context);

        $pagevariables = get_pagevariables();
        $pagevariables->set_context($context);
        // We always must pass webservice params through validate_parameters.
        self::validate_parameters(self::reportfilterform_parameters(), ['action' => $action,
        'reportid' => $reportid, 'instance' => $instance, ]);

        $reportrecord = new reportbase($reportid);
        $reportrecord->customheader = true; // For not to display Form Header.
        $reportrecord->instanceid = $instance;
        $filterform = new block_learnerscript\form\filter_form(null, $reportrecord);
        $reportfilterform = $filterform->render();
        return $reportfilterform;
    }
    /**
     * Report Filter Form
     * @return external_description
     */
    public static function reportfilterform_returns() {
        return new external_value(PARAM_RAW, 'reportfilterform');
    }
    /**
     * Import Report parameters description
     * @return external_function_parameters
     */
    public static function importreports_parameters() {
        return new external_function_parameters(
            [
                'total' => new external_value(PARAM_INT, 'Total reports', VALUE_DEFAULT, 0),
                'current' => new external_value(PARAM_INT, 'Current Report Position', VALUE_DEFAULT, 0),
                'errorreportspositiondata' => new external_value(PARAM_RAW, 'error report positions', VALUE_DEFAULT, 0),
                'lastreportposition' => new external_value(PARAM_INT, 'Last Report Position', VALUE_DEFAULT, 0),
            ]
        );
    }
    /**
     * Import Reports description
     * @param int $total Total reports count
     * @param int $current Report position
     * @param int $errorreportspositiondata Error in report position data
     * @param int $lastreportposition Last report position
     */
    public static function importreports($total, $current, $errorreportspositiondata, $lastreportposition = 0) {
        global $CFG, $DB;
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('block/learnerscript:managereports', $context);

        // We always must pass webservice params through validate_parameters.
        self::validate_parameters(self::importreports_parameters(),
        ['total' => $total, 'current' => $current, 'errorreportspositiondata' => $errorreportspositiondata,
        'lastreportposition' => $lastreportposition, ]);

        $path = $CFG->dirroot . '/blocks/learnerscript/reportsbackup/';
        $learnerscriptreports = glob($path . '*.xml');
        $course = get_course(SITEID);
        if ($lastreportposition > 0) {
            $errorreportsposition = unserialize($errorreportspositiondata);
            foreach ($learnerscriptreports as $k => $learnerscriptreport) {
                if ((!empty($errorreportsposition) && in_array($k, $errorreportsposition)) || $k >= $lastreportposition) {
                    $finalreports[$k] = $learnerscriptreport;
                }
            }

            $position = $current;
            $importurl = $finalreports[$position];
            $data = [];
            if (file_exists($finalreports[$position])
                && pathinfo($finalreports[$position], PATHINFO_EXTENSION) == 'xml') {
                $filedata = file_get_contents($importurl);
                $status = (new ls)->cr_import_xml($filedata, $course, false, true);
                if ($status) {
                    $data['import'] = true;
                } else {
                    $data['import'] = false;
                }
                $event = \block_learnerscript\event\import_report::create([
                    'objectid' => $position,
                    'context' => $context,
                    'other' => ['reportid' => $status,
                                     'status' => $data['import'],
                                     'position' => $position, ],
                    ], );
                $event->trigger();
                $currentposition = array_search($position, array_keys($finalreports));
                $nextposition = $currentposition + 1;
                $percent = $nextposition / $total * 100;
                $data['percent'] = round($percent, 0);
                $data['current'] = array_keys($finalreports)[$nextposition];
            }
        } else {
            $position = $current - 1;
            $finalreports = $learnerscriptreports;
            $importurl = $finalreports[$position];
            $data = [];
            if (file_exists($finalreports[$position])
                && pathinfo($finalreports[$position], PATHINFO_EXTENSION) == 'xml') {
                $filedata = file_get_contents($importurl);
                $status = (new ls)->cr_import_xml($filedata, $course, false, true);
                if ($status) {
                    $data['import'] = true;
                } else {
                    $data['import'] = false;
                }
                $event = \block_learnerscript\event\import_report::create([
                    'objectid' => $position,
                    'context' => $context,
                    'other' => ['reportid' => $status,
                                     'status' => $data['import'],
                                     'position' => $position, ],
                ]);
                $event->trigger();

                $percent = $current / $total * 100;
                $data['percent'] = round($percent, 0);
            }
        }

        $pluginsettings = new lssetting('block_learnerscript/lsreportconfigstatus',
                'lsreportconfigstatus', get_string('lsreportconfigstatus', 'block_learnerscript'), '', PARAM_BOOL, 2);
        $totallsreports = $DB->count_records('block_learnerscript');
        if (count($learnerscriptreports) <= $totallsreports) {
            $pluginsettings->config_write('lsreportconfigstatus', true);
        } else {
            $pluginsettings->config_write('lsreportconfigstatus', false);
        }
        $data = json_encode($data);
        return $data;
    }
    /**
     * Import Reports
     * @return external_description
     */
    public static function importreports_returns() {
        return new external_value(PARAM_RAW, 'data');
    }

    /**
     * Learnerscript reports configuration import params
     * @return external_function_parameters
     */
    public static function lsreportconfigimport_parameters() {
        return new external_function_parameters(
            []
        );
    }
    /**
     * Learnerscript reports configuration import
     */
    public static function lsreportconfigimport() {

        $context = context_system::instance();
        self::validate_context($context);
        require_capability('block/learnerscript:managereports', $context);

        $pluginsettings = new lssetting('block_learnerscript/lsreportconfigimport',
                    'lsreportconfigimport', get_string('lsreportconfigimport', 'block_learnerscript'), '', PARAM_INT, 2);
        $return = $pluginsettings->config_write('lsreportconfigimport', 0);
        $data = json_encode($return);
        return $data;
    }

    /**
     * Learnerscript reports configuration import params
     * @return external_description
     */
    public static function lsreportconfigimport_returns() {
        return new external_value(PARAM_RAW, 'data');
    }
    /**
     * Filter Courses parameters description
     * @return external_function_parameters
     */
    public static function filter_courses_parameters() {
        return new external_function_parameters(
            [
                'action' => new external_value(PARAM_TEXT, 'action', VALUE_DEFAULT),
                'maximumselectionlength' => new external_value(PARAM_INT, 'maximum selection length to search', VALUE_DEFAULT),
                'term' => new external_value(PARAM_TEXT, 'Current search term in search box', VALUE_DEFAULT),
                '_type' => new external_value(PARAM_RAW, 'A "request type" will be usually a query', VALUE_DEFAULT),
                'fiterdata' => new external_value(PARAM_RAW, 'fiterdata', VALUE_DEFAULT),
                'basicparamdata' => new external_value(PARAM_RAW, 'basicparamdata', VALUE_DEFAULT),
                'reportinstanceid' => new external_value(PARAM_INT, 'reportid', VALUE_DEFAULT),
                'courses' => new external_value(PARAM_RAW, 'Course id of report', VALUE_DEFAULT),
            ]
        );
    }
    /**
     * Filter Courses description
     * @param int $action Action
     * @param int $maximumselectionlength Maximum selection length of coursename
     * @param boolean $term Search text
     * @param boolean $type Text type
     * @param string $fiterdata Reports filter data
     * @param string $basicparamdata Mandatory filters data
     * @param int $reportinstanceid Report instance ID
     * @param int $courses Courses list
     */
    public static function filter_courses($action, $maximumselectionlength,
    $term, $type, $fiterdata, $basicparamdata, $reportinstanceid, $courses) {
        global $DB, $CFG;
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('block/learnerscript:viewreports', $context);

        $pagevariables = get_pagevariables();
        $pagevariables->set_context($context);
        $search = $term;

        // We always must pass webservice params through validate_parameters.
        self::validate_parameters(self::filter_courses_parameters(), ['action' => $action,
        'maximumselectionlength' => $maximumselectionlength, 'term' => $term, '_type' => $type,
        'fiterdata' => $fiterdata, 'basicparamdata' => $basicparamdata,
        'reportinstanceid' => $reportinstanceid, 'courses' => $courses, ]);

        $filters = json_decode($fiterdata, true);
        $basicparams = json_decode($basicparamdata, true);
        $filterdata = array_merge($filters, $basicparams);
        $report = $DB->get_record('block_learnerscript', ['id' => $reportinstanceid]);
        $reportclass = new stdClass();
        if (!empty($report) && $report->type) {
            require_once($CFG->dirroot . '/blocks/learnerscript/reports/' . $report->type . '/report.class.php');
            $reportclassname = 'block_learnerscript\lsreports\report_' . $report->type;
            $properties = new stdClass;
            $reportclass = new $reportclassname($report, $properties);
        }
        $pluginclass = new stdClass();
        $pluginclass->report = new stdClass();
        $pluginclass->report->type = 'custom';
        $pluginclass->reportclass = $reportclass;
        $courseoptions = (new \block_learnerscript\local\querylib)->filter_get_courses($pluginclass, $courses, true, $search,
        $filterdata, $type, false);
        $termsdata = [];
        $termsdata['total_count'] = count($courseoptions);
        $termsdata['incomplete_results'] = false;
        $termsdata['items'] = $courseoptions;
        $return = $termsdata;
        $data = json_encode($return);
        return $data;
    }
    /**
     * Filter Courses
     * @return external_description
     */
    public static function filter_courses_returns() {
        return new external_value(PARAM_RAW, 'data');
    }
    /**
     * Filter users parameters description
     * @return external_function_parameters
     */
    public static function filterusers_parameters() {
        return new external_function_parameters(
            [
                'action' => new external_value(PARAM_TEXT, 'action', VALUE_DEFAULT),
                'maximumselectionlength' => new external_value(PARAM_INT, 'maximum selection length to search', VALUE_DEFAULT),
                'term' => new external_value(PARAM_TEXT, 'Current search term in search box', VALUE_DEFAULT),
                '_type' => new external_value(PARAM_RAW, 'A "request type" will be usually a query', VALUE_DEFAULT),
                'fiterdata' => new external_value(PARAM_RAW, 'fiterdata', VALUE_DEFAULT),
                'basicparamdata' => new external_value(PARAM_RAW, 'basicparamdata', VALUE_DEFAULT),
                'reportinstanceid' => new external_value(PARAM_INT, 'reportinstanceid', VALUE_DEFAULT),
                'courses' => new external_value(PARAM_INT, 'Course id of report', VALUE_DEFAULT),
            ]
        );
    }
    /**
     * Filter users description
     * @param int $action Action
     * @param int $maximumselectionlength Maximum selection length of coursename
     * @param boolean $term Search text
     * @param boolean $type Text type
     * @param string $fiterdata Reports filter data
     * @param string $basicparamdata Mandatory filters data
     * @param int $reportinstanceid Report instance ID
     * @param int $courses Courses list
     */
    public static function filterusers($action, $maximumselectionlength, $term, $type,
    $fiterdata, $basicparamdata, $reportinstanceid, $courses) {
        global $DB, $CFG;
        $context = context_system::instance();
        self::validate_context($context);
        require_capability('block/learnerscript:viewreports', $context);

        $pagevariables = get_pagevariables();
        $pagevariables->set_context($context);
        $search = $term;

        // We always must pass webservice params through validate_parameters.
        self::validate_parameters(self::filterusers_parameters(), ['action' => $action,
        'maximumselectionlength' => $maximumselectionlength, 'term' => $term, '_type' => $type,
        'fiterdata' => $fiterdata, 'basicparamdata' => $basicparamdata,
        'reportinstanceid' => $reportinstanceid, 'courses' => $courses, ]);

        $filters = json_decode($fiterdata, true);
        $basicparams = json_decode($basicparamdata, true);

        $filterdata = array_merge($filters, $basicparams);

        $report = $DB->get_record('block_learnerscript', ['id' => $reportinstanceid]);
        $reportclass = new stdClass();
        if (!empty($report) && $report->type) {
            require_once($CFG->dirroot . '/blocks/learnerscript/reports/' . $report->type . '/report.class.php');
            $reportclassname = 'block_learnerscript\lsreports\report_' . $report->type;
            $properties = new stdClass;
            $reportclass = new $reportclassname($report, $properties);
        }
        $pluginclass = new stdClass();
        $pluginclass->report = new stdClass();
        $pluginclass->report->type = 'custom';
        $pluginclass->reportclass = $reportclass;
        $courseoptions = (new \block_learnerscript\local\querylib)->filter_get_users($pluginclass,
        true, $search, $filterdata, SITEID, $type, $courses);
        $termsdata = [];
        $termsdata['total_count'] = count($courseoptions);
        $termsdata['incomplete_results'] = false;
        $termsdata['items'] = $courseoptions;
        $return = $termsdata;
        $data = json_encode($return);
        return $data;
    }
    /**
     * Filter Users
     * @return external_description
     */
    public static function filterusers_returns() {
        return new external_value(PARAM_RAW, 'data');
    }
}
