<?
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 */

namespace angelrove\membrillo2\WInputs\WInputCheck;

use angelrove\utils\CssJsLoad;


class WInputCheck
{
    //----------------------------------------------------
    public function __construct()
    {
      // CssJsLoad::set(__DIR__.'/styles.css');
    }
    //----------------------------------------------------------------
    /**
     * $value = por defecto admite los valores: 0, 1
     */
    public function get($name, $label, $value, $isReadonly=false, $onclick='', $setValueChecked='1')
    {
      if(!$value) $value = 0;

      $checked = '';
      if($value) {
         $checked = 'checked';
      }

      $disabled = ($isReadonly === true)? 'disabled':'';

      return <<<EOD

      <input type="hidden" id="$name" name="$name" value="$value">
      <input type="checkbox" value="$setValueChecked" id="chck_$name" $disabled
             onclick="document.getElementById('$name').value = (this.checked)? '$setValueChecked' : '0'; $onclick" $checked> <label for="chck_$name">$label</label>

EOD;
    }
    //----------------------------------------------------------------
}
