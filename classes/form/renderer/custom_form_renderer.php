<?php
namespace block_featured_links\form\renderer;

use MoodleQuickForm_Renderer;

defined('MOODLE_INTERNAL') || die();
require_once("$CFG->libdir/formslib.php");

/**
 * Created by PhpStorm.
 * User: andrewm
 * Date: 2/03/17
 * Time: 1:02 PM
 */
class custom_form_renderer extends \MoodleQuickForm_Renderer {
    public function __construct(){
        parent::__construct();
        $this->_elementTemplates['default'] = "\n\t\t".'<div id="{id}" class="fitem form-group row {advanced}<!-- BEGIN required --> required<!-- END required --> fitem_{typeclass} {emptylabel}" {aria-live}><div class="fitemtitle col-md-3"><label>{label}<!-- BEGIN required -->{req}<!-- END required -->{advancedimg} </label>{help}</div><div class="felement col-md-9 form-inline {typeclass}<!-- BEGIN error --> error<!-- END error -->" data-fieldtype="{type}"><!-- BEGIN error --><span class="error" tabindex="0">{error}</span><br /><!-- END error -->{element}</div></div>';
    }
}