<?
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 */

namespace angelrove\membrillo2\WInputs;

use angelrove\utils\UtilsBasic;


class WInputRadios
{
    //------------------------------------------------------------------
    /* From array */
    public static function get($name, $listDatos, $id_selected, $listColors=array(), $is_assoc='')
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
         if($isAsociativo == false) {
            $id = $nombre;
         }

         // Selected
         $SELECTED = '';
         if($id == $id_selected) $SELECTED = ' checked';

         $idCheck = $name.'_'.$id;

         // Color
         $style_bg = '';
         if(isset($listColors[$id])) {
            $style_bg = 'style="background:'.$listColors[$id].'"';
         }

         // Option
         $strSelect .= <<<EOD
         <label class="radio-inline" $style_bg>
           <input type="radio"
                id="$idCheck"
                name="$name"
                value="$id" $SELECTED>
           $nombre
         </label>
EOD;
      }

      return '<div class="WInputRadios" id="WInputRadios_'.$name.'">'.
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
          <label class="radio-inline">
            <input type="radio"
                   id="$idCheck"
                   name="$nameCheck"
                   value="$id" $SELECTED>
            $image
          </label>
EOD;
      }

      return  '<div class="WInputRadios_img" id="WInputRadios_img_'.$name.'">'.
                 $strSelect.
              '</div>';
    }
    //----------------------------------------------------------------------
}
