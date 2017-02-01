<?
/**
 * WInputColor
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 */

namespace angelrove\membrillo\WInputs\WInputColor;


class WInputColor
{
  //---------------------------------------------------------------------
  public static function get($name, $value) {

    echo <<<EOD

     <!-- WInputColor -->
     <script>
     $(document).ready(function() {
       $("#WInputColor_$name").change(function() {
         $("#$name").val($(this).val());
       });
       $("#$name").change(function() {
         $("#WInputColor_$name").val($(this).val());
       });
     });
     </script>

     <span id="WInputColor">
      <input type="text" id="$name" name="$name" value="$value"><input type="color" id="WInputColor_$name" value="$value">
     <span>
     <!-- /WInputColor -->

EOD;
  }
  //---------------------------------------------------------------------
}
