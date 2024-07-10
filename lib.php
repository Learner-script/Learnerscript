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
 * The interface library of Learnerscript
 *
 * @package    block_learnerscript
 * @copyright  2023 Moodle India Information Solutions Private Limited
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use block_learnerscript\local\ls;
use block_learnerscript\local\schedule;
/**
 * Learnerscript block plugin file
 * @param  object $course        Course data
 * @param  object $cm            Course module data
 * @param  object $context       Context data
 * @param  string $filearea      File filter area
 * @param  array $args          Plugin file arguments
 * @param  boolean $forcedownload Force download
 * @param  array  $options       File options
 */
function block_learnerscript_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = []) {
    if ($filearea == 'logo') {
        $itemid = (int) array_shift($args);

        if ($itemid > 0) {
            return false;
        }
        $fs = get_file_storage();
        $filename = array_pop($args);
        if (empty($args)) {
            $filepath = '/';
        } else {
            $filepath = '/' . implode('/', $args) . '/';
        }

        $file = $fs->get_file($context->id, 'block_learnerscript', $filearea, $itemid, $filepath, $filename);

        if (!$file) {
            return false;
        }
        $filedata = $file->resize_image(200, 200);
        \core\session\manager::write_close();
        send_stored_file($file, null, 0, 1);
    }

    send_file_not_found();
}
/**
 * PDF export report header image path
 * @param  boolean $excel Report header
 * @return string
 */
function block_learnerscript_get_reportheader_imagepath($excel = false) {
    global $CFG;
    $fs = get_file_storage();
    $syscontext = context_system::instance();
    $reportheaderimagepath = '';
    // Now get the full list of stamp files for this instance.
    if ($files = $fs->get_area_files($syscontext->id, 'block_learnerscript', 'logo', 0,
        'filename', false)) {
        foreach ($files as $file) {
            $filename = $file->get_filename();
            if ($filename !== '.') {
                if ($excel) {
                    $reportheaderimagepath = new moodle_url('/blocks/learnerscript/pix/logo.jpg');
                } else {
                    $url = moodle_url::make_pluginfile_url($syscontext->id,
                        'block_learnerscript', 'logo', 0, '/', $file->get_filename(), false);
                    $reportheaderimagepath = $url->out();
                }
            }
        }
    } else {
         $reportheaderimagepath = new moodle_url('/blocks/learnerscript/pix/logo.jpg');
    }
    return $reportheaderimagepath;
}

/**
 * Learnerscript reports schedule form
 * @param array $args Schedule form arguments
 */
function block_learnerscript_schreportform_ajaxform($args) {
    global $CFG, $DB, $OUTPUT, $PAGE, $USER;
    $args = (object) $args;
    $o = '';
    $context = context_system::instance();
    $reportid = $args->reportid;
    $instance = $args->instance;
    $scheduleid = 0;
    $ajaxformdata = [];
    if (!empty($args->jsonformdata)) {
        parse_str($args->jsonformdata, $ajaxformdata);
        if (!empty($ajaxformdata)) {
            if (isset($ajaxformdata['users_data'])
            && !is_array($ajaxformdata['users_data'])) {
                $ajaxformdata['schuserslist'] = $ajaxformdata['users_data'];
            }
        }
    }

    if ((has_capability('block/learnerscript:managereports', $context) ||
        has_capability('block/learnerscript:manageownreports', $context) ||
        is_siteadmin()) && !empty($reportid)) {
        require_once($CFG->dirroot . '/blocks/learnerscript/components/scheduler/schedule_form.php');
        $roleslist = (new schedule)->reportroles('', $reportid);
        $schuserslist = !empty($ajaxformdata['schuserslist']) ? $ajaxformdata['schuserslist'] : [];
        list($schusers, $schusersids) = (new schedule)->userslist($reportid, $scheduleid, $schuserslist);
        $exportoptions = (new ls)->cr_get_export_plugins();
        $frequencyselect = (new schedule)->get_options();
        if (!empty($ajaxformdata['frequency']) && $ajaxformdata['frequency']) {
            $schedulelist = (new schedule)->getschedule($ajaxformdata['frequency']);
        } else {
            $schedulelist = [null => get_string('selectall', 'block_reportdashboard')];
        }
        $scheduleurl = new moodle_url('/blocks/learnerscript/components/scheduler/schedule.php');
        $scheduleform = new scheduled_reports_form($scheduleurl, ['id' => $reportid,
                                'scheduleid' => $scheduleid, 'roles_list' => $roleslist,
                                'schusers' => $schusers, 'schusersids' => $schusersids,
                                'exportoptions' => $exportoptions,
                                'schedule_list' => $schedulelist,
                                'frequencyselect' => $frequencyselect,
                            'instance' => $instance, ], 'post', '', null, true, $ajaxformdata);
        $setdata = new stdClass();
        $setdata->schuserslist = $schusersids;
        $setdata->users_data = explode(',', $schusersids);

        $scheduleform->set_data($setdata);
        if (!empty($ajaxformdata) && $scheduleform->is_validated()) {
            // If we were passed non-empty form data we want the mform to call validation functions and show errors.
            $validated = $scheduleform->is_validated();

            $validateddata = $scheduleform->get_data();
            if ($validateddata) {
                try {
                    $fromform = new stdClass();
                    $formrole = explode('_', $ajaxformdata['role']);
                    $fromform->reportid = $ajaxformdata['reportid'];
                    $fromform->roleid = $formrole[0];
                    $fromform->sendinguserid = $ajaxformdata['schuserslist'];

                    $fromform->exportformat = $ajaxformdata['exportformat'];

                    $fromform->frequency = $ajaxformdata['frequency'];
                    $fromform->schedule = $ajaxformdata['schedule'];
                    $fromform->exporttofilesystem = $ajaxformdata['exporttofilesystem'];
                    $fromform->userid = $USER->id;
                    $fromform->nextschedule = (new schedule)->next($fromform);
                    $fromform->timemodified = time();
                    $fromform->timecreated = time();
                    if (array_key_exists(1, $formrole)) {
                        $fromform->contextlevel = $formrole[1];
                    } else {
                        $fromform->contextlevel = 10;
                    }
                    $schedule = $DB->insert_record('block_ls_schedule', $fromform);
                    $event = \block_learnerscript\event\schedule_report::create([
                                    'objectid' => $fromform->reportid,
                                    'context' => $context,
                                ]);
                    $event->trigger();
                    return ['error' => false, 'data' => $validateddata];
                } catch (dml_exception $ex) {
                    throw new moodle_exception($ex);
                }
            }
        } else {
            $output = [];

            if (!empty($ajaxformdata)) {
                $scheduleform->is_validated();
                $output['formerror'] = true;
            }

            $OUTPUT->header();
            $PAGE->start_collecting_javascript_requirements();
            ob_start();
            $scheduleform->display();
            $o .= ob_get_contents();
            ob_end_clean();

            $data = $o;

            $jsfooter = $PAGE->requires->get_end_code();
            $output['error'] = false;
            $output['html'] = $data;
            $output['javascript'] = $jsfooter;

            return $output;
        }
    }
}
/**
 * Learnerscript reports send emails
 * @param array $args Send emails arguments
 */
