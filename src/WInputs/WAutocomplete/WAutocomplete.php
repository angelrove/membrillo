<?php
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 */

namespace angelrove\membrillo\WInputs\WAutocomplete;

use angelrove\utils\CssJsLoad;

class WAutocomplete
{
  private $input_name;
  private $input_id;
  private $input_label;

  //---------------------------------------------------------------------
  public function __construct($input_name, $input_id, $label, $url_ajax)
  {
      $this->input_name = $input_name;
      $this->input_id   = $input_id;
      $this->input_label= $label;

      CssJsLoad::set_script("
          var AUTOCOMPLETE_INPUT_NAME = '$input_name';
          var AUTOCOMPLETE_INPUT_ID   = '$input_id';
          var AUTOCOMPLETE_URL_AJAX   = '$url_ajax';
      ");

      CssJsLoad::set(__DIR__ . '/libs.js');
  }
  //---------------------------------------------------------------------
  public function get($form) {

    return '
      <!-- WAutocomplete -->
      <div class="WAutocomplete" id="WAutocomplete_'.$this->input_name.'">
        '.$form->getInput($this->input_name, $this->input_label, false).'
        '.$form->getInput($this->input_id, false, false, 'hidden').'
      </div>
      <!-- /WAutocomplete -->
      ';
  }
  //---------------------------------------------------------------------
}
