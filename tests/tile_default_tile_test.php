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

defined('MOODLE_INTERNAL') || die();

/**
 * Tests the methods on the \block_totara_featured_links\tile\default_tile class
 */
class block_totara_featured_links_tile_default_tile_testcase extends advanced_testcase {

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
     * Tests the \block_totara_featured_links\tile\default_tile::add_tile() method.
     */
    public function test_add_tile() {
        $this->resetAfterTest();

        $blockinstance = $this->blockgenerator->create_instance();
        $tile_class = $this->blockgenerator->create_default_tile($blockinstance->id);
        // Check that the tile is the right type of object.
        $this->assertInstanceOf('\block_totara_featured_links\tile\default_tile', $tile_class);
        // Makes sure that this is the first tile.
        $this->assertEquals(1, $tile_class->sort);
    }

    /**
     * Tests the \block_totara_featured_links\tile\default_tile::add_tile() method
     * Where the block id passed to the method is incorrect
     */
    public function test_add_tile_no_id() {
        $this->resetAfterTest();

        $this->blockgenerator->create_instance();
        $this->setExpectedException('Exception', 'The Block instance id was not not found');
        $this->blockgenerator->create_default_tile(-1);

        // Make sure you cant put random values at the constructor.
        $this->setExpectedException('dml_missing_record_exception');
        new \block_totara_featured_links\tile\default_tile(-1);
    }

    /**
     * Tests the \block_totara_featured_links\tile\default_tile::edit_content_form() method
     * Also makes sure that you can't pass dumb stuff to it
     */
    public function test_edit_content_form() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $blockinstance = $this->blockgenerator->create_instance();
        $tile_class = $this->blockgenerator->create_default_tile($blockinstance->id);

        // Refresh the tile_class object.
        $tile_class = new \block_totara_featured_links\tile\default_tile($tile_class->id);

        $this->setExpectedException('Exception', 'The block for the tile was not found');
        $tile_class->edit_content_form(['blockinstanceid' => -1, 'tileid' => -1]);

        $edit_form = $tile_class->edit_content_form(['blockinstanceid' => $blockinstance->id, 'tileid' => $tile_class->id]);

        $this->assertInstanceOf('\block_totara_featured_links\tile\base_form_content', $edit_form);
    }

    /**
     * Tests the \block_totara_featured_links\tile\default_tile::edit_auth_form() method
     * Also makes sure that you can't pass dumb stuff to it
     */
    public function test_edit_auth_form() {
        $this->resetAfterTest();
        $this->setAdminUser();

        $blockinstance = $this->blockgenerator->create_instance();
        $tile_class = $this->blockgenerator->create_default_tile($blockinstance->id);
        $tile_class = new \block_totara_featured_links\tile\default_tile($tile_class->id);

        $this->setExpectedException('Exception', 'The block for the tile was not found');
        $tile_class->edit_content_form(['blockinstanceid' => -1, 'tileid' => -1]);
        $this->setExpectedException('Exception', 'The tile was not found');
        $tile_class->edit_content_form(['blockinstanceid' => $blockinstance->id, 'tileid' => -1]);

        $edit_form = $tile_class->edit_auth_form(['blockinstanceid' => $blockinstance->id, 'tileid' => $tile_class->id]);

        $this->assertInstanceOf('\block_totara_featured_links\tile\base_form_auth', $edit_form);
    }
}