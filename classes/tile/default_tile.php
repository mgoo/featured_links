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



namespace block_totara_featured_links\tile;

defined('MOODLE_INTERNAL') || die();

/**
 * This is the base class for the default tile it can be an example of how to do this in the tile mods
 * Class default_tile
 * @package block_totara_featured_links\tile
 */
class default_tile extends base{
    const FILE_NAME = 'background_img';
    const USED_FIELDS = ['heading', 'textbody', 'url', 'background_color', 'background_img', 'alt_text'];
    protected $CLASS_NAME = '\block_totara_featured_links\tile\default_tile';


    /**
     * Uses the self::USED_FIELDS to filter the data that has being pulled from the database
     */
    public function filter_values () {
        $data_filtered = new \stdClass();
        foreach ($this->data as $key => $datum) {
            if (in_array($key, self::USED_FIELDS)) {
                $data_filtered->$key = $this->data->$key;
            }
        }
        return $data_filtered;
    }

    /**
     * returns the name of the tile that will be displayed
     * @return string
     */
    public static function get_name() {
        return get_string('default_name', 'block_totara_featured_links');
    }

    /**
     * Adds a default tile to the database
     * Returns the object that it made
     * @param int $blockinstanceid
     * @return default_tile $tile_class
     */
    public static function add_tile($blockinstanceid, $tile_class = null) {
        $tile_class = new self();
        return parent::add_tile($blockinstanceid, $tile_class);
    }

    /**
     * instantiates the background_img array so php doesn't complain later
     * @param $tile_class
     * @return null|void
     */
    public function custom_add () {
        $this->data->background_img = [];
    }

    /**
     * returns the class of the edit for that the tile uses
     * @param array['blockinstanceid' => int, 'tileid' => int] $parameters
     * @return \moodleform mixed
     */
    public function edit_content_form($parameters) {
        global $DB;
        if (!$DB->record_exists('block_instances', ['id' => $parameters['blockinstanceid']])) {
            throw new \Exception('The block for the tile was not found');
        }
        if ($this->id != $parameters['tileid']) {
            throw new \Exception('The tileid passed and the tile id of the object do not match');
        }
        if ($DB->record_exists('block_featured_tiles', ['id' => $this->id])) {
            $data_obj = $this->data_filtered;

            // Add specific values to the array.
            $data_obj->sort = $this->sort;
            $data_obj->type = $this->type;

            // Move background file to the draft area.
            if (isset($this->data->background_img->itemid)) {
                $draftitemid = $this->data->background_img->itemid;
                $data_obj->background_img = $draftitemid;
            }
        } else { // Is new tile.
            $data_obj = new \stdClass();
            $data_obj->sort = self::get_next_sortorder($parameters['blockinstanceid']);
        }
        $data_obj->type = $this->CLASS_NAME;
        $parameters['type'] = $this->CLASS_NAME;
        $data_obj->blockid = $parameters['blockinstanceid'];
        $form = new default_form_content($this, new \moodle_url('edit_tile_content.php', $parameters), $data_obj);
        $form->set_data($data_obj);
        return $form;
    }

    /**
     * returns the class of the visibility form
     * @param array['blockinstanceid' => int, 'tileid' => int] $parameters
     * @return default_form_auth
     */
    public function edit_auth_form($parameters) {
        global $DB;
        if (!$DB->record_exists('block_instances', ['id' => $parameters['blockinstanceid']])) {
            throw new \Exception('The block for the tile was not found');
        }
        if (!$DB->record_exists('block_featured_tiles', ['id' => $parameters['tileid']])) {
            throw new \Exception('The tile was not found');
        }
        if ($this->id != $parameters['tileid']) {
            throw new \Exception('The tileid passed and the tile id of the object do not match');
        }
        return new default_form_auth($this->get_auth_form_data(), $parameters);
    }

    /**
     * renders the tile contents
     * should return a string of HTML
     * the div wrappers and buttons are added in the get_content class so they cannot be overridden
     * @param \core_renderer $renderer
     * @return string
     */
    public function render_content(\core_renderer $renderer) {
        $data = $this->get_template_data();
        $html_data = $renderer->render_from_template('block_totara_featured_links/content', $data);
        return $html_data;
    }

    /**
     * Puts the data from the class in a way which the template can render
     * @return array
     */
    protected function get_template_data() {
        return ['heading' => $this->data->heading, 'textbody' => $this->data->textbody];
    }

    /**
     * moves a file from the draft area to a defined area
     * @param array $data
     * @return null
     */
    public function tile_custom_save($data) {
        global $CFG;
        // Saves the Draft area.
        $draftitemid = file_get_submitted_draft_itemid(self::FILE_NAME);
        file_save_draft_area_files($draftitemid,
            \context_block::instance($this->blockid)->__get('id'),
            'block_totara_featured_links', 'tile_background',
            $draftitemid,
            ['subdirs' => 0, 'maxbytes' => 0, 'maxfiles' => 1]);

        // Gets the url to the new file.
        $fs = get_file_storage();
        $files = $fs->get_area_files(\context_block::instance($this->blockid)->__get('id'),
            'block_totara_featured_links',
            'tile_background',
            $draftitemid,
            '',
            false);

        if ($file = reset($files)) {
            $this->data->background_img = new \stdClass();
            $this->data->background_img->url =(string) \moodle_url::make_pluginfile_url($file->get_contextid(),
                $file->get_component(),
                $file->get_filearea(),
                $file->get_itemid(),
                $file->get_filepath(),
                $file->get_filename());
            $this->data->background_img->itemid = $file->get_itemid();
        } else {
            $this->data->background_img = [];
        }

        if (substr($data->url, 0, count($CFG->wwwroot)) == $CFG->wwwroot) {
            $data->url = substr($data->url, count($CFG->wwwroot)-1);
        } else if (substr($data->url, 0, 7) != 'http://'
            && substr($data->url, 0, 8) != 'https://'
            && substr($data->url, 0, 1) != '/') {

            $data->url = 'http://'.$data->url;
        }

        // Saves the rest of the data for the tile.
        $this->data->alt_text = $data->alt_text;
        $this->data->url = $data->url;
        $this->data->heading = $data->heading;
        $this->data->textbody = $data->textbody;
        $this->data->background_color = $data->background_color;
        return null;
    }

    /**
     * returns 0 for no rule 1 for showing and -1 for hidden
     * @return int
     */
    public function get_custom_visibility() {
        return 0;
    }

    /**
     * takes the data submitted from the visibility form an makes a string to save to the database
     * @param array $data all the data from the form
     * @return string
     */
    public function set_custom_visibility($data) {
        return '';
    }

    /**
     * Returns an array that the template will uses to put in text to help with acessability
     * example
     *      [ 'tile_title' => string,
     *          'link_sr-only' => string]
     * @return array
     */
    public function get_accessibility_text() {
        $sronly = '';
        if (isset($this->data->alt_text)) {
            $sronly = $this->data->alt_text;
        } else if (isset($this->data->heading)) {
            $sronly = $this->data->heading;
        } else if (isset($this->data->textbody)) {
            $sronly = $this->data->textbody;
        }

        return [
            'tile_title' => isset($this->data->alt_text) ? $this->data->alt_text : '',
            'link_sr-only' => $sronly
        ];
    }
}