function block_learnerscript_sendreportemail_ajaxform($args) {
    global $CFG, $DB, $OUTPUT, $PAGE, $USER, $SESSION;

    $args = (object) $args;
    $o = '';
    $context = context_system::instance();
    $reportid = $args->reportid;
    $instance = $args->instance;
    $scheduleid = 0;
    $ajaxformdata = [];
    if (!empty($args->jsonformdata)) {
        parse_str($args->jsonformdata, $ajaxformdata);
        if (!empty($ajaxformdata)) {
            if (isset($ajaxformdata['email']) && (!is_array($ajaxformdata['email']) ||
                $ajaxformdata['email'] == '_qf__force_multiselect_submission')) {
                unset($ajaxformdata['email']);
            }
        }
    }

    if ((has_capability('block/learnerscript:managereports', $context) ||
        has_capability('block/learnerscript:manageownreports', $context) ||
        is_siteadmin()) && !empty($reportid)) {
        require_once($CFG->dirroot . '/blocks/reportdashboard/email_form.php');
        $emailform = new block_reportdashboard_emailform(new moodle_url('/blocks/reportdashboard/dashboard.php'),
        ['reportid' => $reportid,
        'AjaxForm' => true, 'instance' => $instance, 'ajaxformdata' => $ajaxformdata, ], 'post', '',
        null, true, $ajaxformdata, );

        if (!empty($ajaxformdata) && $emailform->is_validated()) {
            // If we were passed non-empty form data we want the mform to call validation functions and show errors.
            $validated = $emailform->is_validated();

            $validateddata = $emailform->get_data();
            if ($validateddata) {
                try {
                    $roleid = 0;
                    $rolecontext = 0;
                    if (!empty($SESSION->role)) {
                        $roleid = $DB->get_field('role', 'id', ['shortname' => $SESSION->role]);
                        $rolecontext = $SESSION->ls_contextlevel;
                    }
                    $data = new stdClass();
                    $userlist = implode(',', $ajaxformdata['email']);
                    $data->sendinguserid = $userlist;
                    $data->exportformat = $ajaxformdata['format'];
                    $data->frequency = -1;
                    $data->schedule = 0;
                    $data->exporttofilesystem = 1;
                    $data->reportid = $ajaxformdata['reportid'];
                    $data->timecreated = time();
                    $data->timemodified = 0;
                    $data->userid = $USER->id;
                    $data->roleid = $roleid;
                    $data->nextschedule = time();
                    $data->contextlevel = $rolecontext;
                    $insert = $DB->insert_record('block_ls_schedule', $data);
                    return ['error' => false, 'data' => $validateddata];
                } catch (dml_exception $ex) {
                    throw new moodle_exception($ex);
                }
            }
        } else {
            $output = [];

            if (!empty($ajaxformdata)) {
                $emailform->is_validated();
                $output['formerror'] = true;
            }

            $OUTPUT->header();
            $PAGE->start_collecting_javascript_requirements();
            ob_start();
            $emailform->display();
            $o .= ob_get_contents();
            ob_end_clean();

            $data = $o;

            $jsfooter = $PAGE->requires->get_end_code();
            $output['error'] = false;
            $output['html'] = $data;
            $output['javascript'] = $jsfooter;

            return $output;
        }
    }
}
