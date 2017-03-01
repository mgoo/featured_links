<?php
/**
 * This file is part of Totara LMS
 *
 * Copyright (C) 2017 onwards Totara Learning Solutions LTD
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Andrew McGhie <andrew.mcghie@totaralearning.com>
 * @package block_featured_links
 */

namespace block_featured_links\tile;

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/formslib.php");

/**
 * Class base_form
 * each type of form should extend this class.
 * However plugin tile types should not extend this class
 * @package block_featured_links\tile
 */
abstract class base_form extends \moodleform{
    protected $tile;

    public function __construct($tile, $action = null, $customdata = null, $method = 'post', $target = '', $attributes = null, $editable = true, $ajaxformdata = null) {
        $this->tile = $tile;
        parent::__construct($action, $customdata, $method, $target, $attributes, $editable, $ajaxformdata);
    }

    /**
     * Defines the wrapping for the form defined in specific definition
     * makes tile type and position appear on every form
     */
    protected function definition(){

    }

    /**
     * gets the requirements for the form eg css and javascript
     * @return null
     */
    public function requirements() {
        return;
    }
}
