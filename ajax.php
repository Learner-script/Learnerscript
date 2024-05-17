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

$requests = json_decode($rawjson, true);

foreach ($requests as $key => $val) {
    if (strpos($key, 'filter_') !== false) {
        $_POST[$key] = $val;
    }
}
if (empty($requests['scheduleid'])) {
    $scheduleid = 0;
} else {
    $scheduleid = $requests['scheduleid'];
}
if (empty($requests['selectedroleid'])) {
    $selectedroleid = '';
} else {
    $selectedroleid = $requests['selectedroleid'];
}
if (empty($requests['roleid'])) {
    $roles = 0;
} else {
    $roles = $requests['roleid'];
}
if (empty($requests['term'])) {
    $search = '';
} else {
    $search = $requests['term'];
}
if (empty($requests['type'])) {
    $type = '';
} else {
    $type = $requests['type'];
}
if (empty($requests['schuserslist'])) {
    $schuserslist = '';
} else {
    $schuserslist = $requests['schuserslist'];
}
if (empty($requests['bullkselectedusers'])) {
    $bullkselectedusers = '';
} else {
    $bullkselectedusers = $requests['bullkselectedusers'];
}
if (empty($requests['validdate'])) {
    $validdate = 0;
} else {
    $validdate = $requests['validdate'];
}
if (empty($requests['page'])) {
    $page = 0;
} else {
    $page = $requests['page'];
}
if (empty($requests['start'])) {
    $start = 0;
} else {
    $start = $requests['start'];
}
if (empty($requests['length'])) {
    $length = 0;
} else {
    $length = $requests['length'];
}
if (empty($requests['courseid'])) {
    $courseid = 0;
} else {
    $courseid = $requests['courseid'];
}
if (empty($requests['frequency'])) {
    $frequency = 0;
} else {
    $frequency = $requests['frequency'];
}
if (empty($requests['instance'])) {
    $instance = 0;
} else {
    $instance = $requests['instance'];
}
if (empty($requests['cmid'])) {
    $cmid = 0;
} else {
    $cmid = $requests['cmid'];
}
if (empty($requests['status'])) {
    $status = '';
} else {
    $status = $requests['status'];
}
if (empty($requests['userid'])) {
    $userid = 0;
} else {
    $userid = $requests['userid'];
}
if (empty($requests['components'])) {
    $components = '';
} else {
    $components = $requests['components'];
}
if (empty($requests['component'])) {
    $component = '';
} else {
    $component = $requests['component'];
}
if (empty($requests['pname'])) {
    $pname = '';
} else {
    $pname = $requests['pname'];
}
if (empty($requests['jsonformdata'])) {
    $jsonformdata = '';
} else {
    $jsonformdata = $requests['jsonformdata'];
}
if (empty($requests['export'])) {
    $export = '';
} else {
    $export = $requests['export'];
}
if (empty($requests['lsfstartdate'])) {
    $lsfstartdate = 0;
} else {
    $lsfstartdate = $requests['lsfstartdate'];
}
if (empty($requests['lsfenddate'])) {
    $lsfenddate = 0;
} else {
    $lsfenddate = $requests['lsfenddate'];
}
if (empty($requests['cid'])) {
    $cid = '';
} else {
    $cid = $requests['cid'];
}
if (empty($requests['reporttype'])) {
    $reporttype = '';
} else {
    $reporttype = $requests['reporttype'];
}
if (empty($requests['categoryid'])) {
    $categoryid = 0;
} else {
    $categoryid = $requests['categoryid'];
}
if (empty($requests['filters'])) {
    $filters = '';
} else {
    $filters = $requests['filters'];
}
if (empty($requests['basicparams'])) {
    $basicparams = '';
} else {
    $basicparams = $requests['basicparams'];
}
if (empty($requests['elementsorder'])) {
    $elementsorder = '';
} else {
    $elementsorder = $requests['elementsorder'];
}
if (empty($requests['contextlevel'])) {
    $contextlevel = 0;
} else {
    $contextlevel = $requests['contextlevel'];
}
if (empty($requests['reportid'])) {
    $reportid = 0;
} else {
    $reportid = $requests['reportid'];
}
$action = $requests['action'];
$reportid = optional_param('reportid', $reportid, PARAM_INT);
$scheduleid = optional_param('scheduleid', $scheduleid, PARAM_INT);
$selectedroleid = optional_param('selectedroleid', $selectedroleid, PARAM_RAW);
$roles = optional_param('roleid', $roles, PARAM_RAW);
$search = optional_param('search', $search, PARAM_TEXT);
$type = optional_param('type', $type, PARAM_TEXT);
$schuserslist = optional_param('schuserslist', $schuserslist, PARAM_RAW);
$bullkselectedusers = optional_param('bullkselectedusers', $bullkselectedusers, PARAM_RAW);
$expireddate = optional_param('validdate', $validdate, PARAM_RAW);
$page = optional_param('page', $page, PARAM_INT);
$start = optional_param('start', $start, PARAM_INT);
$length = optional_param('length', $length, PARAM_INT);
$courseid = optional_param('courseid', $courseid, PARAM_INT);
$frequency = optional_param('frequency', $frequency, PARAM_INT);
$instance = optional_param('instance', $instance, PARAM_INT);
$cmid = optional_param('cmid', $cmid, PARAM_INT);
$status = optional_param('status', $status, PARAM_TEXT);
$userid = optional_param('userid', $userid, PARAM_INT);
$components = optional_param('components', $components, PARAM_RAW);
$component = optional_param('component', $component, PARAM_RAW);
$pname = optional_param('pname', $pname, PARAM_RAW);
$jsonformdata = optional_param('jsonformdata', $jsonformdata, PARAM_RAW);
$conditionsdata = optional_param('conditions', $conditionsdata, PARAM_RAW);
$export = optional_param('export', $export, PARAM_RAW);
$lsfstartdate = optional_param('lsfstartdate', $lsfstartdate, PARAM_INT);
$lsfenddate = optional_param('lsfenddate', $lsfenddate, PARAM_INT);
$cid = optional_param('cid', $cid, PARAM_RAW);
$reporttype = optional_param('reporttype', $reporttype, PARAM_RAW);
$categoryid = optional_param('categoryid', $categoryid, PARAM_INT);
$filters = optional_param('filters', $filters, PARAM_RAW);
$filters = json_decode($filters, true);
$basicparams = optional_param('basicparams', $basicparams, PARAM_RAW);
$basicparams = json_decode($basicparams, true);
$elementsorder = optional_param('elementsorder', $elementsorder, PARAM_RAW);
$contextlevel = optional_param('contextlevel', $contextlevel, PARAM_INT);

