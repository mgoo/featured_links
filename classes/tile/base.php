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
 * @package block_featured_links
 *
 *
 */

namespace block_featured_links\tile;

use action_menu;
use action_menu_link_secondary;
use core\output\flex_icon;
use renderer_base;

defined('MOODLE_INTERNAL') || die();
/**
 *
 */
define('BLOCK_FEATURED_LINKS_ACCESS_SHOW', 'show');
/**
 *
 */
define('BLOCK_FEATURED_LINKS_ACCESS_HIDE', 'hide');
/**
 *
 */
define('BLOCK_FEATURED_LINKS_ACCESS_CUSTOM', 'custom');


/**
 * Class base
 * @package block_featured_links\tile
 */
abstract class base{
    /** @var number id of the tile */
    public $id;
    /** @var number the order that the tile appears in the block */
    public $sort;
    /** @var number the id of the block that the tile is in */
    public $blockid;
    /** @var number the type of tile that it is */
    public $type;
    /** @var number the time that the tile was created */
    public $timecreated;
    /** @var number the last time that the tile was modified */
    public $timemodified;
    /** @var number the id of user who modified the tile last */
    public $userid;
    /** @var string the raw version of the data */
    public $data_raw;
    /** @var \stdClass the unfiltered and parsed version of the data */
    public $data;
    /** @var string has the modified version of the url so that if the www root changes the url will to */
    public $url_mod;
    /** @var \stdClass the filtered and parsed version of the data */
    public $data_filtered;
    /** @var string determines the basic visibility of the tile */
    public $access;
    /** @var boolean this holds whether or not to apply audience rules */
    public $audience_showing;
    /** @var string what type of aggregation does the audiences use and whether to display the form values*/
    public $audience_aggregation;
    /** @var boolean this hold whether or not to apply preset rules and whether to display the form */
    public $preset_showing;
    /** @var array the presets that apply to the tile  */
    public $presets;
    /** @var string the raw version of $presets */
    public $presets_raw;
    /** @var string what type of aggregation do the presets use */
    public $presets_aggregation;
    /** @var string the overall aggregation for the audiences presets and custom rules */
    public $overall_aggregation;
    /** @var boolean this holds whether the custom tile rules are showing */
    public $tile_rules_showing;
    /** @var string the custom rules that are saved and managed by the tile classes */
    public $custom_rules;
    /** @var array created from exploding audience_raw */
    public $audiences;
    /** @var string comer separated values of the audience that the tile is visible to */
    public $audiences_raw;

    /** gets the name of the tile to display in the edit form */
    public static function get_name() {
        throw new \coding_exception('Please Override this function');
    }

    /**
     * This makes a new tile.
     * The tile class must be passed
     * The block id must exist
     * @param int $blockinstanceid
     * @param \block_featured_links\tile\base $tile_class
     * @return null
     * @throws \Exception
     * @throws \coding_exception
     */
    public static function add_tile($blockinstanceid, $tile_class = null) {
        global $DB, $USER;
        if (!$DB->record_exists('block_instances', ['id' => $blockinstanceid])) {
            throw new \Exception('The Block instance id was not not found');
        }
        if (empty($tile_class)) {
            throw new \coding_exception('Please pass the tile class to the parent function');
        }
        $tile_class->type = '\block_featured_links\tile\default_tile';
        $tile_class->blockid = $blockinstanceid;

        $tile_class->data = new \stdClass();

        // Finds the id for the row.
        $tile_class->id = $DB->insert_record('block_featured_tiles', $tile_class, true);

        $tile_class->timecreated = time();
        $tile_class->userid = $USER->id;
        $tile_class->timemodified = time();

        // Get the ordering for the new tile.
        $order_values = $DB->get_fieldset_select('block_featured_tiles', 'sort', "blockid = $blockinstanceid");
        $tile_class->sort = $order_values ? max($order_values) + 1 : 1; // Sets the minimum position to 1.

        $tile_class->access = BLOCK_FEATURED_LINKS_ACCESS_SHOW;
        $tile_class->set_default_visibility();

        $tile_class->custom_add();

        $tile_class->encode_data();
        $tile_class->data_filtered = $tile_class->filter_values();
        $DB->update_record('block_featured_tiles', $tile_class);
        return $tile_class;
    }

