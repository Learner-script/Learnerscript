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

namespace block_learnerscript\components\permissions\roleincourse;
use block_learnerscript\local\pluginbase;
use block_learnerscript\local\permissionslib;
use context_helper;

/**
 * Class plugin for user roles premission.
 *
 * @package    block_learnerscript
 * @copyright  2024 Moodle India Information Solutions Private Limited
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class plugin extends pluginbase {

    /** @var $role */
    public $role;

    /**
     * @var array $userroles User role
     */
    public $userroles;

    /**
     * Role in course
     *
     */
    public function init() {
        $this->form = true;
        $this->unique = false;
        $this->fullname = get_string('roleincourse', 'block_learnerscript');
        $this->reporttypes = ['courses', 'sql', 'users', 'statistics',
        'coursesoverview', 'usercourses', 'grades', 'useractivities',
        'userbadges', 'userprofile', 'noofviews',
        'courseprofile', 'courseactivities', 'courseviews', ];
    }

    /**
     * Summary
     * @param  object $data Columns data
     * @return string
     */
    public function summary($data) {
        global $DB;
        $rolename = $DB->get_field('role', 'shortname', ['id' => $data->roleid]);
        $contextname = context_helper::get_level_name($data->contextlevel);
        return $rolename . ' at ' . $contextname .' level';
    }

    /**
     * Execute
     * @param  int $userid  User id
     * @param  object $context User context
     * @param  object $data    Report columns data
     * @return boolean
     */
    public function execute($userid, $context, $data) {
        global $DB;
        $permissions = (isset($this->reportclass->componentdata->permissions))
        ? $this->reportclass->componentdata->permissions : [];
        if (!empty($this->role)) {
            $currentroleid = $DB->get_field('role', 'id', ['shortname' => $this->role]);
            $return = [];
            foreach ($permissions->elements as $p) {
                $currentroleid = $DB->get_field('role', 'id', ['shortname' => $this->role]);
                if ($p->pluginname == 'roleincourse'
                && isset($p->formdata->contextlevel)
                && $p->formdata->roleid == $currentroleid) {
                    $permissionslib = new permissionslib($p->formdata->contextlevel,
                    $p->formdata->roleid, $userid);
                    if (has_capability('block/learnerscript:reportsaccess', $context)) {
                            $return[] = true;
                    }
                }
            }
            return in_array(true, $return);
        }
        return false;
    }
}
