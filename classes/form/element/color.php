<?php
namespace block_featured_links\form\element;
global $CFG;
require_once($CFG->libdir.'/form/text.php');
use MoodleQuickForm_text;

class color extends MoodleQuickForm_text {
    /**
     * Class constructor
     *
     * @param     string    $elementName    (optional)Input field name attribute
     * @param     string    $elementLabel   (optional)Input field label
     * @param     mixed     $attributes     (optional)Either a typical HTML attribute string
     *                                      or an associative array
     */
    public function __construct($elementName=null, $elementLabel=null, $attributes=null) {
        parent::__construct($elementName, $elementLabel, $attributes);
        $this->setType('color');
        $this->_attributes['class'] = 'form-control';
    }
}