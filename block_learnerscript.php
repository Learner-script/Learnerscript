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

use block_learnerscript\local\ls;

/**
 * Learnerscript, a Moodle block to create customizable reports.
 *
 * @package    block_learnerscript
 * @copyright  2023 Moodle India Information Solutions Private Limited
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_learnerscript extends block_list {

    /**
     * Sets the block name and version number
     *
     * @return void
     * */
    public function init() {
        $this->title = get_string('pluginname', 'block_learnerscript');
    }

    /**
     * Sets the block configuration
     *
     * @return void
     * */
    public function specialization() {
        if (empty($this->config->title)) {
            $this->title = get_string('pluginname', 'block_learnerscript');
        } else {
            $this->title = $this->config->title;
        }
    }

    /**
     * Block allows each instance to be configured
     *
     * @return boolean
     * */
    public function instance_allow_config() {
        return true;
    }

    /**
     * Where to add the block
     *
     * @return array
     * */
    public function applicable_formats() {
        return ['site' => true, 'course' => true, 'my' => true];
    }

    /**
     * Global Config?
     *
     * @return boolean
     * */
    public function has_config() {
        return true;
    }

    /**
     * More than one instance per page?
     *
     * @return boolean
     * */
    public function instance_allow_multiple() {
        return false;
    }

    /**
     * Gets the contents of the block (course view)
     *
     * @return object An object with the contents
     * */
    public function get_content() {
        global $DB, $USER, $CFG, $COURSE;
        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->footer = '';
        $this->content->icons = [];

        if (!isloggedin()) {
            return $this->content;
        }

        $course = $DB->get_record('course', ['id' => $COURSE->id]);

        if (!$course) {
            throw new moodle_exception(get_string('nocourseexist', 'block_learnerscript'));
        }

        if ($course->id == SITEID) {
            $context = context_system::instance();
        } else {
            $context = context_course::instance($course->id);
        }
        $reportdashboardblockexists = $this->page->blocks->is_known_block_type('reportdashboard', false);
        if (!is_siteadmin()) {
            $userrolesql = "SELECT CONCAT(ra.roleid, '_',c.contextlevel) AS rolecontext, r.shortname, c.contextlevel
            FROM {role_assignments} ra
            JOIN {context} c ON c.id = ra.contextid
            JOIN {role} r ON r.id = ra.roleid
            WHERE 1 = 1 AND ra.userid = :userid AND (";
            foreach ($USER->access['ra'] as $key => $value) {
                $userrolesql .= " c.path LIKE '".$key."' OR ";
            }
            $userrolesql .= " 1 = 1) GROUP BY ra.roleid, c.contextlevel, r.shortname ";
            $userroles = $DB->get_record_sql($userrolesql, ['userid' => $USER->id], IGNORE_MULTIPLE);
            if (!empty($userroles)) {
                $roleshortname = $userroles->shortname;
                if ($roleshortname == 'editingteacher' && $userroles->contextlevel == 10) {
                    $rolecontextlevel = 50;
                } else {
                    $rolecontextlevel = $userroles->contextlevel;
                }
            } else {
                $roleshortname = 0;
                $rolecontextlevel = 0;
            }
        }
        if ($reportdashboardblockexists) {
            if (!is_siteadmin()) {
                if ($roleshortname == 'student') {
                    $this->content->items[] = html_writer::link(new moodle_url($CFG->wwwroot .
                    '/blocks/reportdashboard/profilepage.php',
                    ['filter_users' => $USER->id, 'role' => $roleshortname, 'contextlevel' => $rolecontextlevel]),
                    get_string('pluginname', 'block_learnerscript'), ['class' => 'ls-block_reportdashboard']);
                } else {
                    $this->content->items[] = html_writer::link(new moodle_url($CFG->wwwroot .
                    '/blocks/reportdashboard/dashboard.php',
                    ['role' => $roleshortname, 'contextlevel' => $rolecontextlevel]),
                    get_string('pluginname', 'block_learnerscript'), ['class' => 'ls-block_reportdashboard']);
                }

            } else {
                $this->content->items[] = html_writer::link(new moodle_url($CFG->wwwroot .
                '/blocks/reportdashboard/dashboard.php', []), get_string('pluginname', 'block_learnerscript'),
                ['class' => 'ls-block_reportdashboard']);
            }
        }
        // Site (Shared) reports.
        if (!empty($this->config->displayglobalreports)) {
            $reports = $DB->get_records('block_learnerscript', ['global' => 1], 'name ASC');

            if ($reports) {
                foreach ($reports as $report) {
                    if ($report->visible && (new ls)->cr_check_report_permissions($report,
                                                    $USER->id, $context)) {
                        $rname = format_string($report->name);

                        $this->content->items[] =
                        html_writer::link(new moodle_url($CFG->wwwroot . '/blocks/learnerscript/viewreport.php',
                        ['id' => $report->id, 'courseid' => $course->id, "alt" => $rname]), $rname,
                        ['class' => 'ls-block_reportlist_reportname']);
                    }
                }
                if (!empty($this->content->items)) {
                    $this->content->items[] = '========';
                }
            }
        }

        $reports = $DB->get_records('block_learnerscript', ['courseid' => $course->id], 'name ASC');

        if ($reports) {
            foreach ($reports as $report) {
                if (!$report->global && $report->visible && (new ls)->cr_check_report_permissions($report, $USER->id, $context)) {
                    $rname = format_string($report->name);
                    $this->content->items[] =
                    html_writer::link(new moodle_url($CFG->wwwroot . '/blocks/learnerscript/viewreport.php',
                    ['id' => $report->id, 'courseid' => $course->id, "alt" => $rname]), $rname,
                    ['class' => 'ls-block_reportlist_reportname']);;
                }
            }
        }

        if (has_capability('block/learnerscript:managereports', $context) ||
            has_capability('block/learnerscript:manageownreports', $context)) {
            if (is_siteadmin()) {
                $this->content->items[] =
                html_writer::link(new moodle_url($CFG->wwwroot . '/blocks/learnerscript/managereport.php',
                        []), get_string('managereports', 'block_learnerscript'),
                        ['class' => 'ls-block_managereports']);
            } else {
                $this->content->items[] = html_writer::link(new moodle_url($CFG->wwwroot . '/blocks/learnerscript/managereport.php',
                ['role' => $roleshortname, 'contextlevel' => $rolecontextlevel]),
                get_string('managereports', 'block_learnerscript'), ['class' => 'ls-block_managereports']);
            }
        }

        if (!has_capability('block/learnerscript:managereports', $context) ||
            !has_capability('block/learnerscript:manageownreports', $context)) {
            $this->content->items[] = html_writer::link(new moodle_url($CFG->wwwroot . '/blocks/learnerscript/reports.php',
            ['role' => $roleshortname, 'contextlevel' => $rolecontextlevel]),
            get_string('managereports', 'block_learnerscript'), ['class' => 'ls-block_managereports']);
        }

        return $this->content;
    }
}
