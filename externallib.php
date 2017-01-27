<?php
/**
 * This file is part of Totara LMS
 *
 * Copyright (C) 2010 onwards Totara Learning Solutions LTD
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
 *
 *
 */



defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . "/externallib.php");

/**
 * Class block_totara_featured_links_external
 * This has the funcitons that will be called by AJAX
 */
class block_totara_featured_links_external extends external_api{

    /**
     * remove_tile function will remove a tile from a block from ajax making it so that the whole page doesnt have to be reloaded.
     * @return external_function_parameters
     */
    public static function remove_tile_parameters() {
        return new external_function_parameters(
            [
                'blockinstanceid' => new external_value(PARAM_INT, 'The block id that the tile is being removed from'),
                'tileid' => new external_value(PARAM_INT, 'The tile to be remove')
            ]
        );
    }

    /**
     * Removes a tile
     * @param int $blockid
     * @param int $tileid
     * @return bool
     */
    public static function remove_tile($blockid, $tileid) {
        self::validate_parameters(self::remove_tile_parameters(), ['blockinstanceid' => $blockid, 'tileid' => $tileid]);
        global $DB;

        if (!$DB->get_record('block_featured_tiles', ['id' => $tileid])) {
            return false;
        }

        // Remove the row form the tiles table.
        $DB->delete_records('block_featured_tiles', ['id' => $tileid]);
        \block_totara_featured_links\tile\base::squash_ordering($blockid);
        return true;
    }

    /**
     * Return type of the remove_tile function
     * @return external_value
     */
    public static function remove_tile_returns() {
        return new external_value(PARAM_BOOL, "This will return weather then tile was successfully removed");
    }

    /**
     * Parameters of the add_audience_list_item function
     * @return external_function_parameters
     */
    public static function add_audience_list_item_parameters() {
        return new external_function_parameters(
            ['cohortid' => new external_value(PARAM_INT, 'The id of the cohort to add')]
        );
    }

    /**
     * renders the HTML code for a list item in the audience list
     * @param $cohortid
     * @return string
     */
    public static function add_audience_list_item($cohortid) {
        self::validate_parameters(self::add_audience_list_item_parameters(), ['cohortid' => $cohortid]);
        $data = \block_totara_featured_links\form\element\audience_list::get_cohort_data($cohortid);
        return \block_totara_featured_links\form\element\audience_list::render_row($cohortid, $data['name'], $data['learners']);
    }

    /**
     * Return type of the add_audience_list_item function
     * @return external_value
     */
    public static function add_audience_list_item_returns() {
        return new external_value(PARAM_TEXT, 'The HTML code for the list item');
    }

    /**
     * Parameters of the render_form function
     * @return external_function_parameters
     */
    public static function render_form_parameters() {
        return new external_function_parameters(
            ['tileid' => new external_value(PARAM_TEXT, 'The id of the tile that the form is for'),
             'form_type' => new external_value(PARAM_TEXT, 'The class of the tile type that the form is for'),
             'parameters' => new external_value(PARAM_TEXT, 'Parameters for the form')]
        );
    }

    /**
     */
    public static function render_form($tileid, $form_type, $parameters) {
        self::validate_parameters(self::render_form_parameters(), ['tileid' => $tileid, 'form_type' => $form_type, 'parameters' => $parameters]);
        $parameters = json_decode($parameters, true);
        $tile_class = new $form_type($tileid);
        $form = $tile_class->edit_content_form($parameters);
        return $form->render();
    }

    /**
     * Return type of the render_form function
     * @return external_value
     */
    public static function render_form_returns() {
        return new external_value(PARAM_TEXT, 'The HTML code for the form');
    }
}