<?
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 * Globals: $CONFIG_APP
 *          $CONFIG_DB
 *          $CONFIG_SECCIONES
 *
 *          Session
 *          $seccCtrl
 *          $objectsStatus
 *          Event
 *          $LOCAL
 *
 */

namespace angelrove\membrillo2;

use angelrove\membrillo2\Login\LoginCtrl;

use angelrove\membrillo2\WApp\Session;
use angelrove\membrillo2\WApp\Config_Secciones;
use angelrove\membrillo2\WApp\SeccCtrl;

use angelrove\membrillo2\WObjectsStatus\ObjectsStatus;
use angelrove\membrillo2\WObjectsStatus\Event;

use angelrove\utils\CssJsLoad;
use angelrove\utils\Vendor;


class AppCms extends Application
{
  //-----------------------------------------------------------------
  function __construct($document_root)
  {
    parent::__construct($document_root);
  }
  //-----------------------------------------------------------------
  function run()
  {
     parent::run();
     $this->runrun();
  }
  //-----------------------------------------------------------------
  function runrun()
  {
     $app = $this;

     //----------------------------------------------------
     /* Globals */
      global $CONFIG_APP,
             $CONFIG_DB,
             $CONFIG_SECCIONES,
             $seccCtrl,
             $objectsStatus,
             $LOCAL;

     //----------------------------------------------------
     /* Login */
      LoginCtrl::init();

     //----------------------------------------------------
     /* System Events */
      $this->system_services();

     //----------------------------------------------------
     /* System objects */
     //----------------------------------------------------
      // >> $CONFIG_SECCIONES -----
      $CONFIG_SECCIONES = Session::get('CONFIG_SECCIONES');
      if(!$CONFIG_SECCIONES)
      {
         $CONFIG_SECCIONES = Session::set('CONFIG_SECCIONES', new Config_Secciones());
         require(DOCUMENT_ROOT.'/app/CONFIG_SECC.inc');

         // Usuario: cuando se ha cargado el último objeto de sesión
         require(DOCUMENT_ROOT.'/app/onInitSession.inc');
      }

      // Sección por defecto
      if(!isset($_REQUEST['secc']) || !$_REQUEST['secc']) {
         header('Location: /'.$CONFIG_SECCIONES->getDefault().'/'); exit();
      }

      // >> $seccCtrl -------------
      $seccCtrl = Session::get('seccCtrl');

      // Inicio de la app o cambio de seccion: reiniciar
      if(!$seccCtrl || ($_REQUEST['secc'] != $seccCtrl->secc)) {
         $seccCtrl = Session::set('seccCtrl', new SeccCtrl($_REQUEST['secc']));
         //@include_once('./app/'.$CONFIG_SECCIONES->getFolder($seccCtrl->secc).'/onInitSecc.inc');
         $seccCtrl->initSecc();
      }
      else {
         $seccCtrl->initPage();
      }

      // >> $objectsStatus --------
      $objectsStatus = Session::get('objectsStatus');
      if(!$objectsStatus) {
         $objectsStatus = Session::set('objectsStatus', new ObjectsStatus());
      }
      $objectsStatus->initPage();

      // >> Event -----------------
      Event::initPage();

     //----------------------------------------------------
     /* Config front */
     //----------------------------------------------------
      $path_secc = './app/'.$CONFIG_SECCIONES->getFolder($seccCtrl->secc);

     // Lang ----------------------
      include_once('lang/es.inc');

     // CssJsLoad -----------------
      CssJsLoad::__init(CACHE_PATH, CACHE_URL);
      CssJsLoad::set_minify((IS_LOCALHOST? false : true));
      CssJsLoad::set_version(CACHE_VERSION);

      if(CACHE_CSSJS_DISABLED == 'auto') {
         CssJsLoad::set_cache_disabled((IS_LOCALHOST? true : false));
      } else {
         CssJsLoad::set_cache_disabled(CACHE_CSSJS_DISABLED);
      }

     // Load on init --------------
      require('_vendor_cssjs.inc');
      require(DOCUMENT_ROOT.'/_vendor_cssjs.inc');

      require(DOCUMENT_ROOT.'/app/onInitPage.inc');
      include($path_secc.'/onInitPage.inc');

     // Basics vendor css/js ------
      Vendor::usef('jquery');
      Vendor::usef('bootstrap');
      Vendor::usef('font-awesome');
      Vendor::usef('lightbox');

     //----------------------------------------------------
     /* OUT */
     //----------------------------------------------------
      // Events -----------
      if(Event::$EVENT) {
         $path_ctrl = $path_secc.'/ctrl_'.Event::$CONTROL;

         // oper
         if(Event::$OPER)
         {
            include($path_ctrl.'/oper.inc');

            if(Event::$REDIRECT_AFTER_OPER) {
               header('Location:./?CONTROL='.Event::$CONTROL.
                                 '&EVENT='  .Event::$EVENT.
                                 '&ROW_ID=' .Event::$ROW_ID.
                                 '&OPERED=' .Event::$OPER);

               Messages::set_debug('>> Redirected ---');
               exit();
            }
         }

         // flow
         if(file_exists($path_ctrl.'/flow.inc')) {
            include($path_ctrl.'/flow.inc');
         }
         else if(file_exists($path_secc.'/tmpl_main.inc')) {
            include($path_secc.'/tmpl_main.inc');
         }
         else {
            throw new \Exception('membrillo2: Default "flow.inc" or "tmpl_main.inc" not found in secc "/'.$seccCtrl->secc.'/".');
         }
      }
      // Default out ------
      else
      {
        include($path_secc.'/tmpl_main.inc');
      }

  }
  //-----------------------------------------------------------------
  private function system_services()
  {
     if(!isset($_REQUEST['APP_EVENT'])) {
        return true;
     }

     switch($_REQUEST['APP_EVENT'])
     {
       case 'close':
         Session::session_destroy();
       break;

       case 'download':
         $file     = $_REQUEST['f'];
         $fileUser = $_REQUEST['fu'];
         $mime     = $_REQUEST['mime'];

         header("Content-type: $mime;");
         header('Content-Disposition: attachment; filename="'.$fileUser.'";');
         readfile($file);

         exit();
       break;
     }
  }
  //-----------------------------------------------------------------
}
