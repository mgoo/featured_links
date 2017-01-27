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

namespace block_totara_featured_links\form\validator;

defined('MOODLE_INTERNAL') || die();

use block_totara_featured_links\tile\base;
use ReflectionClass;
use \totara_form\element_validator;

/**
 * Class is_class_object
 * Validator that makes sure the tile type is a valid tile type not a random class
 * @package block_totara_featured_links\form\validator
 */
class is_class_object extends element_validator {

    /**
     * This will return an error if the type field does not contain a valid class or a class that does not extend base
     *
     * @return void adds errors to element
     */
    public function validate () {
        $classname = $this->element->get_data()['type'];
        if (!class_exists($classname)){
            $this->element->add_error(get_string('invalid_class_name', 'block_totara_featured_links'));
        }
        if (!is_subclass_of($classname, base::get_class())) {
            $this->element->add_error(get_string('invalid_class', 'block_totara_featured_links'));
        }
    }
}