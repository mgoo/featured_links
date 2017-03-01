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


/**
 * Class course_form_content
 * Defines the content form for the course tile
 * @package block_featured_links\tile
 */
class course_form_content extends base_form_content {

    /**
     * Defines the input for the course id.
     * @param \moodleform $group
     * @return null
     */
    public function specific_definition(&$mform) {
        $mform->addElement('text', 'course_name_id', get_string('course_name_label', 'block_featured_links'));
        $mform->setType('course_name_id', PARAM_INT);

        $mform->addElement('select', 'heading_location', get_string('heading_location', 'block_featured_links'),[
            'top' => get_string('top_heading', 'block_featured_links'),
            'bottom' => get_string('bottom_heading', 'block_featured_links')
        ]);

        $mform->addElement('color', 'background_color', get_string('tile_background_color', 'block_featured_links'));
        $mform->setType('background_color', PARAM_TEXT);
        return;
    }

    /**
     * Gets the requirements for the form
     * spectrum and autocomplemete
     */
    public function requirements () {
        parent::requirements();
        global $PAGE, $DB;
        $PAGE->requires->css(new \moodle_url('/blocks/featured_links/spectrum/spectrum.css'));
        $PAGE->requires->strings_for_js(['less', 'clear_color', 'course_select'], 'block_featured_links');
        $PAGE->requires->strings_for_js(['cancel', 'choose', 'more'], 'moodle');
        $PAGE->requires->js_call_amd('block_featured_links/spectrum', 'spectrum');
        $PAGE->add_body_class('contains-spectrum-colorpicker');
    }

}