<?
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 */

namespace angelrove\membrillo2\WPage;


use angelrove\utils\CssJsLoad;

use angelrove\membrillo2\Login\Login;
use angelrove\membrillo2\DebugTrace;
use angelrove\membrillo2\Messages;
use angelrove\membrillo2\WObjectsStatus\Event;


class WPage
{
   public  static $title = false;
   private static $pagekey = '';
   private static $view_empty = false;

   //----------------------------------------------------
   public static function set_view_empty()
   {
     self::$view_empty = true;
   }
   //----------------------------------------------------
   public static function add_pagekey($key)
   {
     self::$pagekey .= $key.' ';
   }
   //----------------------------------------------------
   public static function get()
   {
     global $CONFIG_APP, $seccCtrl;

     // Title ---
     if(self::$title === false) {
        self::$title = $seccCtrl->title;
     }

     ?><!DOCTYPE html>
    <html lang="es">
    <head>
      <meta charset="utf-8">
      <meta name="author" content="https://github.com/angelrove">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">

      <title><?=$CONFIG_APP['data']['TITLE']?></title>

      <!-- css -->
      <? CssJsLoad::get_css(); ?>
      <!-- /css -->
    </head>
    <body class="pagekey_<?=self::$pagekey?>">
      <? self::debug_objects() ?>

      <!-- Header -->
      <? if(!self::$view_empty) { ?>
        <header class="page-header container-fluid">
          <? self::header() ?>
        </header>

        <!-- Navbar -->
        <? Navbar::get(); ?>
      <? } ?>

      <!-- main -->
      <main class="container">
        <div class="page-header"><h2 id="forms"><?=self::$title?></h1></div>
        <? Messages::show() ?>

        <?
   }
   //---------------------------------
   public static function get_end()
   {

        ?>
      </main>
      <!-- /main -->

      <?
      if(!self::$view_empty) {
         include('tmpl_page_footer.inc');
      }
      ?>

      <!-- js -->
      <? CssJsLoad::get_js(); ?>
      <!-- /js -->

   </body>
   </html>
   <?
   }
   //----------------------------------------------------
   public static function debug_objects()
   {
     if(!DEBUG_VARS) {
        return;
     }

     ?><!-- debug_objects --><?
     global $CONFIG_APP, $CONFIG_DB, $CONFIG_SECCIONES, $seccCtrl, $objectsStatus;
     $const = get_defined_constants(true);

     DebugTrace::out('- CONSTANTS -',    $const['user']);
     DebugTrace::out('CONFIG_APP',       $CONFIG_APP);
     DebugTrace::out('CONFIG_DB',        $CONFIG_DB);
     DebugTrace::out('CONFIG_SECCIONES', $CONFIG_SECCIONES);
     DebugTrace::out('seccCtrl',         $seccCtrl);
     DebugTrace::out('objectsStatus',    $objectsStatus);
     DebugTrace::out('Event', array('::EVENT'=>Event::$EVENT, '::OPER'=>Event::$OPER, '::CONTROL'=>Event::$CONTROL, '::ROW_ID'=>Event::$ROW_ID));
     DebugTrace::out('Login', 'User login: '.Login::$login);
     ?><!-- /debug_objects --><?
   }
   //----------------------------------------------------
   public static function header($defaultTmpl=true)
   {
     global $app, $CONFIG_APP;

     $titulo = $CONFIG_APP['data']['TITLE'];
     if(!$titulo && !Login::$login) {
        return;
     }

     // title
     // if(isset($CONFIG_APP['domain']['title_img']) && $CONFIG_APP['domain']['title_img']) {
     //    $titulo = '<img src="/_images/'.$CONFIG_APP['data']['TITLE_IMG'].'">';
     // }

     // user
     $strLogin = '';
     if(Login::$login) {
        $strLogin = '<span class="userName">'.Login::$login.'</span> | '.
                    '<a href="/?APP_EVENT=close">Close <i class="fa fa-sign-out fa-lg"></i></a>';
     }

     // tmpl ------
     if($defaultTmpl) {
         ?>
         <h1 class="pull-left"><?=$titulo?></h1>
         <div class="pull-right"><?=$strLogin?></div>
         <?
     }
     else {
        return $datos = array('titulo'  => $titulo,
                              'strLogin'=> $strLogin);
     }
   }
   //----------------------------------------------------
}
