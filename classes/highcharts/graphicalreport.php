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

namespace block_learnerscript\highcharts;

/**
 * Class graphicalreport
 *
 * @package    block_learnerscript
 * @copyright  2024 Moodle India Information Solutions Private Limited
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class graphicalreport {
    /**
     * Loads the required high chart JS libraries
     */
    public function __construct() {
    }

    /** Generates linechart/barchart with given data
     * @param object $data graph data
     * @param object $series series of values(X axis and Y axis etc...)
     * @param object $name
     * @param string $type line or bar
     * @param array $head
     * @param string $containerid div container ID of chart
     * @return array  line/bar chart markup with JS code
     */
    public function lbchart($data, $series, $name, $type, $head, $containerid = null) {
        $i = 0;
        $containerid == null ? $containerid = $series['id'] : null;
        empty($series['formdata']->serieslabel) ? $series['formdata']->serieslabel = $name->name : null;
        $lbchartdata = $this->get_lbchartdata($data, $series, $type, $head, $name);
        $lbchartdata['dataLabels'] = ['enabled' => true];
        $lbchartdata['borderRadius'] = 5;
        if (!empty($lbchartdata['error']) && $lbchartdata['error']) {
            return $lbchartdata;
        } else {
            $yaxistext = null;
            if ($series['formdata']->calcs) {
                $yaxistext = get_string($series['formdata']->calcs, 'block_learnerscript');
            }
            $seriesdatalabels = isset($series['formdata']->datalabels) ? $series['formdata']->datalabels : 0;
            $categorylistcategorylist = isset($lbchartdata['categorylist']) ? $lbchartdata['categorylist'] : [];

            $container = $type . 'container' . $containerid;
            $options = ['type' => '' . $type . '',
                        'containerid' => '' . $container . '',
                        'title' => '' . $series['formdata']->chartname . '',
                        'showlegend' => '' . $series['formdata']->showlegend . '',
                        'serieslabel' => '' . $series['formdata']->serieslabel . '',
                        'categorydata' => $categorylistcategorylist,
                        'id' => $series['id'],
                        'data' => $lbchartdata['comdata'],
                        'datalabels' => '' . $seriesdatalabels . '',
                        'yaxistext' => $yaxistext,
                        'ylabel' => $head[$series['formdata']->serieid],
                    ];
            return $options;
        }
    }

    /** Generates combination chart with given data
     * @param object $data graph data
     * @param object $series series of values(X axis and Y axis etc...)
     * @param object $name
     * @param string $type line or bar
     * @param array  $head
     * @param array $seriesvalues
     * @param string $containerid div container ID of chart
     * @return array
     */
    public function combination_chart($data, $series, $name, $type, $head, $seriesvalues, $containerid = null) {
        $containerid == null ? $containerid = $series['id'] : null;
        empty($series['formdata']->serieslabel) ? $series['formdata']->serieslabel = $name->name : null;
        $yaxistext = null;
        $graphdata = null;
        $i = 0;
        foreach ($series['formdata']->yaxis_bar as $yaxis) {
            if (array_key_exists($yaxis, $head)) {
                if ($data) {
                    $categorylist = [];
                    foreach ($data as $r) {
                        if ($r[$series['formdata']->serieid] == '') {
                            continue;
                        }
                        $r[$yaxis] = isset($r[$yaxis]) ? strip_tags($r[$yaxis]) : 0;
                        if (!preg_match('/:\S+/', $r[$yaxis])) {
                            if (strpos($yaxis, 'timespent') !== false) {
                                $label = (new \block_learnerscript\local\ls)->strtime($r[$yaxis]);
                            } else {
                                $r[$yaxis] = is_numeric($r[$yaxis]) ? $r[$yaxis] : floatval($r[$yaxis]);
                                $label = strip_tags($r[$yaxis]);
                            }
                            $graphdata[$yaxis][] = ['y' => $r[$yaxis], 'label' => $label];
                        } else {
                            $time = explode(':', $r[$yaxis]);
                            $totaltime = ($time[0] * 60 * 60) + ($time[1] * 60) + ($time[2]);
                            $label = (new \block_learnerscript\local\ls)->strtime($totaltime);
                            $totaltime = $totaltime / 3600;
                            $graphdata[$yaxis][] = ['y' => $totaltime, 'label' => $label];
                        }
                        $seriesdata[] = $r[$series['formdata']->serieid];
                        if (empty($series['formdata']->calcs)) {
                            $categorylist[] = strip_tags($r[$series['formdata']->serieid]);
                        } else {
                            $categorylist = [];
                        }
                    }
                    $i++;
                }
                $heading[] = $yaxis;
            }
        }
        $categorylist = [];
        foreach ($series['formdata']->yaxis_line as $yaxis) {
            if (array_key_exists($yaxis, $head)) {
                if ($data) {
                    foreach ($data as $r) {
                        if ($r[$series['formdata']->serieid] == '') {
                            continue;
                        }
                        $r[$yaxis] = isset($r[$yaxis]) ? strip_tags($r[$yaxis]) : 0;
                        if (!preg_match('/:\S+/', $r[$yaxis])) {
                            if (strpos($yaxis, 'timespent') !== false) {
                                $label = (new \block_learnerscript\local\ls)->strtime($r[$yaxis]);
                            } else {
                                $r[$yaxis] = is_numeric($r[$yaxis]) ? $r[$yaxis] : floatval($r[$yaxis]);
                                $label = strip_tags($r[$yaxis]);
                            }
                            $graphdata1[$yaxis][] = ['y' => $r[$yaxis], 'label' => $label];
                        } else {
                            $time = explode(':', $r[$yaxis]);
                            $totaltime = ($time[0] * 60 * 60) + ($time[1] * 60) + ($time[2]);
                            $label = (new \block_learnerscript\local\ls)->strtime($totaltime);
                            $totaltime = $totaltime / 3600;
                            $graphdata1[$yaxis][] = ['y' => $totaltime, 'label' => $label];
                        }
                        $seriesdata[] = $r[$series['formdata']->serieid];
                        if (empty($series['formdata']->calcs)) {
                            $categorylist[] = strip_tags($r[$series['formdata']->serieid]);
                        } else {
                            $categorylist = [];
                        }
                    }
                    $i++;
                }
                $heading[] = $yaxis;
            }
        }
        $comdata = [];
        if (!empty($graphdata)) {
            foreach ($graphdata as $k => $gdata) {
                $bardata = [];
                foreach ($gdata as $yaxisdata) {
                    $bardata[] = $yaxisdata['y'];
                }
                $comdata[] = ['data' => $bardata, 'label' => ucfirst($k), 'type' => 'bar' ];
            }
        }
        if (!empty($graphdata1)) {
            foreach ($graphdata1 as $k => $gdata) {
                $linedata = [];
                foreach ($gdata as $yaxisdata) {
                    $linedata[] = $yaxisdata['y'];
                }
                $comdata[] = ['data' => $linedata, 'label' => ucfirst($k), 'type' => 'line'];
            }
        }
        if (!empty($categorylist)  && !empty($linedata)) {
            if (count($categorylist) != count($linedata) ) {
                $a = count($linedata) - 1;
                $categorylist = array_slice($categorylist, 0, $a);
            }
        }
        $headseriesid = isset($head[$series['formdata']->serieid]) ? $head[$series['formdata']->serieid] : null;
        $options = ['type' => '' . $type . '',
                    'containerid' => '' . $containerid . '',
                    'title' => '' . $series['formdata']->chartname . '',
                    'showlegend' => '' . $series['formdata']->showlegend . '',
                    'serieslabel' => '' . $series['formdata']->serieslabel . '',
                    'categorydata' => $categorylist,
                    'id' => $series['id'],
                    'data' => $comdata,
                    'datalabels' => '' . $series['formdata']->datalabels . '',
                    'yaxistext' => $yaxistext,
                    'ylabel' => $headseriesid,
                ];
        return $options;

    }
    /** Get chart data
     * @param object $data graph data
     * @param object $series series of values(X axis and Y axis etc...)
     * @param string  $type
     * @param array  $head
     * @param object  $report
     * @return array
     */
    public function get_lbchartdata($data, $series, $type, $head, $report) {
        global $CFG;
        $i = 0;
        $error = [];
        $graphdata = [];
        if (empty($head)) {
            $error[] = get_string('nodataavailable', 'block_learnerscript');
        } else {
            foreach ($series['formdata']->yaxis as $yaxis) {
                if (array_key_exists($yaxis, $head)) {
                    if ($data) {
                        $categorylist = [];
                        foreach ($data as $r) {
                            if ($r[$series['formdata']->serieid] == '') {
                                continue;
                            }
                            if (array_key_exists($yaxis, $r)) {
                                $r[$yaxis] = strip_tags($r[$yaxis]);
                            } else {
                                $r[$yaxis] = '';
                            }
                            if (!preg_match('/:\S+/', $r[$yaxis])) {
                                if (strpos($yaxis, 'timespent') !== false) {
                                    $label = (new \block_learnerscript\local\ls)->strtime($r[$yaxis]);
                                } else {
                                    $r[$yaxis] = is_numeric($r[$yaxis]) ? $r[$yaxis] : floatval($r[$yaxis]);
                                    $label = strip_tags($r[$yaxis]);
                                }
                                $graphdata[$yaxis][] = ['y' => $r[$yaxis], 'label' => $label];
                                $calcdata[$yaxis][] = $r[$yaxis];
                            } else {
                                $time = explode(':', $r[$yaxis]);
                                $totaltime = ($time[0] * 60 * 60) + ($time[1] * 60) + ($time[2]);
                                $label = (new \block_learnerscript\local\ls)->strtime($totaltime);
                                $totaltime = $totaltime / 3600;
                                $graphdata[$yaxis][] = ['y' => $totaltime, 'label' => $label];
                                $calcdata[$yaxis][] = $totaltime;
                            }
                            $seriesdata[] = $r[$series['formdata']->serieid];
                            if (empty($series['formdata']->calcs)) {
                                $categorylist[] = strip_tags($r[$series['formdata']->serieid]);
                            } else {
                                $categorylist = [];
                            }
                        }
                        $i++;
                    }
                    $heading[] = $yaxis;
                }
            }
            $j = 0;
            $comdata = [];
            foreach ($graphdata as $k => $gdata) {
                $lbdata = [];
                foreach ($gdata as $yaxisdata) {
                    $lbdata[] = $yaxisdata['y'];
                }
                if ($type == 'spline') {
                    $type = 'line';
                } else if ($type == 'column') {
                    $type = 'bar';
                }
                $comdata[] = ['data' => $lbdata, 'label' => $head[$heading[$j]], 'type' => $type];
                $j++;
            }
        }
        if (empty($error)) {
            return compact('comdata', 'seriesdata', 'categorylist');
        } else {
            return ['error' => true, 'messages' => $error];
        }
    }
}
