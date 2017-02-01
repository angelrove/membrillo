<?
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 */

namespace angelrove\membrillo;


class WInputText
{
   //----------------------------------------------------------------------
   static function get($name, $value, $cols='70', $rows='7', $isReadonly=false)
   {
     if($isReadonly === true) {
        echo '<textarea class="WInputText disabled" readonly cols="'.$cols.'" rows="'.$rows.'">'.$value.'</textarea>';
        return;
     }

     echo '<textarea class="WInputText" id="'.$name.'" name="'.$name.'" cols="'.$cols.'" rows="'.$rows.'">'.$value.'</textarea>';
   }
   //----------------------------------------------------------------------
}