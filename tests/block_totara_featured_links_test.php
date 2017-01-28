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

defined('MOODLE_INTERNAL') || die();

/**
 * Tests the Totara featured links block.
 */
class block_featured_links_testcase extends advanced_testcase {

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
     * Tests the delete instance method of the block cleans up all related data.
     */
    public function test_instance_delete() {
        global $DB;

        $this->resetAfterTest();

        $this->assertEquals(
            0,
            $DB->count_records('block_instances', ['blockname' => 'featured_links']),
            'Unexpected Totara featured links block instance found.'
        );

        $instance1 = $this->blockgenerator->create_instance();
        $instance2 = $this->blockgenerator->create_instance();
        $this->blockgenerator->create_default_tile($instance1->id);
        $this->blockgenerator->create_default_tile($instance1->id);
        $this->blockgenerator->create_default_tile($instance2->id);
        $this->blockgenerator->create_default_tile($instance2->id);
        $this->blockgenerator->create_default_tile($instance2->id);

        $this->assertEquals(2, $DB->count_records('block_instances', ['blockname' => 'featured_links']));
        $this->assertEquals(2, $DB->count_records('block_featured_tiles', ['blockid' => $instance1->id]));
        $this->assertEquals(3, $DB->count_records('block_featured_tiles', ['blockid' => $instance2->id]));

        // To delete the block we use the block API, this will in turn be expected to call >instance_delete().
        blocks_delete_instance($instance1);

        $this->assertEquals(1, $DB->count_records('block_instances', ['blockname' => 'featured_links']));
        $this->assertEquals(0, $DB->count_records('block_featured_tiles', ['blockid' => $instance1->id]));
        $this->assertEquals(3, $DB->count_records('block_featured_tiles', ['blockid' => $instance2->id]));
    }

}