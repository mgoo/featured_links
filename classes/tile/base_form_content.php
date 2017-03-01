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

use block_featured_links\lib\class_component;

defined('MOODLE_INTERNAL') || die();

/**
 * Class base_form_content
 * The base form for the content form
 * Plugin tile types should extend this form
 * @package block_featured_links\tile
 */
abstract class base_form_content extends base_form {

    /**
     * Defines the main part of the form
     * which basically includes ordering and tile type
     */
    public function definition() {
        global $DB, $CFG;
        $GLOBALS['HTML_QUICKFORM_ELEMENT_TYPES']['color'] = [$CFG->dirroot.'/blocks/featured_links/classes/form/element/color.php', 'block_featured_links\form\element\color'];
        $GLOBALS['HTML_QUICKFORM_ELEMENT_TYPES']['number'] = [$CFG->dirroot.'/blocks/featured_links/classes/form/element/number.php', 'block_featured_links\form\element\number'];
        $mform = $this->_form;
        $mform->disable_form_change_checker();

        $mform->addElement('header', 'form_header', get_string('content_edit', 'block_featured_links'));

        $classes = class_component::get_namespace_classes('tile', 'block_featured_links\tile\base');
        $class_options = [];
        foreach ($classes as $class_str) {
            $class_arr = explode('\\', $class_str);
            $plugin_name = $class_arr[0];
            $class_name = $class_arr[count($class_arr) - 1];
            $class_options[$plugin_name.'-'.$class_name] = $class_str::get_name();
        }

        $mform->addElement('select', 'type', get_string('tile_types', 'block_featured_links'), $class_options);

        $this->specific_definition($mform);

        $max = $DB->count_records('block_featured_links_tiles', ['blockid' => $this->tile->blockid]);
        if ($DB->count_records('block_featured_links_tiles', ['id' => $this->tile->id]) == 0) {
            $max++;
        }
        $mform->addElement('number', 'sortorder', get_string('tile_position', 'block_featured_links'), ['type' => 'number', 'max' => max(1, $max), 'min' => 1]);
        $mform->setType('sortorder', PARAM_INT);
        $this->add_action_buttons();
    }

    /**
     * Overrides the get_action_url function so that the parameters can be added to the form even when its being submitted.
     * Needed as the php script requires that the block and tile ids are set
     * @return \moodle_url
     */
    public function get_action_url() {
        return new \moodle_url(
            '/blocks/featured_links/edit_tile_content.php',
            $this->get_parameters()
        );
    }

    /**
     * Gets the requirements for the form
     */
    public function requirements() {

    }

    public abstract function specific_definition(&$mfom);
}