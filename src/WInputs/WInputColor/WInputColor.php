<?php
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 */

namespace angelrove\membrillo2\WInputs\WInputColor;

use angelrove\utils\CssJsLoad;


class WInputColor
{
  //---------------------------------------------------------------------
  public static function get($name, $value) {

    CssJsLoad::set_script('
       $(document).ready(function() {
         $("#WInputColor_'.$name.'").change(function() {
            $("#'.$name.'").val($(this).val());
         });
         $("#'.$name.'").change(function() {
           $("#WInputColor_'.$name.'").val($(this).val());
         });
       });
    ');

    return <<<EOD
      <!-- WInputColor -->
      <span class="WInputColor">
        <input type="text" class="form-control" style="display:initial;width:90px" id="$name" name="$name" value="$value">
        <input type="color" id="WInputColor_$name" value="$value">
      <span>
      <!-- /WInputColor -->
EOD;
  }
  //---------------------------------------------------------------------
}
