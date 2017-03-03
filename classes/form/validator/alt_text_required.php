<?php
/**
 * Created by PhpStorm.
 * User: andrewm
 * Date: 3/03/17
 * Time: 8:52 AM
 */

namespace block_featured_links\form\validator;


use MoodleQuickForm_Rule_Required;

class alt_text_required{

    /**
     * @param array $value ['alt_text' => '', 'heading' => '', 'textbody' => '']
     * @param $options
     * @return bool|array
     */
    public static function validate($value, $options = null){
        if (empty($value['alt_text']) && empty($value['heading']) && empty($value['textbody'])) {
            return ['alt_text' => get_string('requires_alt_text', 'block_featured_links')];
        }
        return true;
    }
}