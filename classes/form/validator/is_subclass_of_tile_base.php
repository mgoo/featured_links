<?php
/**
 * Created by PhpStorm.
 * User: andrewm
 * Date: 2/03/17
 * Time: 3:44 PM
 */

namespace block_featured_links\form\validator;


class is_subclass_of_tile_base {
    public static function validate($value, $options){
        list($plugin_name, $class_name) = explode('-', $value, 2);
        $type = "\\$plugin_name\\tile\\$class_name";
        if (!class_exists($type) || !is_subclass_of($type, '\block_totara_featured_links\tile\base')) {
            return true;
        }
        return false;
    }
}