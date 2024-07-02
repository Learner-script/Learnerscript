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

namespace block_learnerscript\local;
use html_writer;
use moodle_url;

/**
 * Class license_setting
 *
 * @package    block_learnerscript
 * @copyright  2023 Moodle India Information Solutions Private Limited
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class license_setting extends \admin_setting_configtext {
    /**
     * Get learnerscript settings
     */
    public function get_setting() {
        return $this->config_read($this->name);
    }
    /**
     * Write learnerscript settings
     * @param string $data
     */
    public function write_setting($data) {
        GLOBAL $CFG, $PAGE;

        if (empty($data)) {
            set_config('ls_'.$this->name, $data, 'block_learnerscript');
            return '';
        }
        $learnerscript = md5($data);
        set_config('ls_'.$this->name, $learnerscript, 'block_learnerscript');
        $lsreportconfigstatus = get_config('block_learnerscript', 'lsreportconfigstatus');
        if (!$lsreportconfigstatus) {
            redirect(new moodle_url('/blocks/learnerscript/lsconfig.php', ['import' => 1]));
        } else {
            $reportdashboardblockexists = $PAGE->blocks->is_known_block_type('reportdashboard', false);
            if ($reportdashboardblockexists) {
                redirect(new moodle_url('/blocks/reportdashboard/dashboard.php'));
            } else {
                redirect(new moodle_url('/blocks/learnerscript/managereport.php'));
            }
        }
        exit;
    }

    /**
     * Output HTML
     * @param string $data Import report data
     * @param string $query Import query
     * @return string
     */
    public function output_html($data, $query='') {
        global $CFG, $PAGE;
        $default = $this->get_defaultsetting();
        $pluginman = \core_plugin_manager::instance();
        $reportdashboardpluginfo = $pluginman->get_plugin_info('block_reportdashboard');
        $reporttilespluginfo = $pluginman->get_plugin_info('block_reporttiles');
        $error = false;
        $errordata = [];
        $reportdashboardblockexists = $PAGE->blocks->is_known_block_type('reportdashboard', false);
        // Make sure we know the plugin.
        if (is_null($reportdashboardpluginfo) || !$reportdashboardblockexists) {
            $error = true;
            $errordata[] = get_string('learnerscriptwidget', 'block_learnerscript');
        }
        $reporttilesblockexists = $PAGE->blocks->is_known_block_type('reporttiles', false);
        // Make sure we know the plugin.
        if (is_null($reporttilespluginfo) || !$reporttilesblockexists) {
            $error = true;
            $errordata[] = get_string('learnerscripttiles', 'block_learnerscript');
        }

        $return = '';
        $disabled = '';
        if ($error) {
            $errormsg = implode(', ', $errordata);
            $return .= html_writer::div(html_writer::link(get_string('installenable', 'block_learnerscript') . $errormsg .
            get_string('pluginclick', 'block_learnerscript') . new \moodle_url('/admin/tool/installaddon/index.php'),
            get_string('installplugins', 'block_learnerscript'),
            ['title' => get_string('installplugin', 'block_learnerscript')]), "alert alert-notice");
            $disabled = 'disabled';
        }

        $return .= format_admin_setting($this, $this->visiblename,
            html_writer::div(html_writer::tag('input', '', ['type' => 'text', 'size' => $this->size,
            'id' => $this->get_id(), 'name' => $this->get_full_name(), 'value' => s($data) . $disabled])),
            $this->description, true, '', $default, $query);
        return $return;
    }
}
