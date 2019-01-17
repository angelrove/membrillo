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

  //---------------------------------------------------------------------
  public function __construct($input_name, $input_id, $url_ajax)
  {
      $this->input_name = $input_name;
      $this->input_id   = $input_id;

      CssJsLoad::set_script("
          var AUTOCOMPLETE_INPUT_NAME = '$input_name';
          var AUTOCOMPLETE_INPUT_ID   = '$input_id';
          var AUTOCOMPLETE_URL_AJAX   = '$url_ajax';
      ");

      CssJsLoad::set(__DIR__ . '/libs.js');
  }
  //---------------------------------------------------------------------
  public function get($form, $input_label) {

    return '
      <!-- WAutocomplete -->
      <div id="WAutocomplete">
        '.$form->getInput($this->input_name, $input_label, false).'
        '.$form->getInput($this->input_id, false, false, 'hidden').'
      </div>
      <!-- /WAutocomplete -->
      ';
  }
  //---------------------------------------------------------------------
}
