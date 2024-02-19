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

/** Learner Script - Report Congiguration/Design for LearnerScript Reports
 * @package   block_learnerscript
 * @copyright 2023 Moodle India
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
ini_set("memory_limit", "-1");
ini_set('max_execution_time', 6000);

use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\Common\Creator\WriterFactory;

require_once($CFG->dirroot . '/blocks/learnerscript/lib.php');
require_once($CFG->libdir . '/adminlib.php');

/**
 * Export data in XLSX format.
 * @param object $reportclass
 * @param int $id report id
 */
function export_report($reportclass, $id) {
    global $DB, $CFG;
    $reportdata = $reportclass->finalreport;
    require_once($CFG->dirroot . '/lib/excellib.class.php');
    $table = $reportdata->table;
    $filename = $reportdata->name . "_" . date('d M Y H:i:s', time()) . '.xlsx';
    $writer = WriterFactory::createFromFile($filename);
    $writer->openToBrowser($filename); // Stream data directly to the browser.
    $filter = ['Filters'];
    $filterrow = Row::fromValues($filter);
    $writer->addRow($filterrow);
    $finalfilterdata = '';
    if (!empty($reportclass->selectedfilters)) {
        foreach ($reportclass->selectedfilters as $k => $filter) {
            $filterrow = Row::fromValues([$k, $filter]);
            $writer->addRow($filterrow);
        }
    }
    $head = [];
    $reporttype = $DB->get_field('block_learnerscript',  'type',  ['id' => $id]);
    if ($reporttype != 'courseprofile' && $reporttype != 'userprofile') {
        if (!empty($table->head)) {
            $keys = array_keys($table->head);
            foreach ($table->head as $key => $heading) {
                $head[] = $heading;
            }
            $headrow = Row::fromValues($head);
            $writer->addRow($headrow);
        }
    }
    $datarow = [];
    if (!empty($table->data)) {
        foreach ($table->data as $key => $value) {
            $data = array_map(function ($v) {
                return trim(strip_tags($v));
            }, $value);
            $datarow[] = Row::fromValues($data);
        }
    }
    $writer->addRows($datarow);
    $writer->close();
}
