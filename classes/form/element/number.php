<?php
namespace block_totara_featured_links\form\element;

use HTML_QuickForm_input;

require_once("HTML/QuickForm/input.php");

/**
 * HTML class for a text field
 *
 * @author       Adam Daniel <adaniel1@eesus.jnj.com>
 * @author       Bertrand Mansion <bmansion@mamasam.com>
 * @version      1.0
 * @since        PHP4.04pl1
 * @access       public
 */

class number extends HTML_QuickForm_input
{

    // {{{ constructor

    /**
     * Class constructor
     *
     * @param     string    $elementName    (optional)Input field name attribute
     * @param     string    $elementLabel   (optional)Input field label
     * @param     mixed     $attributes     (optional)Either a typical HTML attribute string
     *                                      or an associative array
     * @since     1.0
     * @access    public
     * @return    void
     */
    public function __construct($elementName=null, $elementLabel=null, $attributes=null) {
        parent::__construct($elementName, $elementLabel, $attributes);
        $this->_persistantFreeze = true;
        $this->setType('number');
    } //end constructor

    /**
     * Old syntax of class constructor for backward compatibility.
     */
    public function HTML_QuickForm_text($elementName=null, $elementLabel=null, $attributes=null) {
        self::__construct($elementName, $elementLabel, $attributes);
    }
}
