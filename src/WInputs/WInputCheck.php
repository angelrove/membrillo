<?
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 */

namespace angelrove\membrillo2\WInputs;


class WInputCheck
{
    //----------------------------------------------------------------
    /**
     * $value = por defecto admite los valores: 0, 1
     */
    public static function get($name, $label, $value, $isReadonly=false, $setValueChecked='1')
    {
      if(!$value) $value = 0;

      $checked = '';
      if($value) {
         $checked = 'checked';
      }

      $disabled = ($isReadonly === true)? 'disabled':'';

      return <<<EOD
    <label class="checkbox-inline WInputCheck" id="WInputCheck_$name">
       <input type="hidden" id="$name" name="$name" value="$value">
       <input type="checkbox"
              value="$setValueChecked" $disabled $checked
              onclick="document.getElementById('$name').value = (this.checked)? '$setValueChecked' : '0';">
       $label
    </label>
EOD;

    }
    //----------------------------------------------------------------
}
