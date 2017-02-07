<?
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 *  Objetivo
 *  -----------------------------
 *  - Mostrar mensajes de aplicación.
 *  - Se guarda en SESSION para poder emitir el mensaje en otra pantalla mediante un ajax.
 *  - Eficaz en caso de redirecciónes.
 *  - La llamada apara obtener los mensajes se hace en js al final de la página así que no
 *    importa que el objeto html se incluya al comienzo de la página
 *
 *  Ejem.:
 *  Messages::set('El registro se ha guardado correctamente.');
 *  header("Location:...")
 *  Messages::show();
 *
 */

namespace angelrove\membrillo2;

use angelrove\utils\CssJsload;


class Messages
{
  private static $max_size = 1000;

  //----------------------------------------------------
  /*
   * $type: 'success', 'danger', 'debug'
   */
  public static function set($msg, $type='success')
  {
     // Max size ---
     if(strlen($_SESSION['Messages_msg'][$type]) > self::$max_size) {
        self::set_empty();
     }

     // Set ---
     $_SESSION['Messages_msg'][$type] .= '<div>'.$msg.'</div>';
  }
  //----------------------------------------------------
  public static function set_empty()
  {
     $_SESSION['Messages_msg']['success'] = '';
     $_SESSION['Messages_msg']['danger']  = '';
     $_SESSION['Messages_msg']['debug']   = '';
  }
  //----------------------------------------------------
  public static function set_debug($msg)
  {
     if(IS_LOCALHOST) {
        self::set('<pre>'.$msg.'</pre>', 'debug');
     }
  }
  //----------------------------------------------------
  public static function show()
  {
     CssJsLoad::set_script('
  $(document).ready(function() {
     $("#WApplication_msgs_load").load("/index_ajax.php?sys_service=Messages_get");
  });
', 'Messages');

     ?>
     <!-- Messages -->
     <style>
     .WApplication_msgs.alert-debug {
        z-index: 1; position: absolute; top: 0; left: initial; right: 0; background-color: #286090;
      }
     </style>

     <div id="WApplication_msgs_load"></div>
     <!-- /Messages -->
     <?
  }
  //----------------------------------------------------
  // Esta función es llamada por ajax
  public static function get()
  {
     if(!isset($_SESSION['Messages_msg'])) {
        $_SESSION['Messages_msg'] = array('success'=> '',
                                          'danger' => '',
                                          'debug' => '',
                                          );
     }

     // OUT ---
     foreach($_SESSION['Messages_msg'] as $type => $msg) {
        if(!$msg) {
           continue;
        }

        ?><div class="WApplication_msgs center-block2 alert alert-<?=$type?>" role="alert"><?=$msg?></div><?
     }

     // Empty ---
     self::set_empty();
  }
  //----------------------------------------------------
}
