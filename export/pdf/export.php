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
require_once($CFG->dirroot . '/blocks/learnerscript/lib.php');
use block_learnerscript\local\ls;

/**
 * This function export report in PDF format
 * @param object $reportclass Report class
 * @param int $id Report id
 */
function export_report($reportclass, $id) {
    global $DB, $CFG;
    $reportdata = $reportclass->finalreport;
    $requestdata = $_REQUEST;
    require_once($CFG->libdir . '/pdflib.php');
    $reportname = $DB->get_record('block_learnerscript', ['id' => $id]);
    $table = $reportdata->table;
    $matrix = [];
    $reportname->name == $reportdata->name . "_" . date('d M Y H:i:s', time()) . '.pdf';

    $filters = [];
    foreach ($requestdata as $key => $val) {
        if (strpos($key, 'filter_') !== false) {
            $key = explode('_', $key, 2)[1];
            $filters[$key] = $val;
        }
    }
    $finalfilterdata = '';
    if (!empty($reportclass->selectedfilters)) {
        foreach ($reportclass->selectedfilters as $k => $filter) {
            $finalfilterdata .= html_writer::div($k . ' ' . $filter);
        }
    }
    if (!empty($table->head)) {
        $countcols = count($table->head);
        $keys = array_keys($table->head);
        $lastkey = end($keys);
        foreach ($table->head as $key => $heading) {
            $matrix[0][$key] = str_replace("\n", ' ', htmlspecialchars_decode(strip_tags(nl2br($heading))));
        }
    }
    if (!empty($table->data)) {
        foreach ($table->data as $rkey => $row) {
            foreach ($row as $key => $item) {
                $matrix[$rkey + 1][$key] = str_replace("\n", ' ', htmlspecialchars_decode(strip_tags(nl2br($item))));
            }
        }
    }

    $table = "";
    $table .= "<table border=\"1\" cellpadding=\"5\">";
    $s = count($matrix);
    reset($matrix);
    $firstkey = key($matrix);
    $reporttype = $DB->get_field('block_learnerscript',  'type',  ['id' => $id]);
    if ($matrix) {
        if ($reporttype != 'courseprofile' && $reporttype != 'userprofile') {
            $table .= "<thead><tr style=\"color:#000000;\">";
            for ($i = $firstkey; $i < ($firstkey + 1); $i++) {
                foreach ($matrix[$i] as $col) {
                    $table .= "<td>$col</td>";
                }
            }
            $table .= "</tr></thead>";
        }

        $table .= "<tbody>";
        for ($i = ($firstkey + 1); $i < count($matrix); $i++) {
            $table .= "<tr>";
            foreach ($matrix[$i] as $col) {
                $table .= "<td>$col</td>";
            }
            $table .= "</tr>";
        }
    }
    $table .= "</tbody></table>";

    /* declaring MYPDF for custom Header and Footer */
    /** MYPDF */
    class MYPDF extends TCPDF {
        /**
         * PDF page header
         */
        public function header() {
            global $DB, $CFG;
            $this->SetY(100);

            $headerdata = $this->getHeaderData();
            $this->SetFont('helvetica', 'B', 10);
        }

        /**
         * PDF page footer
         */
        public function footer() {
            global $DB, $CFG;
            $requestdata = $_REQUEST;
            $reportname = $DB->get_record('block_learnerscript', ['id' => $requestdata['id']]);
            // Position at 15 mm from bottom.
            // Set font.
            $this->SetFont('helvetica', 'I', 10);

            $this->Cell(0, 10, 'Page ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C',
            0, '', 0, false, 'T', 'M');
            $this->SetY(-15);
        }

    }

    $doc = new MYPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    $doc->setPrintHeader(true);
    $doc->setPrintFooter(true);

    // Set default header data.
    $doc->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 059', PDF_HEADER_STRING);

    // Set header and footer fonts.
    $doc->setHeaderFont([PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN]);
    $doc->setFooterFont([PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA]);

    // Set default monospaced font.
    $doc->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    // Set margins.
    $doc->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $doc->SetHeaderMargin(PDF_MARGIN_HEADER);
    $doc->SetFooterMargin(PDF_MARGIN_FOOTER);

    // Set auto page breaks.
    $doc->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

    // Set auto page breaks.
    $doc->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);

    // Set image scale factor.
    $doc->setImageScale(PDF_IMAGE_SCALE_RATIO);

    // Add a page.
    $doc->AddPage();

    // Set JPEG quality.
    $doc->setJPEGQuality(75);

    $head = get_config('block_learnerscript', 'analytics_color');
    $header = (new ls)->pdf_reportheader();

    $headerimgpath = get_reportheader_imagepath();

    $filename = $reportname->name;
    $doc->writeHTMLCell($w = 0, $h = 10, $x = '10', $y = '10', $header, $border = 0, $ln = 1,
    $fill = 0, $reseth = true, $align = '', $autopadding = true);
    $doc->writeHTMLCell($w = 100, $h = 10, $x = '80', $y = '12', '<h1><b>' . $reportname->name .
    '</b></h1>', $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);
    $doc->writeHTMLCell($w = 100, $h = 10, $x = '10', $y = '23', '<h4>Filters</h4>', $border = 0,
    $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);
    $doc->writeHTMLCell($w = 100, $h = 10, $x = '10', $y = '30', $finalfilterdata, $border = 0,
    $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);
    if (!empty($reportclass->selectedfilters) && count($reportclass->selectedfilters) <= 4) {
        $doc->writeHTMLCell($w = 0, $h = 0, $x = '10', $y = '70', $table, $border = 0,
        $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);
    } else {
        $doc->writeHTMLCell($w = 0, $h = 0, $x = '10', $y = '90', $table, $border = 0,
        $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);
    }

    $doc->Output($filename, 'I');
}
