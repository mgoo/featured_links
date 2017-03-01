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
require_once('test_helper.php');


defined('MOODLE_INTERNAL') || die();

/**
 * Class block_featured_links_tile_course_tile_testcase
 * Test the course_tile class
 */
class block_featured_links_tile_course_tile_testcase extends test_helper {
    /**
     * The block generator instance for the test.
     * @var block_featured_links_generator $generator
     */
    protected $blockgenerator;

    /**
     * Gets executed before every test case.
     */
    public function setUp() {
        parent::setUp();
        $this->blockgenerator = $this->getDataGenerator()->get_plugin_generator('block_featured_links');
    }

    /**
     * Makes sure that the course id is saved to the database.
     */
    public function test_save_content_tile() {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();
        $instance = $this->blockgenerator->create_instance();
        $tile1 = $this->blockgenerator->create_course_tile($instance->id);
        $course = $this->getDataGenerator()->create_course();
        $data = new \stdClass();
        $data->type = 'block_featured_links-default_tile';
        $data->sortorder = 4;
        $data->course_name = $course->fullname;
        $data->course_name_id = $course->id;
        $tile1->save_content($data);
        $this->assertEquals($course->id, json_decode($DB->get_field('block_featured_links_tiles', 'dataraw', ['id' => $tile1->id]))->courseid);
    }

    /**
     * Checks that the course is rendered with the tile.
     */
    public function test_render_course() {
        global $PAGE;
        $PAGE->set_url('/');
        $this->resetAfterTest();
        $this->setAdminUser();
        $instance = $this->blockgenerator->create_instance();
        $tile1 = $this->blockgenerator->create_course_tile($instance->id);
        $course = $this->getDataGenerator()->create_course();
        $data = new \stdClass();
        $data->type = 'block_featured_links-course_tile';
        $data->sortorder = 4;
        $data->course_name = $course->fullname;
        $data->course_name_id = $course->id;
        $data->background_color = '#FFFFFF';
        $tile1->save_content($data);
        $tile_reload = \block_featured_links\tile\base::get_tile_instance($tile1->id); // Load the course data.

        $content = $tile_reload->render_content_wrapper($PAGE->get_renderer('core'), []);
        $this->assertStringStartsWith('<div', $content);
        $this->assertStringEndsWith('</div>', $content);
        $this->assertContains('Test course 1', $content);
    }
}