    /**
     * Returns the name of this class
     * @return string
     */
    public static function get_class(){
        return get_called_class();
    }
    /**
     * This does the tile defined add
     * Ie instantiates objects so they can be referenced later
     * @param $tile_class
     * @return null
     */
    public abstract function custom_add();
    /**
     * Removes the unused values in the data object for the content form.
     * This means that only the value that the tile uses will be updated and supplied to the content form
     * and the tile template allows for values to be persistent when changing tile types.
     * @return \stdClass
     */
    protected abstract function filter_values();
    /**
     * This will return an instance of the edit content form for the tile
     * the edit tile object must extend base_form_content
     * @param array $parameters This is the parameters for the form
     * @return base_form_content
     */
    public abstract function edit_content_form($parameters);
    /**
     * Similar to the edit_content_form but gets the visibility form object instead
     * @param array $parameters
     * @return base_form_auth
     */
    public abstract function edit_auth_form($parameters);
    /**
     * This will render the content of the tile to html
     * background images and color should not be applied here rather set the values in the data for the tile
     * also accessibility text for the tiles link should go in the get_accessibility_text() function
     * @param \core_renderer $renderer
     * @return string HTML code
     */
    public abstract function render_content(\core_renderer $renderer);
    /**
     * Gets the data to be passed to the render_content function
     * @return array
     */
    protected abstract function get_template_data();
    /**
     * This defines the saving process for the custom tile fields
     * This should modify the data variable rather than chang directly saving to the database cause if you don't
     * what you save will get overridden when the tile is saved to the database.
     * @param \stdClass $data
     * @return null
     */
    public abstract function tile_custom_save($data);
    /**
     * Gets whether the tile is visible to the user by the custom rules defined by the tile.
     * This should only be used by the is_visible() function.
     * @return int (-1 = hidden, 0 = no rule, 1 = showing)
     */
    public abstract function get_custom_visibility();
    /**
     * Saves the data for the custom visibility.
     * Should only modify the custom_rules variable so the reset of the visibility and tile options are left the same
     * when its saved to the database
     * @param \stdClass $data all the data from the form
     * @return null
     */
    public abstract function set_custom_visibility($data);
    /**
     * Returns an array that the template will uses to put in text to help with accessibility
     * example
     *      [ 'tile_title' => 'value',
     *          'link_sr-only' => 'value']
     * @return array
     */
    public abstract function get_accessibility_text();

    /**
     * returns the context array for rendering the tile in the block
     * @param renderer_base $core_renderer
     * @param string $tile_content The HTML code for the content of the block
     * @return array
     */
    public function export_for_template($core_renderer, $tile_content){
        global $PAGE;
        $edit_url = (string)((new \moodle_url('/blocks/featured_links/edit_tile@@replace@@.php',
            ['blockinstanceid' => $this->blockid,
                'tileid' => $this->id]))->out_as_local_url());
        return [
            'tile_id' => $this->id,
            'content' => $tile_content,
            'disabled' => (!$this->is_visible()),
            'background_img' => (isset($this->data_filtered->background_img)
                    && !is_array($this->data_filtered->background_img) ?
                get_object_vars($this->data_filtered->background_img) : false),
            'alt_text' => $this->get_accessibility_text(),
            'background_color' => (isset($this->data_filtered->background_color) ?
                $this->data_filtered->background_color :
                false),
            'url' => (isset($this->url_mod) ? $this->url_mod : false),
            'controls' => $core_renderer->render(
                new action_menu([
                    new action_menu_link_secondary(
                        new \moodle_url(
                            str_replace(
                                '@@replace@@',
                                '_content',
                                $edit_url.'&return_url='.$PAGE->url->out_as_local_url()
                            )
                        ),
                        new \pix_icon('i/edit', 'edit_alt_text', 'moodle', ['class' => 'iconsmall', 'title' => '']),
                        'Content',
                        ['type' => 'edit']),
                    new action_menu_link_secondary(
                        new \moodle_url(
                            str_replace(
                                '@@replace@@',
                                '_auth', $edit_url.'&return_url='.$PAGE->url->out_as_local_url()
                            )
                        ),
                        new \pix_icon('i/hide', 'hide_alt_text', 'moodle',  ['class' => 'iconsmall', 'title' => '']),
                        'Visibility',
                        ['type' => 'edit_vis']),
                    new action_menu_link_secondary(
                        new \moodle_url(''),
                        new \pix_icon('i/delete', 'delete_alt_text', 'moodle',  ['class' => 'iconsmall', 'title' => '']),
                        'Delete',
                        ['type' => 'remove', 'blockid' => $this->blockid, 'tileid' => $this->id])
                ])
            )
        ];
    }

    /**
     * returns the array of data used to render the tile with the add tile button
     * @param int $blockid the id of the block that the adder tile will be in
     * @return array
     */
    final public static function adder_export_for_template($blockid) {
        global $PAGE;
        return [
            'adder' => true,
            'url' => (string)new \moodle_url('/blocks/featured_links/edit_tile_content.php',
                [
                    'blockinstanceid' => $blockid,
                    'return_url' => $PAGE->url->out_as_local_url()]
            )
        ];
    }


