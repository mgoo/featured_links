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

namespace block_totara_featured_links;

defined('MOODLE_INTERNAL') || die();

/**
 * Class block_totara_featured_links_external
 * This has the functions that will be called by AJAX
 */
class external extends \external_api{

    /**
     * remove_tile function will remove a tile from a block from ajax making it so that the whole page doesn't have to be reloaded.
     * @return \external_function_parameters
     */
    public static function remove_tile_parameters() {
        return new \external_function_parameters(
            [
                'tileid' => new \external_value(PARAM_INT, 'The tile to be remove')
            ]
        );
    }

    /**
     * Removes a tile
     * @param int $tileid
     * @return bool
     */
    public static function remove_tile($tileid) {
        self::validate_parameters(self::remove_tile_parameters(), ['tileid' => $tileid]);
        global $DB, $USER;
        if (!$DB->record_exists('block_totara_featured_links_tiles', ['id' => $tileid])) {
            return false;
        }
        $tile_instance = \block_totara_featured_links\tile\base::get_tile_instance($tileid);
        // Checks that the inputs are valid and the right capabilities exist.
        $context = \context_block::instance($tile_instance->blockid);
        \external_api::validate_context($context);
        // Checks the user has the correct permissions.
        if (!$tile_instance->can_edit_tile()) {
            print_error('cannot_edit_tile', 'block_totara_featured_links');
        }
        $parentcontext = $context->get_parent_context();
        if ($parentcontext->contextlevel == CONTEXT_COURSECAT) {
            // Check if category is visible and user can view this category.
            $category = $DB->get_record('course_categories', ['id' => $parentcontext->instanceid], '*', MUST_EXIST);
            if (!$category->visible) {
                require_capability('moodle/category:viewhiddencategories', $parentcontext);
            }
        } else if ($parentcontext->contextlevel == CONTEXT_USER && $parentcontext->instanceid != $USER->id) {
            throw new \coding_exception('You do not have permissions to remove the tile');
        }
        return $tile_instance->remove_tile();
    }

    /**
     * Return type of the remove_tile function
     * @return \external_value
     */
    public static function remove_tile_returns() {
        return new \external_value(PARAM_BOOL, "This will return whether then tile was successfully removed");
    }

    /**
     * Parameters of the add_audience_list_item function
     * @return \external_function_parameters
     */
    public static function add_audience_list_item_parameters() {
        return new \external_function_parameters(
            ['audienceid' => new \external_value(PARAM_INT, 'The id of the audience to add')]
        );
    }

    /**
     * renders the HTML code for a list item in the audience list
     * @param $audienceid
     * @return string
     */
    public static function add_audience_list_item($audienceid) {
        global $DB;
        self::validate_parameters(self::add_audience_list_item_parameters(), ['audienceid' => $audienceid]);

        $audience = $DB->get_record('cohort', ['id' => $audienceid], 'contextid', MUST_EXIST);
        $context = \context::instance_by_id($audience->contextid);
        \external_api::validate_context($context);
        require_capability('moodle/cohort:view', $context);

        $data = \block_totara_featured_links\form\element\audience_list::get_audience_data($audienceid);
        return \block_totara_featured_links\form\element\audience_list::render_row($audienceid, $data['name'], $data['learners']);
    }

    /**
     * Return type of the add_audience_list_item function
     * @return \external_value
     */
    public static function add_audience_list_item_returns() {
        return new \external_value(PARAM_CLEANHTML, 'The HTML code for the list item');
    }

    /**
     * Parameters for the reorder tiles method.
     * requires a json encoded string of the array containing the order of the tiles
     * @return \external_function_parameters
     */
    public static function reorder_tiles_parameters() {
        return new \external_function_parameters(
            [
                'tiles' => new \external_multiple_structure(
                    new \external_value(PARAM_ALPHANUMEXT, 'The tiles to be ordered')
                )
            ]
        );
    }

    /**
     * reorders the tiles to the ordering in the JSON array passes.
     * The JSON array must be of some strings that have the id of the tile row in the database last
     * @param $tiles
     */
    public static function reorder_tiles($tiles) {
        global $DB;
        self::validate_parameters(self::reorder_tiles_parameters(), ['tiles' => $tiles]);
        if (count($tiles) <= 1) {
            return;
        }
        $tiles_to_sort = [];
        // Make sure all the tiles are valid before saving.
        foreach ($tiles as $sortorder => $value) {
            $matches = [];
            if (empty(preg_match('/[0-9]+$/', $value, $matches))) {
                throw new \coding_exception('Could not find the tile id form the element id passed please end the element id with the tile id');
            }
            $id = $matches[0];
            $tile = \block_totara_featured_links\tile\base::get_tile_instance($id);

            $tiles_to_sort[] = $tile;
        }

        $context = \context_block::instance($tiles_to_sort[0]->blockid);
        \external_api::validate_context($context);
        if (!has_any_capability(['moodle/my:manageblocks', 'moodle/block:edit'], $context)) {
            return null; // The user does not have permission to drag and drop
        }

        $i = 1;
        foreach ($tiles_to_sort as $tile) {
            if ($i != $tile->sortorder) {
                $tile->sortorder = $i;
                $DB->update_record('block_totara_featured_links_tiles', $tile);
            }
            $i++;
        }
    }

    /**
     * The values that the reorder tiles return: nothing
     * @return void
     */
    public static function reorder_tiles_returns() {
        return null;
    }
}