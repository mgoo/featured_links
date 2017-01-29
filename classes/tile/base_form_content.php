<?php
/**
 * This file is part of Totara LMS
 *
 * Copyright (C) 2010 onwards Totara Learning Solutions LTD
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
 *
 *
 */



namespace block_featured_links\tile;

defined('MOODLE_INTERNAL') || die();

//use \core\mmoodleform;
global $CFG;
require_once("$CFG->libdir/formslib.php");

/**
 * Class base_form_content
 * The base form for the content form
 * Plugin tile types should extend this form
 * @package block_featured_links\tile
 */
abstract class base_form_content extends \moodleform {
    protected $tile;

    public function __construct($tile, $action = null, $customdata = null, $method = 'post', $target = '', $attributes = null, $editable = true) {
        $this->tile = $tile;
        parent::__construct($action, $customdata, $method, $target, $attributes, $editable);
    }


    public function requirements() {
        
    }

    /**
     * Defines the main part of the form
     * which basically includes ordering and tile type
     */
    public function definition () {
        global $DB, $CFG;
        $mform = $this->_form;
        $mform->disable_form_change_checker();
        $GLOBALS['HTML_QUICKFORM_ELEMENT_TYPES']['number'] = [$CFG->dirroot.'/blocks/featured_links/classes/form/element/number.php', 'block_featured_links\form\element\number'];

        $mform->addElement('header', 'form_header', get_string('content_edit', 'block_featured_links'));

        $classes_arr = \block_featured_links\lib\class_component::get_namespace_classes('tile', 'block_featured_links\tile\base');
        $classes  = [];
        foreach($classes_arr as $class){
            if(is_subclass_of($class, base::get_class())){
                $classes[] = $class;
            }
        }

        $class_options = [];
        foreach ($classes as $class) {
            $class_options["\\" . $class] = $class::get_name();
        }

        $mform->addElement('select', 'type', get_string('tile_types', 'block_featured_links'), $class_options);

        $this->specific_definition($mform);

        $max = $DB->count_records('block_featured_tiles', ['blockid' => $this->_customdata->blockid]);
        if ($DB->count_records('block_featured_tiles', ['id' => $this->tile->id]) == 0) {
            $max++;
        }
        $mform->addElement('number', 'sort', get_string('tile_position', 'block_featured_links'), ['type' => 'number', 'max' => ($max == 0 ? 1 : $max), 'min' => 1]);
        $mform->addRule('sort', get_string('error'), 'numeric');
        $mform->addRule('sort', get_string('error'), 'required', true);
        $mform->setType('sort', PARAM_INT);

        $this->add_action_buttons();
    }
}