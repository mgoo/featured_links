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

defined('MOODLE_INTERNAL') || die();

use block_featured_links\form\validator\alt_text_required;
use block_featured_links\form\validator\is_color;
use \block_featured_links\form\element\colorpicker;


/**
 * Class default_form_content
 * This is the default content form.
 * This can be used as an example for other tile types
 * @package block_featured_links\tile
 */
class default_form_content extends base_form_content{

    /**
     * The tile specific content options
     * @param $group
     * @return null
     */
    public function specific_definition(&$mform) {
        global $CFG;
        $GLOBALS['HTML_QUICKFORM_ELEMENT_TYPES']['color'] = [$CFG->dirroot.'/blocks/featured_links/classes/form/element/color.php', 'block_featured_links\form\element\color'];

        $mform->addElement('text', 'url', get_string('url_title', 'block_featured_links'));
        $mform->setType('url', PARAM_URL);
        $mform->addRule('url', get_string('error'),'required', true);

        $mform->addElement('text', 'heading', get_string('tile_title', 'block_featured_links'));
        $mform->setType('heading', PARAM_TEXT);

        $mform->addElement('text', 'textbody', get_string('tile_description', 'block_featured_links'));
        $mform->setType('textbody', PARAM_TEXT);

        $mform->addElement(
            'filemanager',
            'background_img',
            get_string('tile_background', 'block_featured_links'),
            ['subdirs' => 0, 'maxbytes' => 0, 'maxfiles' => 1]
        );
        if (!empty($this->_customdata->background_img)) {
            file_prepare_draft_area($this->_customdata->background_img, \context_block::instance($this->tile->blockid)->__get('id'), 'block_featured_links', 'tile_background', $this->_customdata->background_img,
                array('subdirs' => 0, 'maxbytes' => -1, 'maxfiles' => 1));
        }

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
        global $PAGE;
        $PAGE->requires->css(new \moodle_url('/blocks/featured_links/spectrum/spectrum.css'));
        $PAGE->requires->strings_for_js(['less', 'clear_color'], 'block_featured_links');
        $PAGE->requires->strings_for_js(['cancel', 'choose', 'more'], 'moodle');
        $PAGE->requires->js_call_amd('block_featured_links/spectrum', 'spectrum');
    }
}