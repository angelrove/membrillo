<?
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 */

namespace angelrove\membrillo2\WInputs\WInputRadios;

use angelrove\utils\CssJsLoad;
use angelrove\utils\UtilsBasic;


class WInputRadios
{
    //----------------------------------------------------
    public function __construct()
    {
      CssJsLoad::set(__DIR__.'/styles.css');
    }
    //------------------------------------------------------------------
    /* From array */
    public function get($name, $listDatos, $id_selected, $onclick='', $listColors=array(), $is_assoc='')
    {
      $strSelect = '';

      // ¿Viene con claves?
      if($is_assoc == '') {
         $isAsociativo = UtilsBasic::array_is_assoc($listDatos);
      }
      else {
         $isAsociativo = $is_assoc;
      }

      foreach($listDatos as $id=>$nombre)
      {
         if($isAsociativo == false) $id = $nombre;

         // Selected
         $SELECTED = '';
         if($id == $id_selected) $SELECTED = ' checked';

         $idCheck = $name.'_'.$id;

         $style_bg = (isset($listColors[$id]))? 'style="background:'.$listColors[$id].'"' : '';

         // Option
         $strSelect .= <<<EOD
         <div id="WInputRadios_$idCheck" class="WInputRadio">
           &nbsp;
           <input type="radio" id="$idCheck" name="$name" value="$id" $SELECTED onclick="$onclick"><label for="$idCheck" $style_bg>$nombre</label>
         </div>
EOD;
      }

      $strClass = ($listColors)? ' with-color' : '';

      echo '<div class="WInputRadios WInputRadios_'.$name.$strClass.'">'.
              $strSelect.
           '</div>';
    }
    //------------------------------------------------------------------
    /* Con imagenes */
    public static function show2($name, $id_check, $listDatos, $id_selected)
    {
      $strSelect = '';

      // ¿Viene con claves?
      $isAsociativo = ($listDatos[0])? false : true;

      $nameCheck = $name.'['.$id_check.']';
      $idCheck   = $name.'_'.$id_check.'_';

      foreach($listDatos as $id=>$image)
      {
         if($isAsociativo == false) $id = $nombre;

         // Selected
         $SELECTED = '';
         if($id == $id_selected) $SELECTED = ' checked';

         $idCheck .= $id;

         // Option
         $strSelect .= <<<EOD
          <input type="radio" class="WInputRadio" id="$idCheck" name="$nameCheck" value="$id" $SELECTED><label class="WInputRadiosLabel" for="$idCheck" onclick="$idCheck.checked=true">$image</label>

EOD;
      }

      echo '<div class="WInputRadios2 WInputRadios2_'.$name.'">'.
              $strSelect.
           '</div>';
    }
    //----------------------------------------------------------------------
}
