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
 * Class base_form_auth
 * This is the base form for the visibility option
 * This is the class that plugin tile types should extend
 * @package block_featured_links\tile
 */
abstract class base_form_visibility extends base_form {

    /**
     * returns whether or not to show the tile rules options
     * @return boolean
     */
    public abstract function has_custom_rules();

    /**
     * This defines the main part of the visibility form
     */
    public function definition () {
        global $PAGE, $CFG;

/*        $tileid = $this->get_parameters()['tileid'];
        $blockid  = $this->get_parameters()['blockinstanceid'];*/

        $mform = $this->_form;
        $mform->addElement('header', 'form_header', get_string('visibility_edit', 'block_featured_links'));

        $visibility_options = [];
        $visibility_options[] = $mform->createElement('radio', 'visibility', '', get_string('visibility_show', 'block_featured_links'), base::VISIBILITY_SHOW);
        $visibility_options[] = $mform->createElement('radio', 'visibility', '', get_string('visibility_hide', 'block_featured_links'), base::VISIBILITY_HIDE);
        $visibility_options[] = $mform->createElement('radio', 'visibility', '', get_string('visibility_custom', 'block_featured_links'), base::VISIBILITY_CUSTOM);
        $mform->addGroup($visibility_options, 'visibility', get_string('visibility_label', 'block_featured_links'), '<br>');

        $mform->addElement('header', 'preset-heading', get_string('preset_title', 'block_featured_links'));

        $mform->addElement('advcheckbox', 'preset_showing', get_string('preset_showing', 'block_featured_links'), '', [], [0,1]);
        $mform->disabledIf('preset_showing', 'visibility[visibility]', 'neq', base::VISIBILITY_CUSTOM);

        $preset_options = [];
        $preset_options[] = $mform->createElement('checkbox', 'loggedin', get_string('preset_checkbox_loggedin', 'block_featured_links'));
        $preset_options[] = $mform->createElement('checkbox', 'notloggedin', get_string('preset_checkbox_notloggedin', 'block_featured_links'));
        $preset_options[] = $mform->createElement('checkbox', 'guest', get_string('preset_checkbox_guest', 'block_featured_links'));
        $preset_options[] = $mform->createElement('checkbox', 'notguest', get_string('preset_checkbox_notguest', 'block_featured_links'));
        $preset_options[] = $mform->createElement('checkbox', 'admin', get_string('preset_checkbox_admin', 'block_featured_links'));
        $mform->addGroup($preset_options, 'presets', get_string('preset_title', 'block_featured_links'), '<br>');
        $mform->disabledIf('presets', 'preset_showing');

        $preset_aggregation = [];
        $preset_aggregation[] = $mform->createElement('radio', 'preset_aggregation', '', get_string('preset_aggregation_any', 'block_featured_links'), base::AGGREGATION_ANY);
        $preset_aggregation[] = $mform->createElement('radio', 'preset_aggregation', '', get_string('preset_aggregation_all', 'block_featured_links'), base::AGGREGATION_ALL);
        $mform->addGroup($preset_aggregation, 'presets_aggregation', get_string('preset_aggregation_label', 'block_featured_links'), '<br>');
        $mform->disabledIf('presets_aggregation', 'preset_showing');

        if ($this->has_custom_rules()) {
            $mform->addElement('header', 'custom_header', get_string('tilerules_title', 'block_featured_links'));
            $mform->addElement('advcheckbox', 'tile_rules_showing', get_string('tile_rules_show', 'block_featured_links'), '', [], [0,1]);
            $mform->disabledIf('tile_rules_showing', 'visibility[visibility]', 'neq', base::VISIBILITY_CUSTOM);

            $this->specific_definition($mform);

            $mform->addElement('header', 'aggregation_header', get_string('aggregation_title', 'block_featured_links'));

            $overall_aggregation = [];
            $overall_aggregation[] = $mform->createElement('radio', 'overall_aggregation', '', get_string('aggregation_any', 'block_featured_links'), base::AGGREGATION_ANY);
            $overall_aggregation[] = $mform->createElement('radio', 'overall_aggregation', '', get_string('aggregation_all', 'block_featured_links'), base::AGGREGATION_ALL);
            $mform->addGroup($overall_aggregation, 'overall_aggregation', get_string('aggregation_label', 'block_featured_links'), '<br>');
            $mform->disabledIf('overall_aggregation', 'tile_rules_showing', 'notchecked');
            $mform->disabledIf('overall_aggregation', 'preset_showing', 'notchecked');
        }

        $this->add_action_buttons();
    }

    public function specific_definition(&$mform){

    }

    /**
     * Gets the action url for the form this means that the blockid, tileid and return url
     * Are all passed back to the script
     * @return \moodle_url
     */
    public function get_action_url () {
        $blockinstanceid  = $this->get_parameters()['blockinstanceid'];
        $tileid = $this->get_parameters()['tileid'];
        $return_url = $this->get_parameters()['return_url'];
        return new \moodle_url(
            '/blocks/featured_links/edit_tile_visibility.php',
            ['blockinstanceid' => $blockinstanceid, 'tileid' => $tileid, 'return_url' => $return_url]
        );
    }
}