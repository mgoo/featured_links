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
use totara_form\form\clientaction\hidden_if;
use totara_form\form\element\checkbox;
use totara_form\form\element\checkboxes;
use totara_form\form\element\hidden;
use totara_form\form\element\radios;
use totara_form\form\group\section;
use totara_form\form\element\static_html;

/**
 * Class base_form_auth
 * This is the base form for the visibility option
 * This is the class that plugin tile types should extend
 * @package block_featured_links\tile
 */
abstract class base_form_auth extends base_form{

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
        require_once($CFG->dirroot . '/totara/cohort/lib.php');

        $tileid = $this->get_parameters()['tileid'];
        $blockid  = $this->get_parameters()['blockinstanceid'];

        $group = $this->model->add(new section('group', get_string('auth_edit', 'block_featured_links')));
        $group->set_collapsible(false);

        $access = $group->add(new radios(
            'access',
            get_string('access_label', 'block_featured_links'),
            [BLOCK_TOTARA_FEATURED_LINKS_ACCESS_SHOW => get_string('access_show', 'block_featured_links'),
                BLOCK_TOTARA_FEATURED_LINKS_ACCESS_HIDE => get_string('access_hide', 'block_featured_links'),
                BLOCK_TOTARA_FEATURED_LINKS_ACCESS_CUSTOM => get_string('access_custom', 'block_featured_links')
            ]));

        $audience = $this->model->add(new section('audience', get_string('audience_title', 'block_featured_links')));
        $audience->set_collapsible(true);
        $audience->set_expanded(true);

        $audience_checkbox = $audience->add(
            new checkbox('audience_showing',
                get_string('audience_showing', 'block_featured_links')
            )
        );

        $audiencelist = $audience->add(new audience_list('audience_visible_table', '&nbsp;', $tileid));

        $audiences_visible = $audience->add(new hidden('audiences_visible', PARAM_TEXT));
        $audiences_visible->set_frozen(false);
        $add_audience_button = $audience->add(
            new static_html('add_audience_button',
                '&nbsp;',
                '<input type="button" value="'.get_string('audience_add', 'block_featured_links').'" id="add_audience_id">'
            )
        );

        $audience_aggregation = $audience->add(new radios('audience_aggregation',
            get_string('audience_aggregation_label', 'block_featured_links'),
            [
                'any' => get_string('audience_aggregation_any', 'block_featured_links'),
                'all' => get_string('audience_aggregation_all', 'block_featured_links')
            ]));

        if (has_capability('moodle/cohort:view', \context_block::instance($blockid))) {
            $this->model->add_clientaction(new hidden_if($audiencelist))->is_empty($audience_checkbox)->is_empty($audiences_visible);
            $this->model->add_clientaction(new hidden_if($add_audience_button))->is_empty($audience_checkbox);
            $this->model->add_clientaction(new hidden_if($audience_aggregation))->is_empty($audience_checkbox);
        } else {
            $this->model->add_clientaction(new hidden_if($audience_checkbox))->not_equals($audience_checkbox, 'unreachable value');
            $this->model->add_clientaction(new hidden_if($audiencelist))->not_equals($audience_checkbox, 'unreachable value');
            $this->model->add_clientaction(new hidden_if($add_audience_button))->not_equals($audience_checkbox, 'unreachable value');
            $this->model->add_clientaction(new hidden_if($audience_aggregation))->not_equals($audience_checkbox, 'unreachable value');

            $num_audience = $this->model->get_current_data('audiences_visible')['audiences_visible'] != '' ?
                count(explode(',', $this->model->get_current_data('audiences_visible')['audiences_visible'])) :
                0;
            $audience->add(new static_html('static',
                '',
                str_replace('@@#@@', $num_audience, get_string('audience_hide', 'block_featured_links'))
            ));
        }

        $presets = $this->model->add(new section('presets', get_string('preset_title', 'block_featured_links')));
        $presets->set_collapsible(true);
        $presets->set_expanded(true);
        $preset_checkbox = $presets->add(
            new checkbox('preset_showing',
                get_string('preset_showing',
                    'block_featured_links')
            )
        );
        $preset_checkboxes = $presets->add(new checkboxes('presets_checkboxes',
            get_string('preset_checkboxes_label', 'block_featured_links'),
            [
                'loggedin' => get_string('preset_checkbox_loggedin', 'block_featured_links'),
                'notloggedin' => get_string('preset_checkbox_notloggedin', 'block_featured_links'),
                'guest' => get_string('preset_checkbox_guest', 'block_featured_links'),
                'notguest' => get_string('preset_checkbox_notguest', 'block_featured_links'),
                'admin' => get_string('preset_checkbox_admin', 'block_featured_links')
            ]));
        $preset_aggregation = $presets->add(new radios('preset_aggregation',
            get_string('preset_aggregation_label', 'block_featured_links'),
            [
                'any' => get_string('preset_aggregation_any', 'block_featured_links'),
                'all' => get_string('preset_aggregation_all', 'block_featured_links')
            ]));

        $this->model->add_clientaction(new hidden_if($preset_checkboxes))->is_empty($preset_checkbox);
        $this->model->add_clientaction(new hidden_if($preset_aggregation))->is_empty($preset_checkbox);

        if ($this->has_custom_rules()) {
            $tile_rules = $this->model->add(
                new section('tile_rules',
                    get_string('tilerules_title',
                        'block_featured_links')));
            $tile_rules->set_collapsible(true);
            if (isset($this->model->get_current_data('tile_rules_showing')['tile_rules_showing'])
                && $this->model->get_current_data('tile_rules_showing')['tile_rules_showing']) {
                $tile_rules->set_expanded(true);
            }
            $tile_rules_show = $tile_rules->add(
                new checkbox('tile_rules_showing',
                    get_string('tile_rules_show',
                        'block_featured_links')
                )
            );
            $elements = $this->specific_definition($tile_rules);
            foreach ($elements as $element) {
                $this->model->add_clientaction(new hidden_if($element))->is_empty($tile_rules_show);
            }
            $this->model->add_clientaction(new hidden_if($tile_rules))->not_equals($access, BLOCK_TOTARA_FEATURED_LINKS_ACCESS_CUSTOM);
        }

        $aggregation = $this->model->add(
            new section('aggregation',
                get_string('aggregation_title',
                    'block_featured_links')
            )
        );
        $aggregation->set_collapsible(true);
        $aggregation->set_expanded(true);
        $aggregation->add(new radios('overall_aggregation',
            get_string('aggregation_label', 'block_featured_links'),
            [
                'any' => get_string('aggregation_any', 'block_featured_links'),
                'all' => get_string('aggregation_all', 'block_featured_links')
            ])
        );

        $this->model->add_clientaction(new hidden_if($audience))->not_equals($access, BLOCK_TOTARA_FEATURED_LINKS_ACCESS_CUSTOM);
        $this->model->add_clientaction(new hidden_if($presets))->not_equals($access, BLOCK_TOTARA_FEATURED_LINKS_ACCESS_CUSTOM);

        $PAGE->requires->js_call_amd('block_featured_links/visibility_form', 'init', [$this->model->get_id_suffix()]);

        parent::definition();
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
            '/blocks/featured_links/edit_tile_auth.php',
            ['blockinstanceid' => $blockinstanceid, 'tileid' => $tileid, 'return_url' => $return_url]
        );
    }
}