    /**
     * makes an empty tile if the tile id is null
     * if the tile id is not null then it will check if the tile contains the data
     * if not it will query the database to find the data
     * base constructor.
     * @param \stdClass $tile
     * @internal param int $tileid
     */
    public function __construct ($tile = null) {
        global $DB;
        if (is_null($tile)) {
            return;
        } else if (is_object($tile)) {
            $tile_data = $tile;
        } else {
            $tile_data = $DB->get_record('block_featured_tiles', ['id' => (int)$tile], '*', MUST_EXIST);
        }

        foreach ($tile_data as $key => $value) {
            $this->$key = $value;
        }
        $class = new $this->type();
        if (!$class instanceof base) {
            throw new \coding_exception('The class is not a valid tile class');
        }

//        $this->audiences_raw = '';
//        $results = $DB->get_records('cohort_visibility',
//            [
//                'instanceid' => $this->id,
//                'instancetype' => COHORT_ASSN_ITEMTYPE_FEATURED_LINKS
//            ],
//            '',
//            'cohortid'
//        );

//        if ($results !== false) {
//            foreach ($results as $cohort_vis) {
//                $this->audiences_raw .= ',' . $cohort_vis->cohortid;
//            }
//            $this->audiences_raw = substr($this->audiences_raw, 1);
//        }

        $this->decode_data();
    }

    /**
     * gets the class object for the tile that was specified with the tileid
     * @param $tileid
     * @return mixed
     */
    final public static function get_tile_class ($tileid) {
        global $DB;
        $tile= $DB->get_record('block_featured_tiles', ['id' => $tileid], '*', MUST_EXIST);
        return new $tile->type($tile);
    }

    /**
     * This gets the default data to pass to the auth form
     * @return \stdClass
     */
    public function get_auth_form_data() {
        $data = new \stdClass();
        $data->access = $this->access;
        $data->preset_aggregation = $this->presets_aggregation;
        $data->presets_checkboxes = $this->presets;
        $data->overall_aggregation = $this->overall_aggregation;
        $data->preset_showing = $this->preset_showing;
        $data->tile_rules_showing = $this->tile_rules_showing;
        return $data;
    }

    /**
     * Calculates whether the tile is visible for the user
     * @return bool
     */
    final public function is_visible() {
        global $USER;
        if (!isset($this->access)) {
            return true;
        }
        if ($this->access == BLOCK_FEATURED_LINKS_ACCESS_SHOW) {
            return true;
        } else if ($this->access == BLOCK_FEATURED_LINKS_ACCESS_HIDE) {
            return false;
        } else if ($this->access == BLOCK_FEATURED_LINKS_ACCESS_CUSTOM) {
            $matches = 0;
            $restrictions = 0;
            // Presets.
            if ($this->preset_showing) {
                $preset_matches = 0;
                $preset_restrictions = 0;
                if (in_array('loggedin', $this->presets)) {
                    if (isloggedin()) {
                        $preset_matches++;
                    } else {
                        $preset_restrictions++;
                    }
                }
                if (in_array('notloggedin', $this->presets)) {
                    if (!isloggedin()) {
                        $preset_matches++;
                    } else {
                        $preset_restrictions++;
                    }
                }
                if (in_array('guest', $this->presets)) {
                    if (isguestuser()) {
                        $preset_matches++;
                    } else {
                        $preset_restrictions++;
                    }
                }
                if (in_array('notguest', $this->presets)) {
                    if (!isguestuser()) {
                        $preset_matches++;
                    } else {
                        $preset_restrictions++;
                    }
                }
                if (in_array('admin', $this->presets)) {
                    if (is_siteadmin()) {
                        $preset_matches++;
                    } else {
                        $preset_restrictions++;
                    }
                }
                if ($this->presets_aggregation == 'any') {
                    if ($preset_matches > 0) {
                        $matches++;
                    } else if ($preset_restrictions > 0) {
                        $restrictions++;
                    }
                } else if ($this->presets_aggregation == 'all') {
                    if ($preset_restrictions > 0) {
                        $restrictions++;
                    } else if ($preset_matches > 0) {
                        $matches++;
                    }
                }
            }
            if ($this->tile_rules_showing) {
                // Custom.
                $custom_visibility = $this->get_custom_visibility();
                if ($custom_visibility == 1) {
                    $matches++;
                } else if ($custom_visibility == -1) {
                    $restrictions++;
                }
            }
            // Overall Aggregation.
            if ($this->overall_aggregation == 'any') {
                return $matches > 0 || $restrictions == 0; // Return true if there are no rules as well.
            } else if ($this->overall_aggregation == 'all') {
                return $restrictions == 0;
            }
        }
        return true;
    }