$context = context_system::instance();
$ls = new ls();
require_login();
$PAGE->set_context($context);

$scheduling = new schedule();
$learnerscript = $PAGE->get_renderer('block_learnerscript');

switch ($action) {
    case 'rolewiseusers':
        if ((has_capability('block/learnerscript:managereports', $context)
        || has_capability('block/learnerscript:manageownreports', $context)
        || is_siteadmin()) && !empty($roles)) {
            $userlist = $scheduling->rolewiseusers($roles, $search, 0, 0, $contextlevel);
            $termsdata = [];
            $termsdata['page'] = $page;
            $termsdata['search'] = $search;
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
            $return = $termsdata;
        }
    break;
    case 'viewschuserstable':
        if ((has_capability('block/learnerscript:managereports', $context)
        || has_capability('block/learnerscript:manageownreports', $context)
        || is_siteadmin()) && !empty($schuserslist)) {
            $stable = new stdClass();
            $stable->table = true;
            $return = $learnerscript->viewschusers($reportid, $scheduleid, $schuserslist, $stable);
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
        break;
    case 'schreportform':
        $args = new stdClass();
        $args->reportid = $reportid;
        $args->instance = $instance;
        $args->jsonformdata = $jsonformdata;
        $return = block_learnerscript_schreportform_ajaxform($args);
        break;
    case 'scheduledtimings':
        if ((has_capability('block/learnerscript:managereports', $context)
        || has_capability('block/learnerscript:manageownreports', $context)
        || is_siteadmin()) && !empty($reportid)) {
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
        break;
    case 'generate_plotgraph':
        if (!$report = $DB->get_record('block_learnerscript', ['id' => $reportid])) {
            throw new moodle_exception('reportdoesnotexists', 'block_learnerscript');
        }
        require_once($CFG->dirroot . '/blocks/learnerscript/reports/' . $report->type . '/report.class.php');
        $reportclassname = 'block_learnerscript\lsreports\report_' . $report->type;
        $properties = new stdClass();
        $properties->cmid = $cmid;
        $properties->courseid = $courseid;
        $properties->userid = $userid;
        $properties->status = $status;
        if (!empty($lsfstartdate)) {
            $properties->lsstartdate = $lsfstartdate;
        } else {
            $properties->lsstartdate = 0;
        }

        if (!empty($lsenddate)) {
            $properties->lsenddate = $lsfenddate;
        } else {
            $properties->lsenddate = time();
        }
        $reportclass = new $reportclassname($report, $properties);

        $reportclass->create_report();
        $components = $ls->cr_unserialize($reportclass->config->components);
        if ($reporttype == 'table') {
            $datacolumns = [];
            $columndefs = [];
            $i = 0;
            foreach ($reportclass->finalreport->table->head as $key => $value) {
                $datacolumns[]['data'] = $value;
                $columndef = new stdClass();
                $align = $reportclass->finalreport->table->align[$i] ? $reportclass->finalreport->table->align[$i] : 'left';
                $wrap = ($reportclass->finalreport->table->wrap[$i] == 'wrap') ? 'break-all' : 'normal';
                $width = ($reportclass->finalreport->table->size[$i]) ? $reportclass->finalreport->table->size[$i] : '';
                $columndef->className = 'dt-body-' . $align;
                $columndef->targets = [$i];
                $columndef->wrap = $wrap;
                $columndef->width = $width;
                $columndefs[] = $columndef;
                $i++;
            }
            if (!empty($reportclass->finalreport->table->head)) {
                $tablehead = $ls->report_tabledata($reportclass->finalreport->table);
                $reporttable = new \block_learnerscript\output\reporttable($reportclass,
                    $tablehead,
                    $reportclass->finalreport->table->id,
                    '',
                    $reportid,
                    $reportclass->sql,
                    $report->type,
                    false,
                    false,
                    null
                );
                $return = [];
                $return['tdata'] = $learnerscript->render($reporttable);
                $return['columnDefs'] = $columndefs;
            } else {
                $return['tdata'] = html_writer::div(get_string("nodataavailable", "block_learnerscript"),
                                    'alert alert-info', []);
            }
        } else {
            $seriesvalues = (isset($components['plot']['elements'])) ? $components['plot']['elements'] : [];
            $i = 0;
            foreach ($seriesvalues as $g) {
                if (($reporttype != '' && $g['id'] == $reporttype) || $i == 0) {
                    $return['plot'] = $ls->generate_report_plot($reportclass, $g);
                    if ($reporttype != '' && $g['id'] == $reporttype) {
                        break;
                    }
                }
                $return['plotoptions'][] = ['id' => $g['id'], 'title' => $g['formdata']->chartname,
                'pluginname' => $g['pluginname'], ];
                $i++;
            }
        }
        break;
    case 'frequency_schedule':
        $return = $scheduling->getschedule($frequency);
        break;
    case 'reportobject':
        if (!$report = $DB->get_record('block_learnerscript', ['id' => $reportid])) {
            throw new moodle_exception('reportdoesnotexists', 'block_learnerscript');
        }
        require_once($CFG->dirroot . '/blocks/learnerscript/reports/' . $report->type . '/report.class.php');
        $reportclassname = 'block_learnerscript\lsreports\report_' . $report->type;
        $properties = new stdClass();
        $reportclass = new $reportclassname($report, $properties);
        $reportclass->create_report();
        $return = $ls->cr_unserialize($reportclass->config->components);
        break;
    case 'updatereport':
        if (!$report = $DB->get_record('block_learnerscript', ['id' => $reportid])) {
            throw new moodle_exception('reportdoesnotexists', 'block_learnerscript');
        }
        require_once($CFG->dirroot . '/blocks/learnerscript/reports/' . $report->type . '/report.class.php');
        $reportclassname = 'block_learnerscript\lsreports\report_' . $report->type;
        $properties = new stdClass();
        $reportclass = new $reportclassname($report, $properties);
        $comp = (array) $ls->cr_unserialize($reportclass->config->components);
        $components = json_decode($components, true);
        $comp['columns']['elements'] = $components['columns']['elements'];
        $comp['filters']['elements'] = $components['filters']['elements'];
        $comparray = ['columns', 'filters'];
        foreach ($comparray as $c) {
            foreach ($comp[$c]['elements'] as $k => $d) {
                if ($c == 'filters') {
                    if (empty($d['formdata']['value'])) {
                        unset($comp[$c]['elements'][$k]);
                        continue;
                    }
                }
            }
        }
        $listofexports = $components['exports'];
        $exportlist = [];
        foreach ($listofexports as $key => $exportoptions) {
            if (!empty($exportoptions['value'])) {
                $exportlist[] = $exportoptions['name'];
            }
        }
        $exports = implode(',', $exportlist);
        $components = $ls->cr_serialize($comp);
        if (empty($listofexports)) {
            $DB->update_record('block_learnerscript', (object) ['id' => $reportid, 'components' => $components]);
        } else {
            $DB->update_record('block_learnerscript', (object) ['id' => $reportid,
            'components' => $components, 'export' => $exports, ]);
        }
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
    case 'usercourses':
        if ($reportid) {
            $report = $DB->get_record('block_learnerscript', ['id' => $reportid]);
        }
        if ($report->type) {
            require_once($CFG->dirroot . '/blocks/learnerscript/reports/' . $report->type . '/report.class.php');
            $reportclassname = 'block_learnerscript\lsreports\report_' . $report->type;
            $properties = new stdClass;
            $reportclass = new $reportclassname($report, $properties);
        }
        if ($userid > 0) {
            $courselist = array_keys(enrol_get_users_courses($userid));
            if (!empty($courselist)) {
                if (!empty($reportclass->rolewisecourses)) {
                    $rolecourses = explode(',', $reportclass->rolewisecourses);
                    $courselist = array_intersect($courselist, $rolecourses);
                }
                $courseids = implode(',', $courselist);
                list($coursesql, $params) = $DB->get_in_or_equal($courselist, SQL_PARAMS_NAMED);
                $params['siteid'] = SITEID;
                $params['visible'] = 1;
                $return = $DB->get_records_sql_menu("SELECT id, fullname FROM {course}
                WHERE id <> :siteid AND visible = :visible AND id $coursesql",
                $params);
            } else {
                $return = [];
            }
        } else {
            $pluginclass = new stdClass;
            $pluginclass->singleselection = true;
            $pluginclass->report->type = $report->type;
            $pluginclass->reportclass = $reportclass;
            $return = (new \block_learnerscript\local\querylib)->filter_get_courses($pluginclass, null);
        }
        break;
    case 'enrolledusers':
        if ($courseid > 0) {
            $coursecontext = context_course::instance($courseid);
            $studentroleid = $DB->get_field('role', 'id', ['shortname' => 'student']);
            $enrolledusers = array_keys(get_role_users($studentroleid, $coursecontext));
            $return = [];
            if (!empty($enrolledusers)) {
                list($usql, $params) = $DB->get_in_or_equal($enrolledusers, SQL_PARAMS_NAMED);
                $params['confirmed'] = 1;
                $params['deleted'] = 0;
                $return = $DB->get_records_sql_menu("SELECT id, CONCAT(firstname,' ',lastname) AS name
                FROM {user}
                WHERE confirmed = :confirmed AND deleted = :deleted AND id $usql",
                $params);
            }
        } else {
            if ($reportid) {
                $report = $DB->get_record('block_learnerscript', ['id' => $reportid]);
            }
            if (!empty($report->type) && $report->type) {
                require_once($CFG->dirroot . '/blocks/learnerscript/reports/' . $report->type . '/report.class.php');
                $reportclassname = 'block_learnerscript\lsreports\report_' . $report->type;
                $properties = new stdClass;
                $reportclass = new $reportclassname($report, $properties);
            }
            $pluginclass = new stdClass;
            $pluginclass->singleselection = true;
            $pluginclass->report = new stdClass;
            $pluginclass->report->type = !empty($report->type) ? $report->type : '';
            $pluginclass->report->components = $components;
            $pluginclass->reportclass = !empty($reportclass) ? $reportclass : '';
            $return = (new \block_learnerscript\local\querylib)->filter_get_users($pluginclass, false);
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
    case 'tabsposition':
        $report = $DB->get_record('block_learnerscript', ['id' => $reportid]);
        $components = $ls->cr_unserialize($report->components);
        $elements = isset($components[$component]['elements']) ? $components[$component]['elements'] : [];
        $sortedelements = explode(',', $elementsorder);
        $finalelements = [];
        foreach ($elements as $k => $element) {
            $position = array_search($element['id'], $sortedelements);
            $finalelements[$position] = $element;
        }
        ksort($finalelements);
        $components[$component]['elements'] = $finalelements;
        $finalcomponents = $ls->cr_serialize($components);
        $report->components = $finalcomponents;
        $DB->update_record('block_learnerscript', $report);
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
    case 'configureplot':
        $return = [];
        if (!$report = $DB->get_record('block_learnerscript', ['id' => $reportid])) {
            throw new moodle_exception('reportdoesnotexists', 'block_learnerscript');
        }
        require_once($CFG->dirroot . '/blocks/learnerscript/reports/' . $report->type . '/report.class.php');
        $reportclassname = 'block_learnerscript\lsreports\report_' . $report->type;

        $properties = new stdClass();
        $reportclass = new $reportclassname($report, $properties);
        $reportclass->create_report();
        $components = unserialize($reportclass->config->components);

        $return['columns'] = $components['columns'];
        $uniqueid = random_string(15);
        while (strpos($reportclass->config->components, $uniqueid) !== false) {
            $uniqueid = random_string(15);
        }
        $plot['id'] = $uniqueid;
        $plot['formdata'] = new stdClass();
        $plot['formdata']->chartname = '';
        $plot['formdata']->serieid = '';
        $plot['formdata']->yaxis[] = ['name' => '', 'operator' => '', 'value' => ''];
        $plot['formdata']->showlegend = 0;
        $plot['formdata']->datalabels = 0;
        $plot['formdata']->calcs = null;
        $plot['formdata']->columnsort = null;
        $plot['formdata']->sorting = null;
        $plot['formdata']->limit = null;

        $return['plot'] = $plot;
        break;
    case  'learnerscriptdata':
        $report = $DB->get_record('block_learnerscript', ['id' => $reportid]);
        $return = $report;
    break;
}

    $json = json_encode($return, JSON_NUMERIC_CHECK);
if ($json) {
    echo $json;
} else {
    echo json_last_error_msg();
}
