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
namespace block_learnerscript\local;
defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/lib/evalmath/evalmath.class.php');
use stdclass;
use block_learnerscript\form\filter_form;
use html_table;
use EvalMath;
use EvalWise;
use component_columns;
use block_learnerscript\local\ls as ls;
use context_system;
use AllowDynamicProperties;
#[AllowDynamicProperties]

/** Reportbase */
class reportbase {

    /**
     * @var [type] $id [description]
     */
    public $id = 0;

    /**
     * @var [type] $components [description]
     */
    public $components = [];

    /**
     * @var [type] $finalreport [description]
     */
    public $finalreport;

    /**
     * @var [type] $finalelements [description]
     */
    public $finalelements;

    /**
     * @var [type] $totalrecords [description]
     */
    public $totalrecords = 0;

    /**
     * @var [type] $currentuser [description]
     */
    public $currentuser = 0;

    /**
     * @var [type] $currentcourseid [description]
     */
    public $currentcourseid = 1;

    /**
     * @var [type] $starttime [description]
     */
    public $starttime = 0;

    /**
     * @var [type] $endtime [description]
     */
    public $endtime = 0;

    /**
     * @var [type] $sql [description]
     */
    public $sql = '';

    /**
     * @var [type] $designpage [description]
     */
    public $designpage = true;

    /**
     * @var [type] $tablehead [description]
     */
    public $tablehead;

    /**
     * @var [type] $ordercolumn [description]
     */
    public $ordercolumn;

    /**
     * @var [type] $sqlorder [description]
     */
    public $sqlorder;

    /**
     * @var [type] $exports [description]
     */
    public $exports = true;

    /**
     * @var [type] $start [description]
     */
    public $start = 0;

    /**
     * @var [type] $length [description]
     */
    public $length = 10;

    /**
     * @var [type] $search [description]
     */
    public $search;

    /**
     * @var [type] $courseid [description]
     */
    public $courseid;

    /**
     * @var [type] $cmid [description]
     */
    public $cmid;

    /**
     * @var [type] $userid [description]
     */
    public $userid;

    /**
     * @var [type] $status [description]
     */
    public $status;

    /**
     * @var [type] $filters [description]
     */
    public $filters;

    /**
     * @var [type] $columns [description]
     */
    public $columns;

    /**
     * @var [type] $basicparams [description]
     */
    public $basicparams;

    /**
     * @var [type] $params [description]
     */
    public $params;

    /**
     * @var [type] $filterdata [description]
     */
    public $filterdata;

    /**
     * @var [type] $role [description]
     */
    public $role;

    /**
     * @var [type] $contextlevel [description]
     */
    public $contextlevel;

    /**
     * @var [type] $parent [description]
     */
    public $parent = true;

    /**
     * @var [type] $courselevel [description]
     */
    public $courselevel = false;

    /**
     * @var [type] $conditionsenabled [description]
     */
    public $conditionsenabled = false;

    /**
     * @var [type] $reporttype [description]
     */
    public $reporttype = 'table';

    /**
     * @var [type] $scheduling [description]
     */
    public $scheduling = false;

    /**
     * @var [type] $colformat [description]
     */
    public $colformat = false;

    /**
     * @var [type] $calculations [description]
     */
    public $calculations = false;

    /**
     * @var [type] $singleplot [description]
     */
    public $singleplot;

    /**
     * @var [type] $rolewisecourses [description]
     */
    public $rolewisecourses = '';

    /**
     * @var [type] $groupcolumn [description]
     */
    public $groupcolumn;

    /**
     * @var [type] $componentdata [description]
     */
    public $componentdata;

    /**
     * @var [type] $graphcolumns [description]
     */
    private $graphcolumns;

    /**
     * @var [type] $userroles [description]
     */
    public $userroles;

    /**
     * @var [type] $selectedcolumns [description]
     */
    public $selectedcolumns;

    /**
     * @var [type] $selectedfilters [description]
     */
    public $selectedfilters;

