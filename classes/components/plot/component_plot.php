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

namespace block_learnerscript\components\plot;
use block_learnerscript\local\componentbase;

/**
 * Class component_plot
 *
 * @package    block_learnerscript
 * @copyright  2024 Moodle India Information Solutions Private Limited
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class component_plot extends componentbase {
    /**
     * @var bool $plugins Plugins
     */
    public $plugins;
    /**
     * @var bool $ordering Ordering
     */
    public $ordering;
    /**
     * @var bool $form Form
     */
    public $form;
    /**
     * @var bool $help Help
     */
    public $help;
    /**
     * Plot graphs initialization
     */
    public function init() {
        $this->plugins = true;
        $this->ordering = true;
        $this->form = false;
        $this->help = true;
    }
}
