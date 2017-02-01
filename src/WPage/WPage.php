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
   public static $title;

   //----------------------------------------------------
   public static function start()
   {
    global $CONFIG_APP, $seccCtrl;

    // Set theme ------
    self::set_theme();

    if(!self::$title) {
       self::$title = $seccCtrl->title;
    }

    ?><!DOCTYPE html>
   <html lang="es">
   <head>
     <title><?=$CONFIG_APP['data']['TITLE']?></title>

     <meta charset="utf-8">
     <meta http-equiv="X-UA-Compatible" content="IE=edge">
     <meta name="viewport" content="width=device-width, initial-scale=1">

     <!-- css -->
     <? CssJsLoad::get_css(); ?>
     <!-- /css -->
   </head>
   <body>
     <?

     self::trazas();
     self::header();
     Navbar::show();

     ?><!-- content -->
     <main class="container-fluid"><?
      Messages::show();
      // echo '<h3>'.self::$title.'</h3><p>';
      Frame::start(self::$title);
   }
   //---------------------------------
   static function end() {
     Frame::end();
    ?></main>
    <!-- /content --><?

   include('tmpl_page_footer.inc');

   ?>

<!-- js -->
<? CssJsLoad::get_js(); ?>
<!-- /js -->

   </body>
   </html>
   <?
   }
   //----------------------------------------------------
   public static function trazas()
   {
     if(DEBUG_VARS == 0) {
        return;
     }

     ?><!-- Trazas --><?
     global $CONFIG_APP, $CONFIG_DB, $CONFIG_SECCIONES, $seccCtrl, $objectsStatus;
     $const = get_defined_constants(true);

     DebugTrace::out('- CONSTANTS -',    $const['user']);
     DebugTrace::out('CONFIG_APP',       $CONFIG_APP);
     DebugTrace::out('CONFIG_DB',        $CONFIG_DB);
     DebugTrace::out('CONFIG_SECCIONES', $CONFIG_SECCIONES);
     DebugTrace::out('seccCtrl',         $seccCtrl);
     DebugTrace::out('objectsStatus',    $objectsStatus);
     DebugTrace::out('Event', array('::EVENT'=>Event::$EVENT, '::OPER'=>Event::$OPER, '::CONTROL'=>Event::$CONTROL, '::ROW_ID'=>Event::$ROW_ID));
     DebugTrace::out('Login',           'User login: '.Login::$login);
     ?><!-- /Trazas --><?
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
        $strLogin = '<span class="userName">'.Login::$login.'</span> |
       <a href="/?APP_EVENT=close">Cerrar <i class="fa fa-sign-out"></i></a>';
     }

     // tmpl
     if($defaultTmpl) {
        include('tmpl_page_header.inc');
     }
     else {
        return $datos = array('titulo'  => $titulo,
                              'strLogin'=> $strLogin);
     }
   }
   //----------------------------------------------------
   // PRIVATE
   //----------------------------------------------------
   private static function set_theme()
   {
      global $CONFIG_APP;

      // Default --------
      if(!$CONFIG_APP['bootstrap']['theme'])
      {
         // Navbar::setInverse(true);

         CssJsLoad::set('https://maxcdn.bootstrapcdn.com/bootswatch/3.3.7/yeti/bootstrap.min.css');
         // CssJsLoad::set('https://maxcdn.bootstrapcdn.com/bootswatch/3.3.7/cerulean/bootstrap.min.css');
         // CssJsLoad::set('https://maxcdn.bootstrapcdn.com/bootswatch/3.3.7/slate/bootstrap.min.css');
         // CssJsLoad::set('https://maxcdn.bootstrapcdn.com/bootswatch/3.3.7/cosmo/bootstrap.min.css');
         // CssJsLoad::set('https://maxcdn.bootstrapcdn.com/bootswatch/3.3.7/umited/bootstrap.min.css');

         CssJsLoad::set(__DIR__.'/bootstrap_themes/update-basics.css');
         CssJsLoad::set(__DIR__.'/bootstrap_themes/update-metro.css');
         // CssJsLoad::set(__DIR__.'/bootstrap_themes/update-classic.css');
         // CssJsLoad::set(__DIR__.'/bootstrap_themes/update-yeti.css');

         /*
         switch($theme_options) {
          case 'compact':
            CssJsLoad::set_css('/classes/bootstrap/update-compact.css');
          break;
          case 'dark':
            CssJsLoad::set_css('/classes/bootstrap/update-dark.css');
          break;
         }
         */
      }
      // User -----------
      else
      {
         CssJsLoad::set($CONFIG_APP['bootstrap']['theme']);
      }

      // Update user (modifica el anterior)
      if($CONFIG_APP['bootstrap']['modify']) {
         CssJsLoad::set($CONFIG_APP['bootstrap']['modify']);
      }
   }
   //----------------------------------------------------
}
