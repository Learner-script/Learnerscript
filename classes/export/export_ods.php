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

namespace block_learnerscript\export;
defined('MOODLE_INTERNAL') || die();
ini_set("memory_limit", "-1");
ini_set('max_execution_time', 6000);

use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\Common\Creator\WriterFactory;

require_once($CFG->dirroot . '/blocks/learnerscript/lib.php');
require_once($CFG->libdir . '/adminlib.php');

/**
 * Class export_ods
 *
 * @package    block_learnerscript
 * @copyright  2024 Moodle India Information Solutions Private Limited
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class export_ods {
    /**
     * Export data in ODS format.
     * @package block_learnerscript
     * @param object $reportclass
     * @return mixed
     */
    public function export_report($reportclass) {
        global $CFG;
        $reportdata = $reportclass->finalreport;
        require_once($CFG->dirroot . '/lib/excellib.class.php');
        $table = $reportdata->table;
        $filename = $reportdata->name . "_" . date('d M Y H:i:s', time()) . '.ods';
        $writer = WriterFactory::createFromFile($filename);
        $writer->openToBrowser($filename); // Stream data directly to the browser.
        $filter = ['Filters'];
        $filterrow = Row::fromValues($filter);
        $writer->addRow($filterrow);
        if (!empty($reportclass->selectedfilters)) {
            foreach ($reportclass->selectedfilters as $k => $filter) {
                $filterrow = Row::fromValues([$k, $filter]);
                $writer->addRow($filterrow);
            }
        }
        $head = [];
        if (!empty($table->head)) {
            foreach ($table->head as $key => $heading) {
                $head[] = $heading;
            }
            $headrow = Row::fromValues($head);
            $writer->addRow($headrow);
        }
        $datarow = [];
        if (!empty($table->data)) {
            foreach ($table->data as $key => $value) {
                $data = array_map(function($v) {
                    return trim(strip_tags($v));
                }, $value);
                $datarow[] = Row::fromValues($data);
            }
        }

        $writer->addRows($datarow);
        $writer->close();
    }

    /**
     * ODS report export attachment
     * @param object $reportclass Report data
     * @param string $filename File name
     */
    public function export_ods_attachment($reportclass, $filename = null) {
        global $CFG;
        require_once($CFG->dirroot . '/lib/odslib.class.php');
        $report = $reportclass->finalreport;
        $table = $report->table;
        $matrix = [];
        $filename = $filename . '.ods';

        if (!empty($table->head)) {
            foreach ($table->head as $key => $heading) {
                $matrix[0][$key] = str_replace("\n", ' ', htmlspecialchars_decode(strip_tags(nl2br($heading)),
                                    ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401));
            }
        }

        if (!empty($table->data)) {
            foreach ($table->data as $rkey => $row) {
                foreach ($row as $key => $item) {
                    $matrix[$rkey + 1][$key] = str_replace("\n", ' ', htmlspecialchars_decode(strip_tags(nl2br($item)),
                                ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401));
                }
            }
        }
        $workbook = new \MoodleODSWorkbook($filename);

        $myxls = [];

        $myxls[0] = $workbook->add_worksheet('');
        foreach ($matrix as $ri => $col) {
            foreach ($col as $ci => $cv) {
                $myxls[0]->write($ri, $ci, $cv);
            }
        }
        $writer = new \MoodleODSWriter($myxls);
        $contents = $writer->get_file_content();
        $handle = fopen($filename, 'w');
        fwrite($handle, $contents);
        fclose($handle);
    }
}
