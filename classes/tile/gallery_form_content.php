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


/**
 * Class gallery_form_content
 * Defines the content form for the multi tile
 * @package block_featured_links\tile
 */
class gallery_form_content extends base_form_content {

    /**
     * The tile specific content options
     * @param $group
     * @return null
     */
    public function specific_definition(&$mform) {
        $mform->addElement('text', 'url', get_string('url_title', 'block_featured_links'));
        $mform->setType('url', PARAM_URL);

        $mform->addElement('advcheckbox', 'target', get_string('link_target_label', 'block_featured_links'), '', [], ['_blank', '_self']);

        $mform->addElement('text', 'heading', get_string('tile_title', 'block_featured_links'));
        $mform->setType('heading', PARAM_TEXT);

        $mform->addElement('textarea', 'textbody', get_string('tile_description', 'block_featured_links'));
        $mform->setType('textbody', PARAM_TEXT);

        $mform->addElement('select', 'heading_location', get_string('heading_location', 'block_featured_links'), [
            'top' => get_string('top_heading', 'block_featured_links'),
            'bottom' => get_string('bottom_heading', 'block_featured_links')
        ]);

        $mform->addElement('filemanager', 'background_imgs', get_string('tile_gallery_background', 'block_featured_links'),
            ['subdirs' => 0, 'maxbytes' => 0]);

        $mform->addElement('text', 'interval', get_string('interval', 'block_featured_links'));
        $mform->setType('interval', PARAM_INT);

        $mform->addElement('text', 'alt_text', get_string('tile_alt_text', 'block_featured_links'));
        $mform->setType('alt_text', PARAM_TEXT);

        $mform->addElement('color', 'background_color', get_string('tile_background_color', 'block_featured_links'));
        $mform->setType('background_color', PARAM_TEXT);
        return;
    }

    /**
     * The form requires the javascirpt and css for spectrum as well as passing in the strings
     */
    public function requirements () {
        parent::requirements();
        global $PAGE;
        $PAGE->requires->css(new \moodle_url('/blocks/featured_links/spectrum/spectrum.css'));
        $PAGE->requires->strings_for_js(['less', 'clear_color'], 'block_featured_links');
        $PAGE->requires->strings_for_js(['cancel', 'choose', 'more'], 'moodle');
        $PAGE->requires->js_call_amd('block_featured_links/spectrum', 'spectrum');
        $PAGE->add_body_class('contains-spectrum-colorpicker');
    }
}