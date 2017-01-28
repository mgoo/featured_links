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
 */

defined('MOODLE_INTERNAL') || die();

use block_totara_featured_links\tile\base;

/**
 * Class block_totara_featured_links
 * This is the main class for the block
 * Handel's block level things
 */
class block_totara_featured_links extends block_base {

    /**
     * Initializes the block
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_totara_featured_links');
        $this->blockname = get_class($this);
    }

    /**
     * Gets the javascript that is required for the block to work properly
     */
    public function get_required_javascript() {
        parent::get_required_javascript();
        $this->page->requires->strings_for_js(['delete', 'cancel'], 'core');
        $this->page->requires->strings_for_js(['confirm'], 'block_totara_featured_links');
        $this->page->requires->js_call_amd('block_totara_featured_links/ajax', 'block_totara_featured_links_remove_tile');
    }

    /**
     * Generates and returns the content of the block
     * @return stdClass | stdObject (stdObject is not a thing and should be removed from the documentation in the moodle code)
     */
    public function get_content() {
        if (isset($this->content->text)) {
            return $this->content;
        }

        $editing = $this->page->user_is_editing();

        if (!isset($this->content)) {
            $this->content = new stdClass();
        }

        $core_renderer = $this->page->get_renderer('core');

        $tiles = $this->get_tiles();
        $tile_data = [];
        if ($tiles != false) {
            foreach ($tiles as $tile) {
                $tile = base::get_tile_class($tile->id);
                if ($tile->is_visible() || ($editing && parent::user_can_edit())) {
                    $tile_content = $tile->render_content($core_renderer);
                    $tile_data[$tile->sort] = $tile->export_for_template($core_renderer, $tile_content);
                }
            }
            // Put the tiles in order to the array and indexed rather than hashed.
            $keys = array_keys($tile_data);
            array_multisort($keys, $tile_data);
        }

        // Add the add tile.
        if ($editing) {
            array_push($tile_data, base::adder_export_for_template($this->instance->id));
        }
        if (count($tile_data) == 0) {
            return $this->content;
        }

        /*
         * This is to add empty tiles at the end of the block so that the tiles in the last row stay the
         * same size as the tiles in the rows above
         */
        for ($i = 0; $i < 10; $i++) {
            array_push($tile_data, ['filler' => true]);
        }

        $data = [
            'tile_data' => [],
            'editing' => $editing,
            'size' => $this->config->size,
            'title' => $this->config->title,
            'manual_id' => $this->config->manual_id,
            'instanceid' => $this->instance->id,
            'hidden_text' => get_string('hidden_text', 'block_totara_featured_links')];

        // Puts the tile data into the data array so the values are indexed rather than hashed.
        $data['tile_data'] = array_values($tile_data);

        $this->content->text = $core_renderer->render_from_template('block_totara_featured_links/main', $data);
        return $this->content;
    }

    /**
     * Sets up the database row in the block_totara_featured_links table
     * @return bool
     */
    public function instance_create() {
        $this->config = new stdClass();
        $this->config->size = 'medium';
        $this->config->title = get_string('pluginname', 'block_totara_featured_links');
        $this->config->manual_id = '';

        $this->instance_config_commit();

        return parent::instance_create();
    }

    /**
     * deletes the rows in the database from block_totara_featured_links and block_featured_tiles
     * @return bool
     */
    public function instance_delete() {
        global $DB;
        $DB->delete_records('block_featured_tiles', ['blockid' => $this->instance->id]);
        return parent::instance_delete();
    }

    /**
     * gets the tiles for a specific block id and returns all their data
     * @return array
     */
    private function get_tiles() {
        global $DB;
        $results = $DB->get_records('block_featured_tiles', ['blockid' => $this->instance->id]);
        return $results;
    }


    /**
     * returns whether the multiple instances of a block are allowed on one page
     * @return bool
     */
    public function instance_allow_multiple() {
        return true;
    }

    /**
     * returns whether the block should have a header
     * @return bool
     */
    public function hide_header() {
        return !isset($this->config->title) || strlen($this->config->title) == 0;
    }

    /**
     * Not to sure about this but im pretty sure I need it
     */
    public function specialization() {
        if (isset($this->config)) {
            if (empty($this->config->title)) {
                $this->title = get_string('pluginname', 'block_totara_featured_links');
            } else {
                $this->title = $this->config->title;
            }

            if (!empty($this->config->data)) {
                if (!isset($this->content)) {
                    $this->content = new stdClass();
                }
                $this->get_content();
            }
        }
    }

    /**
     * Saves the block data from the form.
     * saves them to config->data as well as config so that the rendering will use them as they will be loaded back into
     * the block editing form
     * @param $data
     * @param bool $nolongerused
     */
    public function instance_config_save($data, $nolongerused = false) {

        $this->config->title = $data->title;
        $this->config->size = $data->size;
        $this->config->manual_id = $data->manual_id;

        parent::instance_config_save($this->config, null);
    }
}