<?php
/**
 * José A. Romero Vegas, 2006
 * jangel.romero@gmail.com
 *
   Objetivo
   -----------------------------
   setInstantMsg():
   - Se almacena en SESSION y una vez mostrado se elimina de la sesión
   - Mostrar mensajes de aplicación.
     Ejem.: "El campo Nombre es obligatorio",
            "Los datos se han actualizado correctamente",
            "Debug: SELECT * FROM cosa"

   setMsg():
   - Eventos: acciones realizadas por un usuario deben emitir una notificación para otros usuarios.
   - Alertas/Mensajes: un usuario necesita enviar un mensaje/recordatorio a si mismo o a otros usuarios.

   BBDD
   -----------------------------
   sys_messages: id, id_user, tag, msg, resolved

   WMessages.inc
   -----------------------------
   class WMessages {
     setMsg($id_user, $tag); // BBDD
     setInstantMsg($tag);       // SESSION

     delMsg($tag);

     show();
     getMsg();
   }

   ajax-WMessages_getMsg.inc
   -----------------------------
   Messages::getMsg();

   WApplication.inc
   -----------------------------
   Messages::show();

   oper.inc
   -----------------------------
   Messages::setInstantMsg('El registro se ha guardado correctamente.');
   Messages::setMsg(27, 'Prueba');

 */

namespace angelrove\membrillo;


class WMessages
{
  //----------------------------------------------------
  public static function setInstantMsg($tag)
  {
    $_SESSION['WMessages_msg'] .= '<div>'.$tag.'</div>';
  }
  //----------------------------------------------------
  // BBDD
  //----------------------------------------------------
  public static function setMsg($id_user, $tag) {
    $sqlQ = "INSERT INTO sys_messages(id_user, tag) VALUES('$id_user', '$tag')";
    Db_mysql::query($sqlQ);
  }
  //----------------------------------------------------
  public static function delMsg($tag) {
    $sqlQ = "DELETE FROM sys_messages WHERE id_user='".Login::$user_id."' AND tag='$tag'";
    Db_mysql::query($sqlQ);
  }
  //----------------------------------------------------
  // GETs
  //----------------------------------------------------
  public static function show() {
    ?>
    <!-- Mensaje -->
    <script>
     $(document).ready(function() {
       getMsg(); // obtener los mensajes acumulados de un usuario
     });

     function getMsg() {
       var minutos = 1;
       $("#WMessages_out").load("/index_ajax.php?secc=0&service=WMessages_getMsg", function(ret) {
         window.setTimeout("getMsg()", (minutos * 60000)); // refresco
       });
     }
    </script>
    <table class="WApplication_msgs" align="center"><tr id="WMessages_out"></tr></table>
    <!-- /Mensaje -->

    <?php
  }
  //----------------------------------------------------
  public static function getMsg() {

    $txtMsg = '';

    // BBDD ------
    $sqlQ = "SELECT tag AS id, count(id) AS num
             FROM sys_messages
             WHERE id_user='".Login::$user_id."'
             GROUP BY tag";
    $msgs = Db_mysql::getList($sqlQ);
    if($msgs) {
       foreach($msgs as $msg) {
          if(!$msg['num']) continue;
          $txtMsg .= "<b>$msg[id]</b>(<b>$msg[num]</b>)<br>";
          //$txtMsg .= "$msg[msg]<br>";
       }
    }

    // SESSION ---
    if($_SESSION['WMessages_msg']) {
       $txtMsg .= $_SESSION['WMessages_msg'];
       $_SESSION['WMessages_msg'] = '';
    }

    return $txtMsg;
  }
  //----------------------------------------------------
}
