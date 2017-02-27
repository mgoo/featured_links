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

require_once('test_helper.php');

defined('MOODLE_INTERNAL') || die();

/**
 * Tests the Totara featured links block.
 */
class block_totara_featured_links_block_totara_featured_links_testcase extends test_helper {

    /**
     * The block generator instance for the test.
     * @var block_totara_featured_links_generator $generator
     */
    protected $blockgenerator;

    /**
     * Gets executed before every test case.
     */
    public function setUp() {
        parent::setUp();
        $this->blockgenerator = $this->getDataGenerator()->get_plugin_generator('block_totara_featured_links');
    }

    /**
     * Tests the delete instance method of the block cleans up all related data.
     */
    public function test_instance_delete() {
        global $DB;

        $this->resetAfterTest();

        $this->assertEquals(
            0,
            $DB->count_records('block_instances', ['blockname' => 'totara_featured_links']),
            'Unexpected Totara featured links block instance found.'
        );

        $instance1 = $this->blockgenerator->create_instance();
        $instance2 = $this->blockgenerator->create_instance();
        $this->blockgenerator->create_default_tile($instance1->id);
        $this->blockgenerator->create_default_tile($instance1->id);
        $this->blockgenerator->create_default_tile($instance2->id);
        $this->blockgenerator->create_default_tile($instance2->id);
        $this->blockgenerator->create_default_tile($instance2->id);

        $this->assertEquals(2, $DB->count_records('block_instances', ['blockname' => 'totara_featured_links']));
        $this->assertEquals(2, $DB->count_records('block_totara_featured_links_tiles', ['blockid' => $instance1->id]));
        $this->assertEquals(3, $DB->count_records('block_totara_featured_links_tiles', ['blockid' => $instance2->id]));

        // To delete the block we use the block API, this will in turn be expected to call >instance_delete().
        blocks_delete_instance($instance1);

        $this->assertEquals(1, $DB->count_records('block_instances', ['blockname' => 'totara_featured_links']));
        $this->assertEquals(0, $DB->count_records('block_totara_featured_links_tiles', ['blockid' => $instance1->id]));
        $this->assertEquals(3, $DB->count_records('block_totara_featured_links_tiles', ['blockid' => $instance2->id]));
    }

    /**
     * Tests getting the tiles for a block
     * Makes sure they are in the correct order
     */
    public function test_get_tiles() {
        $this->resetAfterTest();

        $instance = $this->blockgenerator->create_instance();
        $this->blockgenerator->create_default_tile($instance->id);
        $this->blockgenerator->create_default_tile($instance->id);
        $this->blockgenerator->create_default_tile($instance->id);

        $block = block_instance('totara_featured_links', $instance);
        $this->assertInstanceOf('block_totara_featured_links', $block);

        // Make the get_tiles method accessible so that we can call it.
        $method = new \ReflectionMethod($block, 'get_tiles');
        $method->setAccessible(true);
        /* @var block_totara_featured_links\tile\base[] $tiles */
        $tiles = $method->invoke($block);

        $this->assertCount(3, $tiles);

        $sortorder = [];
        foreach ($tiles as $tile) {
            $sortorder[] = $tile->sortorder;
        }
        $sortedsortorder = $sortorder;
        // This should not change it.
        sort($sortedsortorder);
        $this->assertSame($sortedsortorder, $sortorder, 'The sort order of the tiles was not correct after being returned from get_tiles.');
    }

    /**
     * Makes sure getting tiles in an empty block
     */
    public function test_get_tiles_empty_block() {
        $this->resetAfterTest();

        $instance = $this->blockgenerator->create_instance();
        $block = block_instance('totara_featured_links', $instance);
        $method = new \ReflectionMethod($block, 'get_tiles');
        $method->setAccessible(true);

        $tiles = $method->invoke($block);

        $this->assertCount(0, $tiles);
    }

