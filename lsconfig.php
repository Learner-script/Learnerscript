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
require_once("../../config.php");
require_once($CFG->libdir.'/adminlib.php');
use block_learnerscript\local\ls as ls;

$import = optional_param('import', 0, PARAM_INT);
$reset = 0;
$status = $reset ? 'reset' : 'import';

require_login();

$context = context_system::instance();

$PAGE->requires->jquery_plugin('ui');
$PAGE->requires->jquery_plugin('ui-css');
$PAGE->requires->css('/blocks/learnerscript/css/slideshow.css');

$lsreportconfigstatus = get_config('block_learnerscript', 'lsreportconfigstatus');
$PAGE->set_url($CFG->wwwroot . '/blocks/learnerscript/lsconfig.php');
$PAGE->set_context($context);
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('lsconfigtitle', 'block_learnerscript'));
$PAGE->set_heading(get_string('lsreportsconfig', 'block_learnerscript'));

if ($import) {
    $pluginman = core_plugin_manager::instance();
    $reportdashboardpluginfo = $pluginman->get_plugin_info('block_reportdashboard');
    $reporttilespluginfo = $pluginman->get_plugin_info('block_reporttiles');
    $error = false;
    $errordata = [];
    // Make sure we know the plugin.
    if (is_null($reportdashboardpluginfo)) {
        $error = true;
        $errordata[] = get_string('installreqplugins',  'block_learnerscript', 'LearnerScript Widget');
    }
    // Make sure we know the plugin.
    if (is_null($reporttilespluginfo)) {
        $error = true;
        $errordata[] = get_string('installreqplugins',  'block_learnerscript', 'LearnerScript Report Tiles');
    }

    $reportdashboardblockexists = $PAGE->blocks->is_known_block_type('reportdashboard', false);
    if (!$reportdashboardblockexists) {
        $error = true;
        $errordata[] = get_string('enablereqplugins',  'block_learnerscript', 'LearnerScript Widget');
    }

    $reportdashboardblockexists = $PAGE->blocks->is_known_block_type('reporttiles', false);
    if (!$reportdashboardblockexists) {
        $error = true;
        $errordata[] = get_string('enablereqplugins',  'block_learnerscript', 'LearnerScript Report Tiles');
    }

    $lsreportconfigimport = get_config('block_learnerscript', 'lsreportconfigimport');
    if (!$error && $lsreportconfigimport) {
        throw new moodle_exception(get_string('configurestarted', 'block_learnerscript'));
    }
}

$renderer = $PAGE->get_renderer('block_learnerscript');
echo $OUTPUT->header();
$error = false;
if ($error) {
    echo $OUTPUT->box_start();
    foreach ($errordata as $errormsg) {
        echo html_writer::div($errormsg, "alert alert-error");;
    }
    echo $OUTPUT->box_end();
    echo html_writer::div(html_writer::link(new \moodle_url($CFG->wwwroot),
    html_writer::tag('button', get_string('continue', 'block_learnerscript'), [])), "text-center");
    echo $OUTPUT->footer();

    exit;
}
$importstatus = false;
$total = 0;
$current = 0;
$errorreportspositiondata = serialize([]);
$lastreportposition = 0;
if ($import) {
    $lsconfigreports = (new ls)->lsconfigreports();
    $importstatus = $lsconfigreports['importstatus'];
    $total = $lsconfigreports['total'];
    $current = $lsconfigreports['current'];
    $errorreportspositiondata = $lsconfigreports['errorreportspositiondata'];
    $lastreportposition = $lsconfigreports['lastreportposition'];
}
if ($importstatus && !$lsreportconfigstatus) {
    $pluginsettings = new block_learnerscript_license_setting('block_learnerscript/lsreportconfigimport',
                'lsreportconfigimport', get_string('lsreportconfigimport', 'block_learnerscript'), '', PARAM_INT, 2);
    $pluginsettings->config_write('lsreportconfigimport', 1);
}


$plottabs = new \block_learnerscript\output\lsconfig($status, $importstatus);
echo $renderer->render($plottabs);

if ($import) {
    (new ls)->importlsusertours();
}

$PAGE->requires->js_call_amd('block_learnerscript/lsreportconfig', 'init',
                                [['total' => $total,
                                            'current' => $current,
                                            'errorreportspositiondata' =>
                                            $errorreportspositiondata,
                                            'lastreportposition' => $lastreportposition,
                                        ], $status,
                                ]);

echo $OUTPUT->footer();
