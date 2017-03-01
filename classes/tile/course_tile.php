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
 * This is the base class for the default tile it can be an example of how to do this in the tile mods
 * Class default_tile
 * @package block_featured_links\tile
 */
class course_tile extends base{
    protected $used_fields = ['courseid', // int The id of the course that the tile links to.
        'background_color', // string The hex value of the background color.
        'heading_location']; // string Where the heading is located 'top' or 'bottom'.
    protected $content_form = '\block_featured_links\tile\course_form_content';
    protected $content_template = 'block_featured_links/content';
    protected $content_class = 'block-featured-links-content block-featured-links-course';

    protected $course = '';

    /**
     * course_tile constructor.
     * @param null $tile
     */
    public function __construct($tile = null) {
        parent::__construct($tile);
        global $DB;
        if (!empty($this->data->courseid)) {
            $this->course = $DB->get_record('course', ['id' => $this->data->courseid]);
        }
    }

    /**
     * Does the custom adding of the tile
     * this tile however doesn't need anything run
     */
    public function add_tile() {

    }

    /**
     * returns the name and id in the right indexes
     * {@inheritdoc}
     */
    public function get_content_form_data() {
        $dataobj = parent::get_content_form_data();
        if (isset($this->course->fullname)) {
            $dataobj->course_name = $this->course->fullname;
        }
        if (isset($this->data_filtered->courseid)) {
            $dataobj->course_name_id = $this->data_filtered->courseid;
        }
        return $dataobj;
    }
    /**
     * returns the name of the tile that will be displayed
     * @return string NAME
     */
    public static function get_name() {
        return get_string('course_name', 'block_featured_links');
    }

    /**
     * Puts the data from the class in a way which the template can render
     * @return array
     */
    protected function get_content_template_data() {
        if (empty($this->data->courseid) || empty($this->course)) {
            return null;
        }
        return ['heading' => $this->course->fullname,
            'textbody' => false,
            'content_class' => (empty($this->content_class) ? '' : $this->content_class),
            'heading_location' => (empty($this->data_filtered->heading_location) ? '' : $this->data_filtered->heading_location),
            'notempty' => true
        ];
    }

    /**
     * Gets the data for the wrapper eg url and background color
     * @param \renderer_base $renderer
     * @return array
     */
    public function get_content_wrapper_template_data($renderer) {
        global $CFG;
        $data = parent::get_content_wrapper_template_data($renderer);
        $data['background_color'] = (!empty($this->data_filtered->background_color) ?
            $this->data_filtered->background_color :
            false);
        $data['alt_text'] = $this->get_accessibility_text();
        $data['url'] = (!empty($this->course) ? $CFG->wwwroot.'/course/view.php?id='.$this->course->id : false);
        return $data;
    }

    /**
     * moves a file from the draft area to a defined area
     * @param \stdClass $data
     * @return void
     */
    public function save_content_tile($data) {
        if (isset($data->course_name_id)) {
            $this->data->courseid = $data->course_name_id;
        }
        if (isset($data->heading_location)) {
            $this->data->heading_location = $data->heading_location;
        }
        if (isset($data->background_color)) {
            $this->data->background_color = $data->background_color;
        }
        return;
    }

    /**
     * Gets whether the tile is visible to the user by the custom rules defined by the tile.
     * This should only be used by the is_visible() function.
     * @return int (-1 = hidden, 0 = no rule, 1 = showing)
     */
    public function is_visible_tile() {
        return 0;
    }

    /**
     * Saves the data for the custom visibility.
     * Should only modify the custom_rules variable so the reset of the visibility and tile options are left the same
     * when its saved to the database
     * @param \stdClass $data all the data from the form
     * @return string
     */
    public function save_visibility_tile($data) {
        return '';
    }

    /**
     * Returns an array that the template will uses to put in text to help with accessibility
     * @return array
     */
    public function get_accessibility_text() {
        return ['sr-only' => get_string('course_sr-only', 'block_featured_links', !empty($this->course->fullname) ? $this->course->fullname : '')];
    }

}