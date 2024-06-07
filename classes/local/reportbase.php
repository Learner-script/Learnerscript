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
namespace block_learnerscript\local;
defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/lib/evalmath/evalmath.class.php');
use stdclass;
use block_learnerscript\form\filter_form;
use html_table;
use EvalMath;
use block_learnerscript\local\ls as ls;
use context_system;

/** Reportbase */
class reportbase {

    /**
     * @var int $id Report id
     */
    public $id = 0;

    /**
     * @var object $components Report components
     */
    public $components = [];

    /**
     * @var object $finalreport Final report data
     */
    public $finalreport;

    /**
     * @var array $finalelements Report final elements
     */
    public $finalelements;

    /**
     * @var int $totalrecords Total records
     */
    public $totalrecords = 0;

    /**
     * @var int $currentuser Current user id
     */
    public $currentuser = 0;

    /**
     * @var int $currentcourseid Current course id
     */
    public $currentcourseid = 1;

    /**
     * @var int $starttime Start time
     */
    public $starttime = 0;

    /**
     * @var int $endtime End time
     */
    public $endtime = 0;

    /**
     * @var string $sql Report SQL query
     */
    public $sql = '';

    /**
     * @var bool $designpage Design page
     */
    public $designpage = true;

    /**
     * @var array $tablehead Report table head
     */
    public $tablehead;

    /**
     * @var array $ordercolumn Report order column
     */
    public $ordercolumn;

    /**
     * @var array $sqlorder SQL order
     */
    public $sqlorder;

    /**
     * @var bool $exports
     */
    public $exports = true;

    /**
     * @var int $start Start count
     */
    public $start = 0;

    /**
     * @var int $length Reports length
     */
    public $length = 10;

    /**
     * @var string $search Search value
     */
    public $search;

    /**
     * @var int $courseid Course id
     */
    public $courseid;

    /**
     * @var int $cmid Course module id
     */
    public $cmid;

    /**
     * @var int $userid User id
     */
    public $userid;

    /**
     * @var string $status Report data status
     */
    public $status;

    /**
     * @var array $filters Report filters
     */
    public $filters;

    /**
     * @var array $columns Report columns
     */
    public $columns;

    /**
     * @var array $basicparams Basic params list
     */
    public $basicparams;

    /**
     * @var array $params Report params list
     */
    public $params;

    /**
     * @var array $filterdata Report filters data
     */
    public $filterdata;

    /**
     * @var string $role User role
     */
    public $role;

    /**
     * @var int $contextlevel User contextlevel
     */
    public $contextlevel;

    /**
     * @var boolean $parent
     */
    public $parent = true;

    /**
     * @var boolean $courselevel
     */
    public $courselevel = false;

    /**
     * @var boolean $conditionsenabled
     */
    public $conditionsenabled = false;

    /**
     * @var string $reporttype
     */
    public $reporttype = 'table';

    /**
     * @var boolean $scheduling
     */
    public $scheduling = false;

    /**
     * @var boolean $colformat
     */
    public $colformat = false;

    /**
     * @var boolean $calculations
     */
    public $calculations = false;

    /**
     * @var boolean $singleplot
     */
    public $singleplot;

    /**
     * @var string $rolewisecourses
     */
    public $rolewisecourses = '';

    /**
     * @var object $componentdata
     */
    public $componentdata;

    /**
     * @var array $graphcolumns
     */
    private $graphcolumns;

    /**
     * @var array $userroles
     */
    public $userroles;

    /**
     * @var array $selectedcolumns
     */
    public $selectedcolumns;

    /**
     * @var array $selectedfilters
     */
    public $selectedfilters;

    /**
     * @var array $conditionfinalelements
     */
    public $conditionfinalelements = [];

    /**
     * @var stdClass $config
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
     * @var array $moodleroles
     */
    public $moodleroles;

    /**
     * @var string $contextrole
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
     * @var boolean $customheader Custom header
     */
    public $customheader;

