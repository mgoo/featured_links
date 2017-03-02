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

/**
 * Class course_form_content
 * Defines the content form for the course tile
 * @package block_featured_links\tile
 */
class course_form_content extends base_form_content {

    /**
     * Defines the input for the course id.
     * @param \moodleform $mform
     * @return null
     */
    public function specific_definition(&$mform) {
        global $DB;
        $courses = $DB->get_records('course', [], '', 'fullname,id');
        $course_options = [];
        foreach($courses as $course){
            $course_options[$course->id] = $course->fullname;
        }

        $mform->addElement('autocomplete', 'course_name_id', get_string('course_name_label', 'block_featured_links'), $course_options, []);

       $mform->addElement('select', 'heading_location', get_string('heading_location', 'block_featured_links'),[
            'top' => get_string('top_heading', 'block_featured_links'),
            'bottom' => get_string('bottom_heading', 'block_featured_links')
        ]);

        $mform->addElement('color', 'background_color', get_string('tile_background_color', 'block_featured_links'));
        $mform->setType('background_color', PARAM_TEXT);
        $mform->addRule('background_color', get_string('color_error', 'block_featured_links'), 'is_color');
    }

    /**
     * Gets the requirements for the form
     * spectrum and autocomplemete
     */
    public function requirements () {
        parent::requirements();
    }

}