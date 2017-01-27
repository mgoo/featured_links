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

require_once('../../config.php');

global $PAGE, $OUTPUT, $CFG, $USER;
require_once($CFG->libdir . '/pagelib.php');

require_login();

$blockinstanceid = required_param('blockinstanceid', PARAM_INT);
$tileid = required_param('tileid', PARAM_INT);
$return_url = optional_param('return_url', null, PARAM_LOCALURL);

$PAGE->set_url(
    new \moodle_url(
        '/blocks/totara_featured_links/edit_tile_auth.php',
        ['blockinstanceid' => $blockinstanceid, 'tileid' => $tileid, 'return_url' => $return_url]
    )
);

$context = \context_block::instance($blockinstanceid, MUST_EXIST);
$PAGE->set_context($context);

require_once($CFG->dirroot . '/totara/core/js/lib/setup.php');
require_once($CFG->dirroot . '/totara/cohort/lib.php');
local_js([
    TOTARA_JS_TREEVIEW,
    TOTARA_JS_UI,
    TOTARA_JS_DIALOG
]);

$tile_class = base::get_tile_class($tileid);
if ($USER->id != $tile_class->userid) {
    require_capability('moodle/site:manageblocks', $context);
} else {
    //require_capability('totara/dashboard:manageblocks', $context); // TODO add capabiliuty
}
$edit_form = $tile_class->edit_auth_form(['blockinstanceid' => $blockinstanceid, 'tileid' => $tileid, 'return_url' => $return_url]);
// Saves the data.
if ($edit_form->is_cancelled()) {
    redirect(new \moodle_url($return_url));
} else if (($form_data = $edit_form->get_data()) && !$edit_form->is_reloaded()) {
    $tile_class->save_visibility($form_data);
    redirect(new \moodle_url($return_url));
}

$edit_form->requirements();
// Draw page.

$PAGE->requires->strings_for_js(['audience_add'], 'block_totara_featured_links');
$PAGE->requires->js_call_amd(
    'block_totara_featured_links/audience_dialogue',
    'init',
    ['instancetype' => COHORT_ASSN_ITEMTYPE_FEATURED_LINKS, 'instanceid' => $tileid, 'sesskey' => $USER->sesskey]
);

echo $OUTPUT->header();
echo $edit_form->render();
echo $OUTPUT->footer();