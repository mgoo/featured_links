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

namespace block_featured_links\tile;

defined('MOODLE_INTERNAL') || die();


/**
 * This is the base class for the default tile it can be an example of how to do this in the tile mods
 * Class default_tile
 * @package block_featured_links\tile
 */
class default_tile extends base{
    protected $used_fields = ['heading', // string The title for the tile.
        'textbody', // string The description for the tile.
        'url', // string The url that the tile links to.
        'background_color', // string The hex value of the background color.
        'background_img', // string The filename  for the background image.
        'alt_text', // string The text to go in the sr-only span in the anchor tag.
        'target', // string The target for the link either '_self' or '_blank'.
        'heading_location']; // string The location of the heading either 'top' or 'bottom'.
    protected $content_class = 'block-featured-links-content';
    protected $content_template = 'block_featured_links/content';

    /**
     * {@inheritdoc}
     */
    public static function get_name() {
        return get_string('default_name', 'block_featured_links');
    }

    /**
     * {@inheritdoc}
     */
    public function add_tile() {

    }

    /**
     * {@inheritdoc}
     */
    public function copy_files(&$new_tile) {
        if (empty($this->data->background_img)) {
            return;
        }
        $fromcontext = \context_block::instance($this->blockid);
        $tocontext = \context_block::instance($new_tile->blockid);
        $fs = get_file_storage();
        // This extra check if file area is empty adds one query if it is not empty but saves several if it is.
        if (!$fs->is_area_empty($fromcontext->id, 'block_featured_links', 'tile_background', $this->id, false)) {
            file_prepare_draft_area($draftitemid,
                $fromcontext->id,
                'block_featured_links',
                'tile_background',
                $this->id,
                ['subdirs' => 0, 'maxbytes' => 0, 'maxfiles' => 1]);
            file_save_draft_area_files($draftitemid,
                $tocontext->id,
                'block_featured_links',
                'tile_background',
                $new_tile->id,
                ['subdirs' => 0, 'maxbytes' => 0, 'maxfiles' => 1]);
        }
    }

    /**
     * Gets the data for the content form and loads the background image back into the draft area so its displayed
     * in the filemanager
     * @return \stdClass
     */
    public function get_content_form_data() {
        $data_obj = parent::get_content_form_data();
        // Move background file to the draft area.
        if (isset($this->data->background_img)) {
            $data_obj->background_img = $this->id;
        }
        return $data_obj;
    }

    /**
     * {@inheritdoc}
     */
    protected function get_content_template_data() {
        $notempty = false;
        if (!empty($this->data_filtered->heading) || !empty($this->data_filtered->textbody)) {
            $notempty = true;
        }
        return ['heading' => (empty($this->data_filtered->heading) ? '' : $this->data->heading),
            'textbody' => (empty($this->data_filtered->textbody) ? '' : $this->data->textbody),
            'content_class' => (empty($this->content_class) ? '' : $this->content_class),
            'heading_location' => (empty($this->data_filtered->heading_location) ? '' : $this->data_filtered->heading_location),
            'notempty' => $notempty
        ];
    }

    /**
     * Adds the data needed for the default tile type
     * @param \core_renderer $renderer
     * @return array
     */
    protected function get_content_wrapper_template_data($renderer) {
        $data = parent::get_content_wrapper_template_data($renderer);

         $data['background_img'] = empty($this->data_filtered->background_img) ? false :
            (string)\moodle_url::make_pluginfile_url(\context_block::instance($this->blockid)->__get('id'),
            'block_featured_links',
            'tile_background',
            $this->id,
            '/',
            $this->data_filtered->background_img);

        $data['alt_text'] = $this->get_accessibility_text();
        $data['background_color'] = (!empty($this->data_filtered->background_color) ?
            $this->data_filtered->background_color :
            false);
        $data['url'] = (!empty($this->url_mod) ? $this->url_mod : false);
        $data['target'] = (!empty($this->data_filtered->target) ? $this->data_filtered : false);
        return $data;
    }

    /**
     * moves a file from the draft area to a defined area
     * @param \stdClass $data
     * @return void
     */
    public function save_content_tile($data) {
        global $CFG;
        // Saves the Draft area.
        $draftitemid = file_get_submitted_draft_itemid('background_img');
        file_save_draft_area_files($draftitemid,
            \context_block::instance($this->blockid)->__get('id'),
            'block_featured_links',
            'tile_background',
            $this->id,
            ['subdirs' => 0, 'maxbytes' => 0, 'maxfiles' => 1]);

        // Gets the url to the new file.
        $fs = get_file_storage();
        $files = $fs->get_area_files(\context_block::instance($this->blockid)->__get('id'),
            'block_featured_links',
            'tile_background',
            $this->id,
            '',
            false);

        if ($file = reset($files)) {
            $this->data->background_img = $file->get_filename();
        }
	unset($this->data->background_imgs);

        /* Checks if the url starts with the wwwroot.
         * If it does it strips the wwwroot so it can be added back dynamically
         * Also checks if the url doesn't start with http:// https:// or a / then it adds https://
         * to stop people from using other protocols like FTP ect.
        */
        if (\core_text::substr($data->url, 0, 7) != 'http://'
            && \core_text::substr($data->url, 0, 8) != 'https://'
            && \core_text::substr($data->url, 0, 1) != '/') {
            $data->url = 'http://'.$data->url;
        }
        $wwwroot_chopped = preg_replace('/^(https:\/\/)|(http:\/\/)/', '', $CFG->wwwroot);
        if (\core_text::substr($data->url, 0, strlen($wwwroot_chopped)) == $wwwroot_chopped) {
            $data->url = \core_text::substr($data->url, strlen($wwwroot_chopped));
        }
        if (\core_text::substr($data->url, 0, strlen($CFG->wwwroot)) == $CFG->wwwroot) {
            $data->url = \core_text::substr($data->url, strlen($CFG->wwwroot));
        }
        if ($data->url == '') {
            $data->url = '/';
        }

        // Saves the rest of the data for the tile.
        if (isset($data->alt_text)) {
            $this->data->alt_text = $data->alt_text;
        }
        if (isset($data->url)) {
            $this->data->url = $data->url;
        }
        if (isset($data->heading)) {
            $this->data->heading = $data->heading;
        }
        if (isset($data->textbody)) {
            $this->data->textbody = $data->textbody;
        }
        if (isset($data->background_color)) {
            $this->data->background_color = $data->background_color;
        }
        if (isset($data->target)) {
            $this->data->target = $data->target;
        }
        if (isset($data->heading_location)) {
            $this->data->heading_location = $data->heading_location;
        }
        return;
    }

    /**
     * {@inheritdoc}
     * The static tile does not use any custom rules
     */
    public function is_visible_tile() {
        return 0;
    }

    /**
     * {@inheritdoc}
     * The static tile does not have any custom rules
     */
    public function save_visibility_tile($data) {
        return '';
    }

    /**
     * Returns an array that the template will uses to put in text to help with accessibility
     * example
     *      [ 'sr-only' => string]
     * @return array
     */
    public function get_accessibility_text() {
        $sronly = '';
        if (!empty($this->data->alt_text)) {
            $sronly = $this->data->alt_text;
        } else if (!empty($this->data->heading)) {
            $sronly = $this->data->heading;
        } else if (!empty($this->data->textbody)) {
            $sronly = $this->data->textbody;
        }

        return [
            'sr-only' => $sronly
        ];
    }
}