    /**
     * @var [type] $conditionfinalelements [description]
     */
    public $conditionfinalelements = [];

    /**
     * @var [type] $config [description]
     */
    public $config;

    /**
     * @var string $lsstartdate
     */
    public $lsstartdate;

    /**
     * @var string $lsenddate
     */
    public $lsenddate;

    /**
     * @var [type] $moodleroles
     */
    public $moodleroles;

    /**
     * @var [type] $contextrole
     */
    public $contextrole;

    /**
     * @var int $instanceid
     */
    public $instanceid;

    /**
     * @var int $defaultcolumn
     */
    public $defaultcolumn;

    /**
     * Construct
     *
     * @param  object $report     Report data
     * @param  object $properties Report properties
     */
    public function __construct($report, $properties = null) {
        global $DB, $SESSION, $USER;

        if (empty($report)) {
            return false;
        }
        if (is_numeric($report)) {
            $this->config = $DB->get_record('block_learnerscript', ['id' => $report]);
        } else {
            $this->config = $report;
        }
        $this->userid = isset($properties->userid) ? $properties->userid : $USER->id;
        $this->courseid = $this->config->courseid;
        if ($USER->id == $this->userid) {
            $this->currentuser = $USER;
        } else {
            $this->currentuser = $DB->get_record('user', ['id' => $this->userid]);
        }
        if (empty($this->role)) {
            $this->role = isset($SESSION->role) ? $SESSION->role : (isset($properties->role) ? $properties->role : '');
        }
        if (empty($this->contextlevel)) {
            $this->contextlevel = isset($SESSION->ls_contextlevel) ? $SESSION->ls_contextlevel :
            (isset($properties->contextlevel) ? $properties->contextlevel : '');
        }
        $this->lsstartdate = isset($properties->lsfstartdate) ? $properties->lsfstartdate : 0;
        $this->lsenddate = isset($properties->lsenddate) ? $properties->lsenddate : time();
        $this->componentdata = (new ls)->cr_unserialize($this->config->components);
        $this->rolewisecourses = $this->rolewisecourses();
        $rolecontexts = $DB->get_records_sql("SELECT DISTINCT CONCAT(r.id, '@', rcl.id),
        r.shortname, rcl.contextlevel
        FROM {role} r
        JOIN {role_context_levels} rcl ON rcl.roleid = r.id AND rcl.contextlevel NOT IN (70)
        WHERE 1 = 1
        ORDER BY rcl.contextlevel ASC");
        foreach ($rolecontexts as $rc) {
            if ($rc->contextlevel == 10 && ($rc->shortname == 'manager')) {
                continue;
            }
            $rcontext[] = $rc->shortname .'_'.$rc->contextlevel;
        }
        $this->moodleroles = isset($SESSION->rolecontextlist) ? $SESSION->rolecontextlist : $rcontext;
        $this->contextrole = isset($SESSION->role) && isset($SESSION->ls_contextlevel)
        ? $SESSION->role . '_' . $SESSION->ls_contextlevel
        : $this->role .'_'.$this->contextlevel;
    }

    /**
     * Initialize
     */
    public function init() {

    }

    /**
     * Check report permissions
     *
     * @param  object $context    User context
     * @param  int $userid     User ID
     */
    public function check_permissions($context, $userid = null) {
        global $CFG, $USER;
        if ($userid == null) {
            $userid = $USER->id;
        }

        if (is_siteadmin($userid) || (new ls)->is_manager($userid, $this->contextlevel, $this->role)) {
            return true;
        }

        if (has_capability('block/learnerscript:managereports', $context, $userid)) {
            return true;
        }

        if (empty($this->config->visible)) {
            return false;
        }
        $permissions = (isset($this->componentdata['permissions'])) ? $this->componentdata['permissions'] : [];
        if (empty($permissions['elements'])) {
            return has_capability('block/learnerscript:viewreports', $context, $userid);
        } else {
            $i = 1;
            $cond = [];
            foreach ($permissions['elements'] as $p) {
                require_once($CFG->dirroot . '/blocks/learnerscript/components/permissions/' .
                    $p['pluginname'] . '/plugin.class.php');
                $classname = 'block_learnerscript\lsreports\plugin_' . $p['pluginname'];
                $class = new $classname($this->config);
                $class->role = $this->role;
                $class->userroles = isset($this->userroles) ? $this->userroles : '';
                $cond[$i] = $class->execute($userid, $context, $p['formdata']);
                $i++;
            }
            if (count($cond) == 1) {
                return $cond[1];
            } else {
                $m = new EvalMath;
                $orig = $dest = [];
                if (isset($permissions['config']) && isset($permissions['config']->conditionexpr)) {
                    $logic = trim($permissions['config']->conditionexpr);
                    // Security.
                    // No more than: conditions * 10 chars.
                    $logic = substr($logic, 0, count($permissions['elements']) * 10);
                    $logic = str_replace(['and', 'or'], ['&&', '||'], strtolower($logic));
                    // More Security Only allowed chars.
                    $logic = preg_replace_callback(
                            '/[^&c\d\s|()]/i',
                            function($matches) {
                                return '';
                            },
                            $logic
                        );
                    $logic = str_replace(['&&', '||'], ['*', '+'], $logic);

                    for ($j = $i - 1; $j > 0; $j--) {
                        $orig[] = 'c' . $j;
                        $dest[] = ($cond[$j]) ? 1 : 0;
                    }
                    return $m->evaluate(str_replace($orig, $dest, $logic));
                } else {
                    return false;
                }
            }
        }
    }

    /**
     * Add filter elements
     *
     * @param  object $mform Filter form
     */
    public function add_filter_elements(&$mform) {
        global $CFG;
        $filters = (isset($this->componentdata['filters'])) ? $this->componentdata['filters'] : [];
        if (!empty($filters['elements'])) {
            foreach ($filters['elements'] as $f) {
                if ($f['formdata']->value) {
                    require_once($CFG->dirroot . '/blocks/learnerscript/components/filters/' .
                        $f['pluginname'] . '/plugin.class.php');
                    $classname = 'block_learnerscript\lsreports\plugin_' . $f['pluginname'];
                    $class = new $classname($this->config);
                    $class->singleselection = true;
                    $this->finalelements = $class->print_filter($mform);
                }
            }
        }
    }

    /**
     * Initial basicparams
     *
     * @param  string $pluginname Mandatory filter plugin name
     */
    public function initial_basicparams($pluginname) {
        global $CFG;
         require_once($CFG->dirroot . '/blocks/learnerscript/components/filters/' .
            $pluginname . '/plugin.class.php');
        $classname = 'block_learnerscript\lsreports\plugin_' . $pluginname;
        $class = new $classname($this->config);
        $class->singleselection = false;
        $selectoption = false;
        $filterarray = $class->filter_data($selectoption);
        $this->filterdata = $filterarray;
    }

    /**
     * Add basicparams
     *
     * @param object $mform Basicparams form
     */
    public function add_basicparams_elements(&$mform) {
        global $CFG;
        $basicparams = (isset($this->basicparams)) ? $this->basicparams : [];
        if (!empty($basicparams)) {
            foreach ($basicparams as $f) {
                if ($f['name'] == 'status') {
                    if ($this->config->type == 'useractivities') {
                        $statuslist = ['all' => get_string('selectstatus', 'block_learnerscript'),
                        'notcompleted' => get_string('notcompleted', 'block_learnerscript'),
                        'completed' => 'Completed', ];
                    } else if ($this->config->type == 'coursesoverview') {
                        $statuslist = ['all' => get_string('selectstatus', 'block_learnerscript'),
                        'inprogress' => get_string('inprogress', 'block_learnerscript'),
                        'completed' => get_string('completed', 'block_learnerscript'), ];
                    } else {
                        $statuslist = ['all' => get_string('selectstatus', 'block_learnerscript'),
                        'inprogress' => get_string('inprogress', 'block_learnerscript'),
                        'notyetstarted' => get_string('notyeststarteds', 'block_learnerscript'),
                        'completed' => get_string('completed', 'block_learnerscript'), ];
                    }
                    $this->finalelements = $mform->addElement('select', 'filter_status', '',
                    $statuslist, ['data-select2' => true]);
                } else {
                    require_once($CFG->dirroot . '/blocks/learnerscript/components/filters/' .
                        $f['name'] . '/plugin.class.php');
                    $classname = 'block_learnerscript\lsreports\plugin_' . $f['name'];
                    $class = new $classname($this->config);
                    $class->singleselection = isset($f['singleselection']) ? $f['singleselection'] : true;
                    $class->placeholder = isset($f['placeholder']) ? $f['placeholder'] : true;
                    $class->maxlength = isset($f['maxlength']) ? $f['maxlength'] : 0;
                    $class->required = true;
                    $this->finalelements = $class->print_filter($mform);
                }
            }
        }

    }

    /**
     * @var object $filterform Filter form
     */
    public $filterform = null;

    /**
     * Check filters request
     *
     * @param  object $action Form action
     */
    public function check_filters_request($action = null) {
        global $CFG;

        $filters = (isset($this->componentdata['filters'])) ? $this->componentdata['filters'] : [];

        if (!empty($filters['elements'])) {
            $formdata = new stdclass;
            $ftcourses = optional_param('filter_courses', 0, PARAM_INT);
            $ftcoursecategories = optional_param('filter_coursecategories', 0, PARAM_INT);
            $ftusers = optional_param('filter_users', 0, PARAM_INT);
            $ftmodules = optional_param('filter_modules', 0, PARAM_INT);
            $ftactivities = optional_param('filter_activities', 0, PARAM_INT);
            $ftstatus = optional_param('filter_status', '', PARAM_TEXT);
            $urlparams = ['filter_courses' => $ftcourses, 'filter_coursecategories' => $ftcoursecategories,
                        'filter_users' => $ftusers, 'filter_modules' => $ftmodules,
                        'filter_activities' => $ftactivities, 'filter_status' => $ftstatus, ];
            $request = array_filter($urlparams);
            if ($request) {
                foreach ($request as $key => $val) {
                    if (strpos($key, 'filter_') !== false) {
                        $formdata->{$key} = $val;
                    }
                }
            }
            $this->instanceid = $this->config->id;

            $filterform = new filter_form($action, $this);

            $filterform->set_data($formdata);
            if ($filterform->is_cancelled()) {
                if ($action) {
                    redirect($action);
                } else {
                    redirect("$CFG->wwwroot/blocks/learnerscript/viewreport.php?id=" .
                        $this->config->id . "&courseid=" . $this->config->courseid);
                }
                die;
            }
            $this->filterform = $filterform;
        }
    }

    /**
     * Print filters
     *
     * @param  string $return Filters data
     */
    public function print_filters($return = false) {
        if (!is_null($this->filterform) && !$return) {
            $this->filterform->display();
        } else if (!is_null($this->filterform)) {
            return $this->filterform->render();
        }
    }

    /**
     * Build SQL query
     *
     * @param  int $count Count data
     */
    public function build_query($count = false) {
        $this->init();
        if ($count) {
            $this->count();
        } else {
            $this->select();
        }
        $this->from();
        $this->joins();
        $this->where();
        $this->search();
        $this->filters();
        if (!$count) {
            $this->groupby();
        }
    }

    /**
     * SQL query where conditions
     */
    public function where() {
        if ($this->reporttype != 'table'  &&  isset($this->selectedcolumns)) {
             $plot = (isset($this->componentdata['plot']['elements']))
             ? $this->componentdata['plot']['elements'] : [];
            foreach ($plot as $e) {
                if ($e['id'] == $this->reporttype) {
                    if ($e['pluginname'] == 'combination') {
                        foreach ($e['formdata']->yaxis_bar as $key) {
                            if (!empty($e['formdata']->{$key}) && method_exists($this, 'column_queries')) {
                                $this->sql .= ' AND (' . $this->column_queries($key, $this->defaultcolumn) . ')'
                                .$e['formdata']->{$key}.''.$e['formdata']->{$key .'_value'}.'';
                            }
                        }
                        foreach ($e['formdata']->yaxis_line as $key) {
                            if (!empty($e['formdata']->{$key}) && method_exists($this, 'column_queries')) {
                                $this->sql .= ' AND (' . $this->column_queries($key, $this->defaultcolumn) . ')'
                                .$e['formdata']->{$key}.''.$e['formdata']->{$key .'_value'}.'';
                            }
                        }
                    } else {
                        if (isset($e['formdata']->yaxis)) {
                            foreach ($e['formdata']->yaxis as $key) {
                                if (!empty($e['formdata']->{$key}) && method_exists($this, 'column_queries')) {
                                    $this->sql .= ' AND (' . $this->column_queries($key, $this->defaultcolumn) . ')'
                                    .$e['formdata']->{$key}.''.$e['formdata']->{$key .'_value'}.'';
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * SQL query select
     */
    public function select() {
        if (isset($this->sqlorder['column'])) {
            if (method_exists($this, 'column_queries') && $this->column_queries($this->sqlorder['column'], $this->defaultcolumn)) {
                if ($this->column_queries($this->sqlorder['column'], $this->defaultcolumn) !== " ") {
                    $this->sql .= ' ,(' . $this->column_queries($this->sqlorder['column'],
                        $this->defaultcolumn) . ') as '. $this->sqlorder['column'].'';
                }
            }
        }
    }

    /**
     * Joins in SQL query
     */
    public function joins() {
    }

    /**
     * Get all elements
     */
    public function get_all_elements() {
        global $DB;
        try {
            $finalelements = $DB->get_records_sql($this->sql, $this->params, $this->start, $this->length);
        } catch (\dml_exception $e) {
            $finalelements = [];
        }
        return $finalelements;
    }

    /**
     * Function to create report
     *
     * @param  int $blockinstanceid Report block instance id
     */
    public function create_report($blockinstanceid = null) {
        global $DB, $CFG;
        $context = context_system::instance();
        $this->check_permissions($context, $this->userid);
        $columns = (isset($this->componentdata['columns']['elements']))
        ? $this->componentdata['columns']['elements'] : [];
        $ordering = (isset($this->componentdata['ordering']['elements']))
        ? $this->componentdata['ordering']['elements'] : [];
        $plot = (isset($this->componentdata['plot']['elements']))
        ? $this->componentdata['plot']['elements'] : [];

        if ($this->reporttype !== 'table') {
            $this->graphcolumns = [];
            foreach ($plot as $column) {
                if ($column['id'] == $this->reporttype) {
                    $this->graphcolumns = $column;
                }
            }
            if (!empty($this->graphcolumns['formdata']->columnsort)
            && $this->graphcolumns['formdata']->columnsort
            && $this->graphcolumns['formdata']->sorting) {
                $this->sqlorder['column'] = $this->graphcolumns['formdata']->columnsort;
                $this->sqlorder['dir'] = $this->graphcolumns['formdata']->sorting;
            }
            if (!empty($this->graphcolumns['formdata']->limit)
            && $this->graphcolumns['formdata']->limit) {
                $this->length = $this->graphcolumns['formdata']->limit;
            }

            if ($this->graphcolumns['pluginname'] == 'combination') {
                $this->selectedcolumns = array_merge([$this->graphcolumns['formdata']->serieid] ,
                $this->graphcolumns['formdata']->yaxis_line,
                $this->graphcolumns['formdata']->yaxis_bar);
            } else if ($this->graphcolumns['pluginname'] == 'pie') {
                $this->selectedcolumns = [$this->graphcolumns['formdata']->areaname ,
                $this->graphcolumns['formdata']->areavalue, ];
            } else {
                 $this->selectedcolumns = !empty($this->graphcolumns['formdata']->yaxis) ?
                                        array_merge([$this->graphcolumns['formdata']->serieid] ,
                 $this->graphcolumns['formdata']->yaxis, ) : $this->graphcolumns['formdata']->serieid;
            }
        } else {
            $columnnames  = [];
            foreach ($columns as $key => $column) {
                if (isset($column['formdata']->column)) {
                    $columnnames[$column['formdata']->column] = $column['formdata']->columname;
                    $this->selectedcolumns[] = $column['formdata']->column;
                }
            }
        }
        $finalelements = [];
        $sqlorder = '';
        $orderingdata = [];
        if (!empty($this->ordercolumn)) {
            $this->sqlorder['column'] = $this->selectedcolumns[$this->ordercolumn['column']];
            $this->sqlorder['dir'] = $this->ordercolumn['dir'];
        } else if (!empty($ordering)) {
            foreach ($ordering as $o) {
                require_once($CFG->dirroot.'/blocks/learnerscript/components/ordering/' .
                    $o['pluginname'] . '/plugin.class.php');
                $classname = 'block_learnerscript\lsreports\plugin_'.$o['pluginname'];
                $classorder = new $classname($this->config);
                if ($classorder->sql) {
                    $orderingdata = $o['formdata'];
                    $sqlorder = $classorder->execute($orderingdata);
                }
            }
        }
        $this->params['siteid'] = SITEID;
        $this->build_query(true);

        if ($this->reporttype == 'table') {
            if (is_siteadmin($this->userid) || (new ls)->is_manager($this->userid, $this->contextlevel, $this->role)) {
                try {
                    $this->totalrecords = $DB->count_records_sql($this->sql, $this->params);
                } catch (\dml_exception $e) {
                    $this->totalrecords = 0;
                }
            } else {
                if ($this->rolewisecourses != '') {
                    try {
                        $this->totalrecords = $DB->count_records_sql($this->sql, $this->params);
                    } catch (\dml_exception $e) {
                        $this->totalrecords = 0;
                    }
                } else {
                    $this->totalrecords = 0;
                }
            }
        }
        $this->build_query();
        if (is_array($this->sqlorder) && !empty($this->sqlorder)) {
            $this->sql .= " ORDER BY ". $this->sqlorder['column'] .' '. $this->sqlorder['dir'];
        } else {
            if (!empty($sqlorder)) {
                $this->sql .= " ORDER BY $sqlorder ";
            } else {
                $this->sql .= " ORDER BY $this->defaultcolumn DESC ";
            }
        }
        if (is_siteadmin($this->userid)
        || (new ls)->is_manager($this->userid, $this->contextlevel, $this->role)
        || $this->role == 'manager') {
            $finalelements = $this->get_all_elements();
            $rows = $this->get_rows($finalelements);
        } else {
            if ($this->rolewisecourses != '') {
                $finalelements = $this->get_all_elements();
                $rows = $this->get_rows($finalelements);
            } else {
                $rows = [];
            }
        }
        $reporttable = [];
        $tablehead = [];
        $tablealign = [];
        $tablesize = [];
        $tablewrap = [];
        $firstrow = true;
        $pluginscache = [];
        if ($rows) {
            foreach ($rows as $r) {
                $tempcols = [];
                foreach ($columns as $c) {
                    $c = (array) $c;
                    if (empty($c)) {
                        continue;
                    }
                    require_once($CFG->dirroot . '/blocks/learnerscript/components/columns/' .
                    $c['pluginname'] . '/plugin.class.php');
                    $classname = 'block_learnerscript\lsreports\plugin_' . $c['pluginname'];

                    if (!isset($pluginscache[$classname])) {
                        $class = new $classname($this->config, $c);
                        $pluginscache[$classname] = $class;
                    } else {
                        $class = $pluginscache[$classname];
                    }
                    $class->role = $this->role;
                    $class->colformat = $this->colformat;
                    $class->reportinstance = $blockinstanceid ? $blockinstanceid : $this->config->id;
                    $class->reportfilterparams = $this->params;
                    $rid = isset($r->id) ? $r->id : 0;
                    if (isset($c['formdata']->column) &&
                    (!empty($this->selectedcolumns) && in_array($c['formdata']->column, $this->selectedcolumns))) {
                        if (!empty($this->params['filter_users'])) {
                            $this->currentuser = $this->params['filter_users'];
                        }
                        if (method_exists($this, 'column_queries')) {
                            if (isset($r->course)) {
                                $c['formdata']->subquery = $this->column_queries($c['formdata']->column, $rid, $r->course);
                                $this->currentcourseid = $r->course;
                            } else if (isset($r->user)) {
                                $c['formdata']->subquery = $this->column_queries($c['formdata']->column, $rid, $r->user);
                            } else {
                                $c['formdata']->subquery = $this->column_queries($c['formdata']->column, $rid);
                            }
                        }
                        $columndata = $class->execute($c['formdata'], $r, $this->userid,
                                                                         $this->currentcourseid,
                                                                         $this->reporttype,
                                                                         $this->starttime,
                                                                         $this->endtime
                                                                         );
                        $tempcols[$c['formdata']->column] = $columndata;
                    }
                    if ($firstrow) {
                        if (isset($c['formdata']->column)) {
                            $columnheading = !empty($c['formdata']->columname) ? $c['formdata']->columname : $c['formdata']->column;
                            $tablehead[$c['formdata']->column] = $columnheading;
                        }
                        list($align, $size, $wrap) = $class->colformat($c['formdata']);
                        $tablealign[] = $align;
                        $tablesize[] = $size ? $size . '%' : '';
                        $tablewrap[] = $wrap;
                    }
                }

                $firstrow = false;
                $reporttable[] = $tempcols;
            }
        }

        // EXPAND ROWS.
        $finaltable = [];
        foreach ($reporttable as $row) {
            $col = [];
            $multiple = false;
            $nrows = 0;
            $mrowsi = [];
            foreach ($row as $key => $cell) {
                if (!is_array($cell)) {
                    $col[$key] = $cell;
                } else {
                    $multiple = true;
                    $nrows = count($cell);
                    $mrowsi[] = $key;
                }
            }
            if ($multiple) {
                $newrows = [];
                for ($i = 0; $i < $nrows; $i++) {
                    $newrows[$i] = $row;
                    foreach ($mrowsi as $index) {
                        $newrows[$i][$index] = $row[$index][$i];
                    }
                }
                foreach ($newrows as $r) {
                    $finaltable[] = $r;
                }
            } else {
                $finaltable[] = $col;
            }
        }

        if ($blockinstanceid == null) {
            $blockinstanceid = $this->config->id;
        }

        // Make the table, head, columns, etc...

        $table = new stdClass;
        $table->id = 'reporttable_' . $blockinstanceid . '';
        $table->data = $finaltable;
        $table->head = $tablehead;
        $table->size = $tablesize;
        $table->align = $tablealign;
        $table->wrap = $tablewrap;
        $table->width = (isset($this->componentdata['columns']['config']))
        ? $this->componentdata['columns']['config']->tablewidth : '';
        $table->summary = $this->config->summary;
        $table->tablealign = (isset($this->componentdata['columns']['config']))
        ? $this->componentdata['columns']['config']->tablealign : 'center';
        $table->cellpadding = (isset($this->componentdata['columns']['config']))
        ? $this->componentdata['columns']['config']->cellpadding : '5';
        $table->cellspacing = (isset($this->componentdata['columns']['config']))
        ? $this->componentdata['columns']['config']->cellspacing : '1';
        $table->class = (isset($this->componentdata['columns']['config']))
        ? $this->componentdata['columns']['config']->class : 'generaltable';
                // CALCS.
        if ($this->calculations) {
            $finalheadcalcs = $this->get_calcs($finaltable, $tablehead);
            $finalcalcs = $finalheadcalcs->data;
            $calcs = new html_table();
            $calcshead = [];
            $calcshead[] = 'Column Name';

            foreach ($finalheadcalcs->calcdata as $key => $head) {
                    $calcshead[$head] = ucfirst(get_string($head, 'block_learnerscript'));
                    $calcshead1[$head] = $key;
            }
            $calcsdata = [];
            foreach ($finalheadcalcs->head as $key => $head) {
                $row = [];
                $row[] = $columnnames[$key];
                foreach ($calcshead1 as $key1 => $value) {
                    if (array_key_exists($key.'-'.$key1, $finalcalcs)) {
                        $row[] = $finalcalcs[$key.'-'.$key1];
                    } else {
                        $row[] = 'N/A';
                    }
                }
                $calcsdata[] = $row;
            }

            $calcs->data = $calcsdata;
            $calcs->head = $calcshead;
            $calcs->size = $tablesize;
            $calcs->align = $tablealign;
            $calcs->wrap = $tablewrap;
            $calcs->summary = $this->config->summary;
            $calcs->attributes['class'] = (isset($this->componentdata['columns']['config']))
            ? $this->componentdata['columns']['config']->class : 'generaltable';
            $this->finalreport = new stdClass();
            $this->finalreport->calcs = $calcs;
        }
        if (!$this->finalreport) {
            $this->finalreport = new stdClass;
        }
        $this->finalreport->table = $table;
        return true;
    }

    /**
     * utf8_strrev
     *
     * @param  string $str
     * @return string
     */
    public function utf8_strrev($str) {
        preg_match_all('/./us', $str, $ar);
        return join('', array_reverse($ar[0]));
    }

    /**
     * Rolewise courses
     */
    public function rolewisecourses() {
        global $DB;
        if (!is_siteadmin($this->userid) && !(new ls)->is_manager($this->userid, $this->contextlevel, $this->role)) {
            if (!empty($this->componentdata['permissions']['elements'])) {
                $roleincourse = array_filter($this->componentdata['permissions']['elements'], function($permission) {
                    // Role in course permission.
                    if ($permission['pluginname'] == 'roleincourse') {
                        return true;
                    }
                });
            }
            if (!empty($roleincourse)) {
                $currentroleid = $DB->get_field('role', 'id', ['shortname' => $this->role]);

                foreach ($roleincourse as $role) {
                    if (!empty($this->role) && (!isset($role['formdata']->contextlevel)
                    || $role['formdata']->roleid != $currentroleid)) {
                        continue;
                    }
                    $permissionslib = new permissionslib($role['formdata']->contextlevel,
                    $role['formdata']->roleid,
                    $this->userid);
                    $rolecontexts = $DB->get_records_sql("SELECT DISTINCT CONCAT(r.id, '@', rcl.id),
                    r.shortname, rcl.contextlevel
                    FROM {role} r
                    JOIN {role_context_levels} rcl ON rcl.roleid = r.id AND rcl.contextlevel NOT IN (70)
                    WHERE 1 = 1
                    ORDER BY rcl.contextlevel ASC");
                    foreach ($rolecontexts as $rc) {
                        if ($rc->contextlevel == 10 && ($rc->shortname == 'manager')) {
                            continue;
                        }
                        $rcontext[] = $rc->shortname .'_'.$rc->contextlevel;
                    }
                    $permissionslib->moodleroles = $rcontext;
                    if ($permissionslib->has_permission()) {
                          return implode(',', $permissionslib->get_rolewise_courses());
                    }
                }
            }
        }
    }
}