    /**
     * saves the sort to the database
     * @return null
     */
    final public function save_ordering() {
        global $DB;
        // Gets what the sort used to be.
        $current_tile = $DB->get_record('block_featured_tiles', ['id' => $this->id]);
        $old_sort =  $current_tile->sort;

        if ($old_sort == $this->sort) {
            return;
        }

        // Shifts all the tiles between the new position and the old position to make room for the tile.
        $orders = $DB->get_records('block_featured_tiles', ['blockid' => $this->blockid]);
        foreach ($orders as $tile) {
            if ($tile->sort >= $old_sort && $tile->sort <= $this->sort) {
                $tile->sort -= 1;
            } else if ($tile->sort <= $old_sort && $tile->sort >= $this->sort) {
                $tile->sort += 1;
            }
            $DB->update_record('block_featured_tiles', $tile);
        }
        $current_tile->sort = $this->sort;
        $DB->update_record('block_featured_tiles', $current_tile);
        $this->squash_ordering($this->blockid);
        return;
    }

    /**
     * Shifts all the sort values down to the lowest values so 1,3,5 becomes 1,2,3
     * @param int $blockid
     */
    final public static function squash_ordering($blockid){
        global $DB;
        $tiles_hashed = $DB->get_records('block_featured_tiles', ['blockid' => $blockid], 'sort');
        $tiles = [];
        foreach ($tiles_hashed as $tile) {
            array_push($tiles, $tile);
        }
        foreach ($tiles as $index => $tile) {
            $tile->sort = $index + 1;
            $DB->update_record('block_featured_tiles', $tile);
        }
    }

    /**
     * Gets the next value for sort that a new tile should have
     * @param $blockid
     * @return int
     */
    final protected static function get_next_sortorder($blockid){
        global $DB;
        return $DB->count_records(
            'block_featured_tiles',
            ['blockid' => $blockid]) + 1;
    }

    /**
     * This saves the tile object to the data base by calling tile_custom_save and encoding the data
     * @param \stdClass $data
     */
    final public function save($data) {
        global $DB;
        if (isset($data->type)) {
            $this->type = $data->type;
        }
        if (isset($data->sort)) {
            $this->sort = $data->sort;
        }

        $this->tile_custom_save($data);
        $this->save_ordering();
        unset($this->sort);
        $this->timemodified = time();

        $this->encode_data();
        $DB->update_record('block_featured_tiles', $this);
    }

    /**
     * This saves the visibility options
     * @param $data
     */
    final public function save_visibility($data) {
        global $DB;
        $this->access = !isset($data->access) ? BLOCK_FEATURED_LINKS_ACCESS_SHOW : $data->access;

        // Remove Values if its not custom.
        if ($data->access != BLOCK_FEATURED_LINKS_ACCESS_CUSTOM) {
            $this->set_default_visibility();
        } else {
            $this->audience_aggregation = !isset($data->audience_aggregation) ? 'any' : $data->audience_aggregation;
            $this->presets_raw = !isset($data->presets_checkboxes) ? '' : implode(',', $data->presets_checkboxes);
            $this->presets_aggregation = !isset($data->preset_aggregation) ? 'any' : $data->preset_aggregation;
            $this->overall_aggregation = !isset($data->overall_aggregation) ? 'any' : $data->overall_aggregation;
            $this->custom_rules = $this->set_custom_visibility($data);
            $this->audience_showing = !isset($data->audience_showing) ? 0 : $data->audience_showing;
            $this->preset_showing = !isset($data->preset_showing) ? 0 : $data->preset_showing;
            $this->tile_rules_showing = !isset($data->tile_rules_showing) ? 0 : $data->tile_rules_showing;
        }

        $this->timemodified = time();

        $DB->update_record('block_featured_tiles', $this);
    }

    /**
     * Sets the default Visibility values
     */
    protected function set_default_visibility() {
        $this->audiences_raw = '';
        $this->audiences = [''];
        $this->audiences_raw = '';
        $this->audience_aggregation = 'any';
        $this->presets = [''];
        $this->presets_raw = '';
        $this->presets_aggregation = 'any';
        $this->overall_aggregation = 'any';
        $this->custom_rules = '';
        $this->audience_showing = 0;
        $this->preset_showing = 0;
        $this->tile_rules_showing = 0;
    }

    /**
     * decodes the raw data variables
     */
    protected function decode_data() {
        $this->data = json_decode($this->data_raw);
        $this->presets = explode(',', $this->presets_raw);
        $this->audiences = explode(',', $this->audiences_raw);
        $this->data_filtered = $this->filter_values();
        $this->url_mod = isset($this->data->url) ? $this->data->url : '';
        if (substr($this->url_mod, 0, 1) == '/') {
            $this->url_mod = new \moodle_url($this->url_mod);
        }
    }

    /**
     * encodes the data ready to put into the database
     */
    protected function encode_data() {
        $this->data_raw = json_encode($this->data);
        $this->presets_raw = implode(',', $this->presets);
    }

}