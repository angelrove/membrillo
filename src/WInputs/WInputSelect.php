<?
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 */

namespace angelrove\membrillo2\WInputs;

use angelrove\utils\Db_mysql;


class WInputSelect
{
    //------------------------------------------------------------------
    /**
     * from Sql
     * Ejem..: $sqlQ = "SELECT idusuario AS id, CONCAT(apellido,' ',nombre) AS nombre FROM usuarios";
     *  $selected: puede ser un id o una lista de IDs (para selects multiples)
     */
    public static function get($sqlQ, $selected)
    {
      if(!$sqlQ) return '';
      $strSelect = '';
      $selected_isArray = is_array($selected);

      $rows = Db_mysql::getList($sqlQ);
      foreach($rows as $id => $row) {
         $nombre = $row['nombre'];
         if(!$nombre) $nombre = $id;

         // Selected
         $SELECTED = '';
         if($selected_isArray) {
            if(array_search($id, $selected) !== false) $SELECTED = 'SELECTED';
         } else {
            if($id == $selected) $SELECTED = 'SELECTED';
         }

         // Option
         $strSelect .= "<option value=\"$id\" $SELECTED>$nombre</option>";
      }

      return $strSelect;
    }
    //------------------------------------------------------------------
    /**
     * from array
     * $tipoId: AUTO_INCR, AUTO_VALUE
     */
    public static function getFromArray($datos, $id_selected, $tipoId='', $listColors='', $listGroup=array())
    {
      $strSelect = '';

      foreach($datos as $id=>$nombre) {
         if($tipoId == 'AUTO_VALUE') {
            $id = $nombre;
         }
         if(is_array($nombre)) {
            $nombre = $nombre['nombre'];
         }

         // Selected
         $SELECTED = '';
         if($id == $id_selected) {
            $SELECTED = ' SELECTED';
         }

         // optgroup
         if($listGroup[$id]) {
            if($strSelect) {
               $strSelect .= '</optgroup>';
            }
            $strSelect .= '<optgroup label="'.$listGroup[$id].'">';
         }

         // Option
         $style = '';
         if($listColors) {
            $style = 'style="background:'.$listColors[$id].'"';
         }
         $strSelect .= '<option '.$style.' value="'.$id.'"'.$SELECTED.'>'.$nombre.'</option>';
      }
      if($listGroup) {
         $strSelect .= '</optgroup>';
      }

      return $strSelect;
    }
    //------------------------------------------------------------------
}
