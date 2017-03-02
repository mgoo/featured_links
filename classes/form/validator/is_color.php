<?php
/**
 * Created by PhpStorm.
 * User: andrewm
 * Date: 2/03/17
 * Time: 3:50 PM
 */

namespace block_featured_links\form\validator;


class is_color {

    public static function validate($value, $options = []){
        if (preg_match('/^#([0-9a-fA-F]{3}){1,2}$/', $value) == 0) {
            return false;
        }
        return true;
    }

}