<?
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 *  Objetivo
 *  -----------------------------
 *  - Mostrar mensajes de aplicación.
 *  - Se guarda en SESSION: de esta manera no importa en que lugar de la app. se emita el mensaje.
 *    Ejem.: "El campo Nombre es obligatorio",
 *           "Los datos se han actualizado correctamente",
 *           "Debug: SELECT * FROM cosa"
 *
 *  oper.inc
 *  -----------------------------
 *  Messages::set('El registro se ha guardado correctamente.');
 *
 *  WApplication.inc
 *  -----------------------------
 *  Messages::show();
 *
 */

namespace angelrove\membrillo;

// use angelrove\utils\CssJsload;


class Messages
{
  //----------------------------------------------------
  public static function set($msg, $type='success')
  {
    $_SESSION['Messages_msg'][$type] .= '<div>'.$msg.'</div>';
  }
  //----------------------------------------------------
  public static function show()
  {
//     CssJs_load::set_script('
// $(document).ready(function() {
//    $("#WApplication_msgs_load").load("/index_ajax.php?secc=0&sys_service=Messages_get");
// });
// ', 'Messages');

    ?>

    <!-- Messages -->
    <div id="WApplication_msgs_load"></div>
    <!-- /Messages -->
    <?
  }
  //----------------------------------------------------
  public static function ajax_show_msg()
  {
    if(!isset($_SESSION['Messages_msg'])) {
       $_SESSION['Messages_msg'] = array('success'=>'', 'danger'=>'');
    }

    // OUT ---
    foreach($_SESSION['Messages_msg'] as $type => $msg)
    {
       if(!$msg) {
          continue;
       }
       ?>
       <div class="WApplication_msgs alert alert-<?=$type?>" role="alert">
          <?=$msg?>
       </div>
       <?
       $_SESSION['Messages_msg'][$type] = '';
    }
  }
  //----------------------------------------------------
}
