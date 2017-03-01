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

require_once('../../config.php');
require_once($CFG->libdir . '/pagelib.php');

require_login();

$tileid = required_param('tileid', PARAM_INT);
$return_url = optional_param('return_url', null, PARAM_LOCALURL);
$tile_instance = \block_featured_links\tile\base::get_tile_instance($tileid);

$PAGE->set_url(
    new \moodle_url(
        '/blocks/featured_links/edit_tile_visibility.php',
        ['tileid' => $tileid, 'return_url' => $return_url]
    )
);

$context = \context_block::instance($tile_instance->blockid, MUST_EXIST);
$PAGE->set_context($context);


// Checks that the user has the right permissions.
if (!$tile_instance->can_edit_tile() || !$tile_instance->is_visibility_applicable()) {
    print_error('cannot_edit_tile', 'block_featured_links');
}
$edit_form = $tile_instance->get_visibility_form(['blockinstanceid' => $tile_instance->blockid, 'tileid' => $tileid, 'return_url' => $return_url]);
// Saves the data.
if ($edit_form->is_cancelled()) {
    redirect(new \moodle_url($return_url));
} else if (($form_data = $edit_form->get_data())) {
    $tile_instance->save_visibility($form_data);
    redirect(new \moodle_url($return_url));
}

$edit_form->requirements();
// Draw page.

echo $OUTPUT->header();
echo $edit_form->render();
echo $OUTPUT->footer();