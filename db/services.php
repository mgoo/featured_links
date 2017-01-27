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


$functions = [
    'block_totara_featured_links_external_remove_tile' => [
        'classname'   => 'block_totara_featured_links_external',
        'methodname'  => 'remove_tile',
        'classpath'   => 'blocks/totara_featured_links/externallib.php',
        'description' => 'Removes a Tile',
        'type'        => 'write',
        'capabilities'=> '',
        'ajax'        => true,
        'loginrequired' => true,
    ],
    'block_totara_featured_links_external_add_audience_list_item' => [
        'classname'   => 'block_totara_featured_links_external',
        'methodname'  => 'add_audience_list_item',
        'classpath'   => 'blocks/totara_featured_links/externallib.php',
        'description' => 'renders a list item',
        'type'        => 'read',
        'capabilities'=> '',
        'ajax'        => true,
        'loginrequired' => true,
    ],
    'block_totara_featured_links_external_render_form' => [
        'classname'   => 'block_totara_featured_links_external',
        'methodname'  => 'render_form',
        'classpath'   => 'blocks/totara_featured_links/externallib.php',
        'description' => 'renders a content form',
        'type'        => 'read',
        'capabilities'=> '',
        'ajax'        => true,
        'loginrequired' => true,
    ]
];
