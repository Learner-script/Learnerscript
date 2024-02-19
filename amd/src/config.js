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
 * TODO describe module config
 *
 * @module     block_learnerscript/config
 * @copyright  2023 YOUR NAME <your@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([], function() {
    window.requirejs.config({
        paths: {
            "datatables": M.cfg.wwwroot + '/blocks/learnerscript/js/jquery.dataTables',
            "serialize": M.cfg.wwwroot + '/blocks/learnerscript/js/jquery.serialize-object',
            "select2": M.cfg.wwwroot + '/blocks/learnerscript/js/select2',
        },
        shim: {
            'datatables': {exports: 'dataTable'},
            'serialize': {exports: 'serialize'},
            'select2': {exports: 'select2'},
        }
    });
});
