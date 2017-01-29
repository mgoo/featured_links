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

use block_featured_links\form\element\audience_list;

global $CFG;
require_once("$CFG->libdir/formslib.php");
/**
 * Class base_form_auth
 * This is the base form for the visibility option
 * This is the class that plugin tile types should extend
 * @package block_featured_links\tile
 */
abstract class base_form_auth extends \moodleform {
    protected $tile;

    public function __construct($tile, $action = null, $customdata = null, $method = 'post', $target = '', $attributes = null, $editable = true) {
        $this->tile = $tile;
        parent::__construct($action, $customdata, $method, $target, $attributes, $editable);
    }

    public function specific_definition(&$mform) {
        throw new \coding_exception('Please override this function');
    }

    /**
     * returns whether or not to show the tile rules options
     * @return boolean
     */
    public abstract function has_custom_rules();

    /**
     * This defines the main part of the visibility form
     */
    public function definition () {
        $mform = $this->_form;

        $mform->addElement('header', 'form_header', get_string('auth_edit', 'block_featured_links'));

        $radio_group = [];
        $radio_group[] = $mform->createElement('radio', 'access', '', get_string('access_show', 'block_featured_links'), 'show');
        $radio_group[] = $mform->createElement('radio', 'access', '', get_string('access_hide', 'block_featured_links'), 'hide');
        $radio_group[] = $mform->createElement('radio', 'access', '', get_string('access_custom', 'block_featured_links'), 'custom');
        $mform->addGroup($radio_group, 'access', get_string('access_label', 'block_featured_links'), ['<br>'], false);
        $mform->setDefault('access', 'show');

        $mform->addElement('header', 'preset_header', get_string('preset_title', 'block_featured_links'));
        $mform->closeHeaderBefore('preset_header');

        $mform->addElement('advcheckbox', 'preset_showing', get_string('preset_showing', 'block_featured_links'), '', [], [0, 1]);
        $mform->disabledIf('preset_showing', 'access', 'neq', 'custom');

        $preset_group = [];
        $preset_group[] = $mform->createElement('advcheckbox', 'presets_checkboxes[0]', '', get_string('preset_checkbox_loggedin', 'block_featured_links'), [], [0, 'loggedin']);
        $preset_group[] = $mform->createElement('advcheckbox', 'presets_checkboxes[1]', '', get_string('preset_checkbox_notloggedin', 'block_featured_links'), [], [0, 'notloggedin']);
        $preset_group[] = $mform->createElement('advcheckbox', 'presets_checkboxes[2]', '', get_string('preset_checkbox_guest', 'block_featured_links'), [], [0, 'guest']);
        $preset_group[] = $mform->createElement('advcheckbox', 'presets_checkboxes[3]', '', get_string('preset_checkbox_notguest', 'block_featured_links'), [], [0, 'notguest']);
        $preset_group[] = $mform->createElement('advcheckbox', 'presets_checkboxes[4]', '', get_string('preset_checkbox_admin', 'block_featured_links'), [], [0, 'admin']);
        $mform->addGroup($preset_group, 'presets_checkboxes', get_string('preset_checkboxes_label', 'block_featured_links'), ['<br>'], false);
        $mform->disabledIf('presets_checkboxes', 'preset_showing');

        $preset_aggregation = [];
        $preset_aggregation[] = $mform->createElement('radio', 'preset_aggregation', '', get_string('preset_aggregation_any', 'block_featured_links'), 'any');
        $preset_aggregation[] = $mform->createElement('radio', 'preset_aggregation', '', get_string('preset_aggregation_all', 'block_featured_links'), 'all');
        $mform->addGroup($preset_aggregation, 'preset_aggregation', get_string('preset_aggregation_label', 'block_featured_links'), ['<br>'], false);
        $mform->disabledIf('preset_aggregation', 'preset_showing');

        if ($this->has_custom_rules()) {
            $mform->addElement('header', 'custom_header', get_string('tilerules_title', 'block_featured_links'));
            $mform->addElement('advcheckbox', 'tile_rules_showing', get_string('tile_rules_show', 'block_featured_links'), '', [], [0, 1]);
            $mform->disabledIf('tile_rules_showing', 'access', 'neq', 'custom');
            $this->specific_definition($mform);
            $mform->disabledIf('custom_header', 'access', 'neq', 'custom');

            $mform->addElement('header', 'aggregation_heading', get_string('aggregation_title', 'block_featured_links'));

            $overall_aggregation = [];
            $overall_aggregation[] = $mform->createElement('radio', 'overall_aggregation', '', get_string('aggregation_any', 'block_featured_links'), 'any');
            $overall_aggregation[] = $mform->createElement('radio', 'overall_aggregation', '', get_string('aggregation_all', 'block_featured_links'), 'all');
            $mform->addGroup($overall_aggregation, 'overall_aggregation', get_string('aggregation_label', 'block_featured_links'), ['<br>'], false);
        }



        $this->add_action_buttons();
    }
}