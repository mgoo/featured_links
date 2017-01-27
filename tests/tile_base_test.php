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
 * Tests the static methods on the abstract \block_totara_featured_links\tile\base class
 */
class block_totara_featured_links_tile_base_testcase extends advanced_testcase {

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
     * Tests the \block_totara_featured_links\tile\base::get_tile_class() method.
     */
    public function test_get_tile_class() {
        $this->resetAfterTest(); // Changing the database, we must reset.

        // First up test with a real id.
        $blockinstance = $this->blockgenerator->create_instance();
        $tile = $this->blockgenerator->create_default_tile($blockinstance->id);
        $expected = '\block_totara_featured_links\tile\default_tile';
        $this->assertInstanceOf($expected, \block_totara_featured_links\tile\base::get_tile_class($tile->id));

        // Now test with an id that can't possibly exist.
        $this->setExpectedException('dml_missing_record_exception', 'Can not find data record in database');
        \block_totara_featured_links\tile\base::get_tile_class(-1);
    }

    /**
     * Tests the \block_totara_featured_links\tile\base::squash_ordering() method.
     */
    public function test_squash_ordering() {
        global $DB;
        $this->resetAfterTest(); // Changing the database, we must reset.

        $blockinstance1 = $this->blockgenerator->create_instance();
        $blockinstance2 = $this->blockgenerator->create_instance();

        $tile1_a = $this->blockgenerator->create_default_tile($blockinstance1->id);
        $tile1_b = $this->blockgenerator->create_default_tile($blockinstance1->id);
        $tile1_c = $this->blockgenerator->create_default_tile($blockinstance1->id);

        $tile2_a = $this->blockgenerator->create_default_tile($blockinstance2->id);
        $tile2_b = $this->blockgenerator->create_default_tile($blockinstance2->id);
        $tile2_c = $this->blockgenerator->create_default_tile($blockinstance2->id);

        // Test that both blocks have their tiles in the correct order.
        $this->assertEquals(1, $tile1_a->sort);
        $this->assertEquals(2, $tile1_b->sort);
        $this->assertEquals(3, $tile1_c->sort);

        $this->assertEquals(1, $tile2_a->sort);
        $this->assertEquals(2, $tile2_b->sort);
        $this->assertEquals(3, $tile2_c->sort);

        // Now mess them up.
        // We do this directly in the database as we only want to test the base method, and not the blocks sort method.
        $DB->set_field('block_featured_tiles', 'sort', '5', ['id' => $tile1_a->id]);
        $DB->set_field('block_featured_tiles', 'sort', '9', ['id' => $tile1_b->id]);
        $DB->set_field('block_featured_tiles', 'sort', '7', ['id' => $tile1_c->id]);
        $DB->set_field('block_featured_tiles', 'sort', '8', ['id' => $tile2_a->id]);
        $DB->set_field('block_featured_tiles', 'sort', '6', ['id' => $tile2_b->id]);
        $DB->set_field('block_featured_tiles', 'sort', '4', ['id' => $tile2_c->id]);

        // Refresh the objects and confirm the sort.
        $tile1_a = new \block_totara_featured_links\tile\default_tile($tile1_a->id);
        $tile1_b = new \block_totara_featured_links\tile\default_tile($tile1_b->id);
        $tile1_c = new \block_totara_featured_links\tile\default_tile($tile1_c->id);

        $tile2_a = new \block_totara_featured_links\tile\default_tile($tile2_a->id);
        $tile2_b = new \block_totara_featured_links\tile\default_tile($tile2_b->id);
        $tile2_c = new \block_totara_featured_links\tile\default_tile($tile2_c->id);

        $this->assertEquals(5, $tile1_a->sort);
        $this->assertEquals(9, $tile1_b->sort);
        $this->assertEquals(7, $tile1_c->sort);

        $this->assertEquals(8, $tile2_a->sort);
        $this->assertEquals(6, $tile2_b->sort);
        $this->assertEquals(4, $tile2_c->sort);

        // Now run squash ordering on the second block instance.
        \block_totara_featured_links\tile\base::squash_ordering($blockinstance2->id);

        // Refresh the objects and confirm the sort.
        $tile1_a = new \block_totara_featured_links\tile\default_tile($tile1_a->id);
        $tile1_b = new \block_totara_featured_links\tile\default_tile($tile1_b->id);
        $tile1_c = new \block_totara_featured_links\tile\default_tile($tile1_c->id);

        $tile2_a = new \block_totara_featured_links\tile\default_tile($tile2_a->id);
        $tile2_b = new \block_totara_featured_links\tile\default_tile($tile2_b->id);
        $tile2_c = new \block_totara_featured_links\tile\default_tile($tile2_c->id);

        $this->assertEquals(5, $tile1_a->sort);
        $this->assertEquals(9, $tile1_b->sort);
        $this->assertEquals(7, $tile1_c->sort);

        $this->assertEquals(3, $tile2_a->sort);
        $this->assertEquals(2, $tile2_b->sort);
        $this->assertEquals(1, $tile2_c->sort);

        // Now run squash ordering on the first block instance.
        \block_totara_featured_links\tile\base::squash_ordering($blockinstance1->id);

        // Refresh the objects and confirm the sort.
        $tile1_a = new \block_totara_featured_links\tile\default_tile($tile1_a->id);
        $tile1_b = new \block_totara_featured_links\tile\default_tile($tile1_b->id);
        $tile1_c = new \block_totara_featured_links\tile\default_tile($tile1_c->id);

        $tile2_a = new \block_totara_featured_links\tile\default_tile($tile2_a->id);
        $tile2_b = new \block_totara_featured_links\tile\default_tile($tile2_b->id);
        $tile2_c = new \block_totara_featured_links\tile\default_tile($tile2_c->id);

        $this->assertEquals(1, $tile1_a->sort);
        $this->assertEquals(3, $tile1_b->sort);
        $this->assertEquals(2, $tile1_c->sort);

        $this->assertEquals(3, $tile2_a->sort);
        $this->assertEquals(2, $tile2_b->sort);
        $this->assertEquals(1, $tile2_c->sort);
    }

