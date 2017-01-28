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



namespace block_featured_links\form\element;

defined('MOODLE_INTERNAL') || die();

use totara_form\form\element\static_html;

/**
 * Class audience_list
 * Makes a list that contains audience names and number of members
 * @package block_featured_links\form\element
 */
class audience_list extends static_html {
    private $tileid;

    /**
     * Audience list constructor.
     *
     * @param string $name
     * @param string $label
     * @param string $tileid
     */
    public function __construct ($name, $label, $tileid) {
        parent::__construct($name, $label, '');
        $this->tileid = $tileid;
    }

    /**
     * Gets the cohort data from the database so it can be rendered into a list item.
     * @param $cohortid int
     * @return array ['name' => string, 'learners' => int]
     */
    public static function get_cohort_data($cohortid) {
        global $DB;
        $name = $DB->get_field('cohort', 'name', ['id' => $cohortid]);
        $learners = $DB->count_records('cohort_members', ['cohortid' => $cohortid]);
        return ['name' => $name, 'learners' => $learners];
    }

    /**
     * Static method like this so I could call it from ajax.
     * @param int $cohortid
     * @param string $audience_name
     * @param int $num_learners
     * @return string HTML code for a list element
     */
    public static function render_row($cohortid, $audience_name, $num_learners) {
        global $PAGE;
        $renderer = $PAGE->get_renderer('core');
        return $renderer->render_from_template(
            'block_featured_links/element_audience_list_item',
            ['name' => $audience_name,
                'cohortid' => $cohortid,
                'num_learners' => $num_learners]
        );
    }

    /**
     * renders the base unorded list and gets the initial items to put into the list.
     * @return string HTML code of the list
     */
    public function render() {
        global $DB, $PAGE;
        $items = [];
//        $results = $DB->get_records(
//            'cohort_visibility',
//            ['instanceid' => $this->tileid, 'instancetype' => COHORT_ASSN_ITEMTYPE_FEATURED_LINKS],
//            '',
//            'cohortid');
//
//        foreach ($results as $result) {
//            $data = self::get_cohort_data($result->cohortid);
//            array_push($items, self::render_row($result->cohortid, $data['name'], $data['learners']));
//        }
        $renderer = $PAGE->get_renderer('core');
        return $renderer->render_from_template(
            'block_featured_links/element_audience_list', [
                'name' => $this->get_name(),
                'items' => $items
            ]
        );
    }

    /**
     * Changes the attributes from the default static html attributes
     * @param \renderer_base $output
     * @return array
     */
    public function export_for_template (\renderer_base $output) {
        $result = parent::export_for_template($output);
        $this->render();
        $attributes = [];
        $attributes['html'] = (string)$this->render();
        $this->set_attribute_template_data($result, $attributes);
        return $result;
    }
}