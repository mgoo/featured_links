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

require_once('../../config.php');

global $PAGE, $OUTPUT, $USER;

require_login();

$blockinstanceid = required_param('blockinstanceid', PARAM_INT);
$tileid = optional_param('tileid', null, PARAM_INT);
$return_url = optional_param('return_url', null, PARAM_LOCALURL);
$type = optional_param('type', null, PARAM_TEXT);
if (!empty($type)) {
    // Make sure the type passes is a tile type.
    if (!class_exists($type)) {
        throw new \Exception(get_string('invalid_class_name', 'block_featured_links'));
    }
    if (!is_subclass_of($type, base::get_class())) {
        throw new \Exception(get_string('invalid_class', 'block_featured_links'));
    }
}
$PAGE->set_url(
    new \moodle_url(
        '/blocks/featured_links/edit_tile_content.php',
        ['blockinstanceid' => $blockinstanceid, 'tileid' => $tileid, 'return_url' => $return_url]
    )
);

$context = \context_block::instance($blockinstanceid, MUST_EXIST);
$PAGE->set_context($context);

$PAGE->requires->js_call_amd('block_featured_links/form', 'init');

if (!empty($tileid)) {
    $tile_class = !empty($type) ? new $type($tileid) : base::get_tile_class($tileid);

    if ($USER->id != $tile_class->userid) {
        require_capability('moodle/site:manageblocks', $context);
    } else {
        //require_capability('totara/dashboard:manageblocks', $context); // TODO add proper capability
    }
} else {
    //require_capability('totara/dashboard:manageblocks', $context); //TODO add proper capability
    $tile_class = !empty($type) ? new $type() : new \block_featured_links\tile\default_tile();
}

$edit_form = $tile_class->edit_content_form(['blockinstanceid' => $blockinstanceid, 'tileid' => $tileid, 'return_url' => $return_url]);

if ($form_data = $edit_form->get_data()) { // Saves the data.
    if (empty($tileid)) {
        $tile_class = $type::add_tile($blockinstanceid);
    }
    $tile_class->save($form_data);
    redirect(new \moodle_url($return_url));
} else if ($edit_form->is_cancelled()) {
    redirect(new \moodle_url($return_url));
}

$edit_form->requirements();
// Draw page.
echo $OUTPUT->header();
echo $edit_form->render();
echo $OUTPUT->footer();
