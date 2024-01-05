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

/** A Moodle block for creating customizable reports
 * @package   block_learnerscript
 * @copyright 2023 Moodle India
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace block_learnerscript\lsreports;
use block_learnerscript\local\pluginbase;
/** Course views Columns */
class plugin_courseviews extends pluginbase {
    /**
     * Course views init function
     */
    public function init() {
        $this->fullname = get_string('courseviews', 'block_learnerscript');
        $this->type = 'undefined';
        $this->form = true;
        $this->reporttypes = ['courseviews'];
    }
    /**
     * Course views column summary
     * @param object $data Course views column name
     */
    public function summary($data) {
        return format_string($data->columname);
    }
    /**
     * This function return field column format
     * @param object $data Field data
     */
    public function colformat($data) {
        $align = (isset($data->align)) ? $data->align : '';
        $size = (isset($data->size)) ? $data->size : '';
        $wrap = (isset($data->wrap)) ? $data->wrap : '';
        return [$align, $size, $wrap];
    }
    /**
     * This function executes the columns data
     * @param object $data Columns data
     * @param object $row Row data
     * @param object $user User data
     * @param int $courseid Course id
     * @param int $starttime Start time
     * @param int $endtime End time
     * @return object
     */
    public function execute($data, $row, $user, $courseid, $starttime = 0, $endtime = 0) {
        return (isset($row->{$data->column})) ? $row->{$data->column} : ' -- ';
    }
}