    /**
     * tests that the block instance can be saved
     */
    public function test_instance_config_save () {
        $this->resetAfterTest();
        $instance = $this->blockgenerator->create_instance();
        $this->blockgenerator->create_default_tile($instance->id);

        $block = block_instance('totara_featured_links', $instance);
        $this->assertInstanceOf('block_totara_featured_links', $block);

        $block->instance_create();

        $data = new \stdClass();
        $data->title = '';
        $data->size = 'small';
        $data->manual_id = 'Some_random_id';

        $block->instance_config_save($data);

        $this->assertTrue($block->hide_header());
    }

    public function test_intance_copy() {
        global $DB;
        $this->resetAfterTest();
        $this->setAdminUser();
        $instance_1 = $this->blockgenerator->create_instance();
        $instance_2 = $this->blockgenerator->create_instance();
        $block_1 = block_instance('totara_featured_links', $instance_1);
        $block_2 = block_instance('totara_featured_links', $instance_2);

        $gallery_tile = $this->blockgenerator->create_gallery_tile($instance_1->id);
        $default_tile = $this->blockgenerator->create_default_tile($instance_1->id);
        $course_tile = $this->blockgenerator->create_course_tile($instance_1->id);
        $course = $this->getDataGenerator()->create_course();

        $gallery_data = new \stdClass();
        $gallery_data->heading = 'this is the heading';
        $gallery_data->url = 'http://www.example.com';
        $gallery_data->textbody = 'this is the text body';
        $gallery_data->heading_location = 'bottom';
        $gallery_data->type = 'block_totara_featured_links-gallery_tile';
        $gallery_data->alt_text = 'This is the alternative text';
        $gallery_data->sortorder = '2';
        $gallery_data->background_color = '#ff0000';

        $default_data = new \stdClass();
        $default_data->heading = 'this is the default heading';
        $default_data->url = 'http://www.example.com';
        $default_data->textbody = 'this is the default text body';
        $default_data->heading_location = 'bottom';
        $default_data->type = 'block_totara_featured_links-default_tile';
        $default_data->alt_text = 'This is the alternative text';
        $default_data->sortorder = '1';
        $default_data->background_color = '#00ff00';

        $course_data = new \stdClass();
        $course_data->course_name_id = $course->id;
        $course_data->heading_location = 'top';
        $course_data->type = 'block_totara_featured_links-course_tile';
        $course_data->sortorder = '3';
        $course_data->background_color = '#0000ff';

        $gallery_tile->save_content($gallery_data);
        $default_tile->save_content($default_data);
        $course_tile->save_content($course_data);

        $this->refresh_tiles($gallery_tile, $default_tile, $course_tile);

        $this->setGuestUser();
        $block_2->instance_copy($instance_1->id);

        $new_tiles = $DB->get_records('block_totara_featured_links_tiles', ['blockid' => (string)$instance_2->id]);

        $data = ['block_totara_featured_links-gallery_tile' => $gallery_tile,
            'block_totara_featured_links-default_tile' => $default_tile,
            'block_totara_featured_links-course_tile' => $course_tile];

        foreach ($new_tiles as $tile) {
            $this->assertSame($data[$tile->type]->dataraw, $tile->dataraw);
            $this->assertSame($data[$tile->type]->type, $tile->type);
            $this->assertNotEquals($data[$tile->type]->userid, $tile->userid);
            $this->assertSame($data[$tile->type]->sortorder, $tile->sortorder);
            $this->assertSame($data[$tile->type]->timecreated, $tile->timecreated);
            $this->assertSame($data[$tile->type]->visibility, $tile->visibility);
            $this->assertSame($data[$tile->type]->audienceaggregation, $tile->audienceaggregation);
            $this->assertSame($data[$tile->type]->presetsraw, $tile->presetsraw);
            $this->assertSame($data[$tile->type]->presetsaggregation, $tile->presetsaggregation);
            $this->assertSame($data[$tile->type]->overallaggregation, $tile->overallaggregation);
            $this->assertSame($data[$tile->type]->tilerules, $tile->tilerules);
            $this->assertSame($data[$tile->type]->audienceshowing, $tile->audienceshowing);
            $this->assertSame($data[$tile->type]->presetshowing, $tile->presetshowing);
            $this->assertSame($data[$tile->type]->tilerulesshowing, $tile->tilerulesshowing);
        }
    }
}