<?
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 */

namespace angelrove\membrillo2;


class CrudUrl
{
   //-------------------------------------------------------------
   public static function get($event='', $control=0, $id='', $oper='', $other_params='')
   {
      $params = array();

      //----
      if($id) {
         $params[] = "ROW_ID=$id";
      }
      if($oper) {
         $params[] = "OPER=$oper";
      }
      if($other_params) {
         $params[] = $other_params;
      }
      $params = implode('&', $params);

      if($params) {
         $params = '?'.$params;
      }

      //----
      if($event) {
         $event = "/$event";
      }

      return '/'.$_GET['secc'].'/crd/'.$control.$event.'/'.$params;
   }
   //-------------------------------------------------------------
   public static function get_nocrud($params='')
   {
      return '/'.$_GET['secc'].'/?'.$params;
   }
  //-------------------------------------------------------------
}