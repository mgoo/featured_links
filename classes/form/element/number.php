<?php
namespace block_featured_links\form\element;
use HTML_QuickForm_input;
require_once("HTML/QuickForm/input.php");

class number extends HTML_QuickForm_input
{
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
        $this->_persistantFreeze = true;
        $this->setType('number');
    }

    public function HTML_QuickForm_text($elementName=null, $elementLabel=null, $attributes=null) {
        self::__construct($elementName, $elementLabel, $attributes);
    }
}