    /**
     * @var string $reportcontenttype Report contenttype
     */
    public $reportcontenttype;

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
        $permissions = (isset($this->componentdata->permissions)) ? $this->componentdata->permissions : [];
        if (empty($permissions->elements)) {
            return has_capability('block/learnerscript:viewreports', $context, $userid);
        } else {
            $i = 1;
            $cond = [];
            foreach ($permissions->elements as $p) {
                require_once($CFG->dirroot . '/blocks/learnerscript/components/permissions/' .
                    $p->pluginname . '/plugin.class.php');
                $classname = 'block_learnerscript\lsreports\plugin_' . $p->pluginname;
                $class = new $classname($this->config);
                $class->role = $this->role;
                $class->userroles = isset($this->userroles) ? $this->userroles : '';
                $cond[$i] = $class->execute($userid, $context, $p->formdata);
                $i++;
            }
            if (count($cond) == 1) {
                return $cond[1];
            } else {
                $m = new EvalMath;
                $orig = $dest = [];
                if (isset($permissions->config) && isset($permissions->config->conditionexpr)) {
                    $logic = trim($permissions->config->conditionexpr);
                    // Security.
                    // No more than: conditions * 10 chars.
                    $logic = substr($logic, 0, count($permissions->elements) * 10);
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
        $filters = (isset($this->componentdata->filters)) ? $this->componentdata->filters : [];
        if (!empty($filters->elements)) {
            foreach ($filters->elements as $f) {
                if ($f->formdata->value) {
                    require_once($CFG->dirroot . '/blocks/learnerscript/components/filters/' .
                        $f->pluginname . '/plugin.class.php');
                    $classname = 'block_learnerscript\lsreports\plugin_' . $f->pluginname;
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
                        'completed' => get_string('completed', 'block_learnerscript'), ];
                    } else if ($this->config->type == 'coursesoverview') {
                        $statuslist = ['all' => get_string('selectstatus', 'block_learnerscript'),
                        'inprogress' => get_string('inprogress', 'block_learnerscript'),
                        'completed' => get_string('completed', 'block_learnerscript'), ];
                    } else {
                        $statuslist = ['all' => get_string('selectstatus', 'block_learnerscript'),
                        'inprogress' => get_string('inprogress', 'block_learnerscript'),
                        'notyetstarted' => get_string('notyetstarted', 'block_learnerscript'),
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

        $filters = (isset($this->componentdata->filters)) ? $this->componentdata->filters : [];

        if (!empty($filters->elements)) {
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
             $plot = (isset($this->componentdata->plot->elements))
             ? $this->componentdata->plot->elements : [];
            foreach ($plot as $e) {
                if ($e->id == $this->reporttype) {
                    if ($e->pluginname == 'combination') {
                        foreach ($e->formdata->yaxis_bar as $key) {
                            if (!empty($e->formdata->{$key}) && method_exists($this, 'column_queries')) {
                                $this->sql .= ' AND (' . $this->column_queries($key, $this->defaultcolumn) . ')'
                                .$e->formdata->{$key}.''.$e->formdata->{$key .'_value'}.'';
                            }
                        }
                        foreach ($e->formdata->yaxis_line as $key) {
                            if (!empty($e->formdata->{$key}) && method_exists($this, 'column_queries')) {
                                $this->sql .= ' AND (' . $this->column_queries($key, $this->defaultcolumn) . ')'
                                .$e->formdata->{$key}.''.$e->formdata->{$key .'_value'}.'';
                            }
                        }
                    } else {
                        if (isset($e->formdata->yaxis)) {
                            foreach ($e->formdata->yaxis as $key) {
                                if (!empty($e->formdata->{$key}) && method_exists($this, 'column_queries')) {
                                    $this->sql .= ' AND (' . $this->column_queries($key, $this->defaultcolumn) . ')'
                                    .$e->formdata->{$key}.''.$e->formdata->{$key .'_value'}.'';
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
        $columns = (isset($this->componentdata->columns->elements))
        ? $this->componentdata->columns->elements : [];
        $ordering = (isset($this->componentdata->ordering->elements))
        ? $this->componentdata->ordering->elements : [];
        $plot = (isset($this->componentdata->plot->elements))
        ? $this->componentdata->plot->elements : [];

        if ($this->reporttype !== 'table') {
            $this->graphcolumns = [];
            foreach ($plot as $column) {
                if ($column->id == $this->reporttype) {
                    $this->graphcolumns = $column;
                }
            }
            if (!empty($this->graphcolumns->formdata->columnsort)
            && $this->graphcolumns->formdata->columnsort
            && $this->graphcolumns->formdata->sorting) {
                $this->sqlorder['column'] = $this->graphcolumns->formdata->columnsort;
                $this->sqlorder['dir'] = $this->graphcolumns->formdata->sorting;
            }
            if (!empty($this->graphcolumns->formdata->limit)
            && $this->graphcolumns->formdata->limit) {
                $this->length = $this->graphcolumns->formdata->limit;
            }

            if ($this->graphcolumns->pluginname == 'combination') {
                $this->selectedcolumns = array_merge([$this->graphcolumns->formdata->serieid] ,
                $this->graphcolumns->formdata->yaxis_line,
                $this->graphcolumns->formdata->yaxis_bar);
            } else {
                 $this->selectedcolumns = !empty($this->graphcolumns->formdata->yaxis) ?
                                        array_merge([$this->graphcolumns->formdata->serieid] ,
                 $this->graphcolumns->formdata->yaxis, ) : $this->graphcolumns->formdata->serieid;
            }
        } else {
            $columnnames  = [];
            foreach ($columns as $key => $column) {
                if (isset($column->formdata->column)) {
                    $columnnames[$column->formdata->column] = $column->formdata->columname;
                    $this->selectedcolumns[] = $column->formdata->column;
                }
            }
        }
        $finalelements = [];
        $sqlorder = '';
        $orderingdata = [];
        if (!empty($this->ordercolumn)) {
            $this->sqlorder['column'] = $this->selectedcolumns[$this->ordercolumn['column']];
            $this->sqlorder['dir'] = $this->ordercolumn['dir'];
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
                    $c = (object) $c;
                    if (empty($c)) {
                        continue;
                    }
                    require_once($CFG->dirroot . '/blocks/learnerscript/components/columns/' .
                    $c->pluginname . '/plugin.class.php');
                    $classname = 'block_learnerscript\lsreports\plugin_' . $c->pluginname;

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
                    if (isset($c->formdata->column) &&
                    (!empty($this->selectedcolumns) && in_array($c->formdata->column, $this->selectedcolumns))) {
                        if (!empty($this->params['filter_users'])) {
                            $this->currentuser = $this->params['filter_users'];
                        }
                        if (method_exists($this, 'column_queries')) {
                            if (isset($r->course)) {
                                $c->formdata->subquery = $this->column_queries($c->formdata->column, $rid, $r->course);
                                $this->currentcourseid = $r->course;
                            } else if (isset($r->user)) {
                                $c->formdata->subquery = $this->column_queries($c->formdata->column, $rid, $r->user);
                            } else {
                                $c->formdata->subquery = $this->column_queries($c->formdata->column, $rid);
                            }
                        }
                        $columndata = $class->execute($c->formdata, $r, $this->reporttype
                                                                         );
                        $tempcols[$c->formdata->column] = $columndata;
                    }
                    if ($firstrow) {
                        if (isset($c->formdata->column)) {
                            $columnheading = !empty($c->formdata->columname) ? $c->formdata->columname : $c->formdata->column;
                            $tablehead[$c->formdata->column] = $columnheading;
                        }
                        list($align, $size, $wrap) = $class->colformat($c->formdata);
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
        $table->width = (isset($this->componentdata->columns->config))
        ? $this->componentdata->columns->config->tablewidth : '';
        $table->summary = $this->config->summary;
        $table->tablealign = (isset($this->componentdata->columns->config))
        ? $this->componentdata->columns->config->tablealign : 'center';
        $table->cellpadding = (isset($this->componentdata->columns->config))
        ? $this->componentdata->columns->config->cellpadding : '5';
        $table->cellspacing = (isset($this->componentdata->columns->config))
        ? $this->componentdata->columns->config->cellspacing : '1';
        $table->class = (isset($this->componentdata->columns->config))
        ? $this->componentdata->columns->config->class : 'generaltable';
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
            if (!empty($this->componentdata->permissions->elements)) {
                $roleincourse = array_filter($this->componentdata->permissions->elements, function($permission) {
                    // Role in course permission.
                    if ($permission->pluginname == 'roleincourse') {
                        return true;
                    }
                });
            }
            if (!empty($roleincourse)) {
                $currentroleid = $DB->get_field('role', 'id', ['shortname' => $this->role]);

                foreach ($roleincourse as $role) {
                    if (!empty($this->role) && (!isset($role->formdata->contextlevel)
                    || $role->formdata->roleid != $currentroleid)) {
                        continue;
                    }
                    $permissionslib = new permissionslib($role->formdata->contextlevel,
                    $role->formdata->roleid,
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
