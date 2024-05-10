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
 * @copyright 2023 Moodle India Information Solutions Private Limited
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . "/blocks/learnerscript/lib.php");
/** Observer class */
class block_learnerscript_observer {
    /**
     * Store all actions about modules create/update/delete in own table.
     *
     */
    public static function ls_timestats() {
        global $COURSE, $USER, $DB;
        $reluser = \core\session\manager::is_loggedinas() ? $USER->id : null;
        if ($USER && is_siteadmin($reluser) || $reluser) {
            return true;
        }
        $pagevariables = get_pagevariables();
        $activityid = $pagevariables->context->instanceid;
        if ($pagevariables->context->contextlevel == 70 && $pagevariables->context->instanceid > 0) {
            $modulename = $DB->get_field_sql("SELECT m.name
            FROM {course_modules} cm
            JOIN {modules} m ON m.id = cm.module
            WHERE cm.id = (:activityid)
            AND cm.visible = :cmvisible
            AND cm.deletioninprogress = :deletioninprogress", ['activityid' => $activityid,
            'cmvisible' => 1, 'deletioninprogress' => 0, ]);
            if ($modulename == 'scorm' || $modulename == 'quiz') {
                return false;
            }
        }
        // Used $_SESSION to get loggedin user information to calculate the timespent.
        $insertdata = new stdClass();
        $insertdata->userid = isset($_SESSION['USER']->id) ? $_SESSION['USER']->id : 0;
        $insertdata->courseid = isset($_SESSION['courseid']) ? $_SESSION['courseid'] : SITEID;
        $insertdata->instanceid = isset($_SESSION['instanceid']) ? $_SESSION['instanceid'] : 0;
        $insertdata->activityid = isset($_SESSION['activityid']) ? $_SESSION['activityid'] : 0;
        $insertdata->timespent = isset($_COOKIE['time_timeme']) ? round($_COOKIE['time_timeme']) : '';
        $insertdata1 = new stdClass();
        $insertdata1->userid = isset($_SESSION['USER']->id) ? $_SESSION['USER']->id : 0;
        $insertdata1->courseid = isset($_SESSION['courseid']) ? $_SESSION['courseid'] : SITEID;
        $insertdata1->timespent = isset($_COOKIE['time_timeme']) ? round($_COOKIE['time_timeme']) : '';

        if (isset($_COOKIE['time_timeme']) && isset($_SESSION['pageurl_timeme']) &&
            $_COOKIE['time_timeme'] != 0) {

            $record1 = $DB->get_record('block_ls_coursetimestats',
                ['courseid' => $insertdata1->courseid,
                    'userid' => $insertdata1->userid, ], '*', IGNORE_MULTIPLE);
            if ($record1) {
                $insertdata1->id = $record1->id;
                $insertdata1->timespent += round($record1->timespent);
                $insertdata1->timemodified = time();
                $DB->update_record('block_ls_coursetimestats', $insertdata1);
            } else {
                $insertdata1->timecreated = time();
                $insertdata1->timemodified = 0;
                $DB->insert_record('block_ls_coursetimestats', $insertdata1);
            }
            if ($pagevariables->context->contextlevel == 70 && $insertdata->instanceid <> 0) {
                $record = $DB->get_record('block_ls_modtimestats', ['courseid' => $insertdata->courseid,
                                                                        'activityid' => $insertdata->activityid,
                                                                        'instanceid' => $insertdata->instanceid,
                                                                        'userid' => $insertdata->userid, ], '*', IGNORE_MULTIPLE);
                if ($record) {
                    $insertdata->id = $record->id;
                    $insertdata->timespent += round($record->timespent);
                    $insertdata->timemodified = time();
                    $DB->update_record('block_ls_modtimestats', $insertdata);
                } else {
                    $insertdata->timecreated = time();
                    $insertdata->timemodified = 0;
                    $DB->insert_record('block_ls_modtimestats', $insertdata);
                }
            }
             $_COOKIE['time_timeme'] = 0;
             unset($_COOKIE['time_timeme']);
        } else {
            $_COOKIE['time_timeme'] = 0;
            $_SESSION['pageurl_timeme'] = isset($_SERVER['REQUEST_URI']) ? parse_url($_SERVER['REQUEST_URI'])['path'] : '';
            $_SESSION['time_timeme'] = round($_COOKIE['time_timeme'], 0);

        }
        $instance = 0;
        if ($pagevariables->context->contextlevel == 70) {
            $cm = get_coursemodule_from_id('', $pagevariables->context->instanceid);
            $instance = $cm->instance;
        }
        $_SESSION['courseid'] = $COURSE->id;
        $_SESSION['pageurl_timeme'] = parse_url($_SERVER['REQUEST_URI'])['path'];
        $_SESSION['instanceid'] = $instance;
        $_SESSION['activityid'] = $pagevariables->context->instanceid;
        $pagevariables->requires->js_call_amd('block_learnerscript/track', 'timeme');
        $_COOKIE['time_timeme'] = 0;
        unset($_COOKIE['time_timeme']);
    }
}
