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
 * @package block_totara_featured_links
 */

namespace block_totara_featured_links\tile;

use block_totara_featured_links\form\element\colorpicker;
use block_totara_featured_links\form\validator\is_color;
use block_totara_featured_links\form\validator\is_valid_course;
use totara_form\form\element\hidden;
use totara_form\form\element\text;
use totara_form\form\element\static_html;
use totara_form\form\element\select;

/**
 * Class course_form_content
 * Defines the content form for the course tile
 * @package block_totara_featured_links\tile
 */
class course_form_content extends base_form_content {

    /**
     * Defines the input for the course id.
     * @param \totara_form\group $group
     * @return null
     */
    public function specific_definition($group) {
        $course_name = empty($this->model->get_current_data('course_name')['course_name']) ? get_string('course_not_selected', 'block_totara_featured_links') : $this->model->get_current_data('course_name')
        ['course_name'];
        $course_name = $group->add(new static_html('course_name',
                get_string('course_name_label', 'block_totara_featured_links'),
                '<span id="course-name">'.$course_name.'</span>'
            )
        );
        $course_name->set_allow_xss(true);
        $course_name->add_validator(new is_valid_course());
        $course_hidden = $group->add(new hidden('course_name_id', PARAM_INT));
        $course_hidden->set_frozen(false);

        $select_course_button = $group->add(
            new static_html('select_course_button',
                '&nbsp;',
                '<input type="button" value="' . get_string('course_select', 'block_totara_featured_links') . '" id="show-course-dialog">'
            )
        );
        $select_course_button->set_allow_xss(true);

        $group->add(new select('heading_location', get_string('heading_location', 'block_totara_featured_links'), [
            'top' => get_string('top_heading', 'block_totara_featured_links'),
            'bottom' => get_string('bottom_heading', 'block_totara_featured_links')
        ]));

        $background = $group->add(
            new colorpicker(
                'background_color',
                get_string('tile_background_color', 'block_totara_featured_links'),
                PARAM_TEXT
            )
        );
        $background->add_validator(new is_color());
        return;
    }

    /**
     * Gets the requirements for the form
     * spectrum and autocomplemete
     */
    public function requirements () {
        parent::requirements();
        global $PAGE, $DB;
        $PAGE->requires->css(new \moodle_url('/blocks/totara_featured_links/spectrum/spectrum.css'));
        $PAGE->requires->strings_for_js(['less', 'clear_color', 'course_select'], 'block_totara_featured_links');
        $PAGE->requires->strings_for_js(['cancel', 'choose', 'more'], 'moodle');
        $PAGE->requires->js_call_amd('block_totara_featured_links/spectrum', 'spectrum');
        $PAGE->add_body_class('contains-spectrum-colorpicker');

        $courses = $DB->get_records('course', [], '', 'fullname');
        $course_names = [];
        foreach (array_values($courses) as $course) {
            $course_names[] = $course->fullname;
        }

        $markup = dialog_display_currently_selected('Currently Selected', 'course');
        $PAGE->requires->js_call_amd('block_totara_featured_links/course_dialog', 'init', ['instance', 'instanceid', sesskey(), $markup]);
    }

}