    /**
     * Tests the \block_totara_featured_links\tile\base::squash_ordering() method on a block
     * instance with no tiles.
     *
     * This is a simple test, there are no tiles, so there is no action taken.
     * We are really just testing that it doesn't error!
     */
    public function test_squash_ordering_without_tiles() {
        $this->resetAfterTest(); // Changing the database, we must reset.

        $blockinstance = $this->blockgenerator->create_instance();
        \block_totara_featured_links\tile\base::squash_ordering($blockinstance->id);
    }

    /**
     * Tests the \block_totara_featured_links\tile\base::squash_ordering() method on a block
     * instance with tiles which have the same sortorder.
     */
    public function test_squash_ordering_with_duplicate_sort_values() {
        global $DB;
        $this->resetAfterTest(); // Changing the database, we must reset.

        $blockinstance = $this->blockgenerator->create_instance();
        $tile1 = $this->blockgenerator->create_default_tile($blockinstance->id);
        $tile2 = $this->blockgenerator->create_default_tile($blockinstance->id);
        $tile3 = $this->blockgenerator->create_default_tile($blockinstance->id);

        $DB->set_field('block_featured_tiles', 'sort', '5', ['id' => $tile1->id]);
        $DB->set_field('block_featured_tiles', 'sort', '5', ['id' => $tile2->id]);
        $DB->set_field('block_featured_tiles', 'sort', '1', ['id' => $tile3->id]);

        // Refresh the objects and confirm the sort.
        $tile1 = new \block_totara_featured_links\tile\default_tile($tile1->id);
        $tile2 = new \block_totara_featured_links\tile\default_tile($tile2->id);
        $tile3 = new \block_totara_featured_links\tile\default_tile($tile3->id);

        $this->assertEquals(5, $tile1->sort);
        $this->assertEquals(5, $tile2->sort);
        $this->assertEquals(1, $tile3->sort);

        \block_totara_featured_links\tile\base::squash_ordering($blockinstance->id);

        // Refresh the objects.
        $tile1 = new \block_totara_featured_links\tile\default_tile($tile1->id);
        $tile2 = new \block_totara_featured_links\tile\default_tile($tile2->id);
        $tile3 = new \block_totara_featured_links\tile\default_tile($tile3->id);

        // We don't know the exact sortorder now, what is important is that we no longer have a duplicate.
        // So check that the sortorders are unique.
        $sortorders = [
            $tile1->sort,
            $tile2->sort,
            $tile3->sort,
        ];
        // This basically calls array_unique to remove duplicates. If there are duplicates then the number of
        // values will decrease and will no longer match the number in the original array.
        $this->assertCount(count($sortorders), array_unique($sortorders));
    }

    /**
     * Tests the base::get_name() method.
     */
    public function test_get_name() {
        $this->setExpectedException('Exception', 'Please Override this function');
        \block_totara_featured_links\tile\base::get_name();
    }
}