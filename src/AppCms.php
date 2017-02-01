<?
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 * Objetos globales: $CONFIG_APP
 *                   $CONFIG_DB
 *
 *                   $CONFIG_SECCIONES
 *                   $seccCtrl
 *                   $LOCAL
 */

namespace angelrove\membrillo;

use angelrove\utils\MyErrorHandler;
use angelrove\utils\CssJsLoad;
use angelrove\utils\Vendor;

use angelrove\membrillo\Application;

use angelrove\membrillo\WApp\Session;
use angelrove\membrillo\WApp\Config_Secciones;
use angelrove\membrillo\WApp\SeccCtrl;

use angelrove\membrillo\WObjectsStatus\ObjectsStatus;
use angelrove\membrillo\WObjectsStatus\Event;

use angelrove\membrillo\Login\LoginCtrl;



class AppCms extends Application
{
  //-----------------------------------------------------------------
  function __construct($document_root)
  {
    parent::__construct($document_root);
    $app = $this;
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
     //----------------------------------------------------
     /* Globals */
      global $CONFIG_APP,
             $CONFIG_SECCIONES,
             $seccCtrl,
             $objectsStatus,
             $LOCAL;

     //----------------------------------------------------
      require(PATH_VENDOR.'/../_vendor_cssjs.inc');

      CssJsLoad::__init(CACHE_PATH, CACHE_URL);

      Vendor::usef('front-basics');
      include_once('local.inc');

     //----------------------------------------------------
     /* System Events */
      $this->systemEvents();

     //----------------------------------------------------
     /* Login */
      LoginCtrl::init();

     //----------------------------------------------------
     /* WApp objects */
     // >> $CONFIG_SECCIONES ---- [session]
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

     // >> $seccCtrl -----------
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

     // >> $objectsStatus ------
      $objectsStatus = Session::get('objectsStatus');
      if(!$objectsStatus) {
         $objectsStatus = Session::set('objectsStatus', new ObjectsStatus());
      }
      $objectsStatus->initPage();

     //----------------------------------------------------
     /* Section */
      $path_secc = './app/'.$CONFIG_SECCIONES->getFolder($seccCtrl->secc);

      require(DOCUMENT_ROOT.'/app/onInitPage.inc');
      include($path_secc.'/onInitPage.inc');

      // Events -----------
      if(Event::$EVENT)
      {
         $path_ctrl = $path_secc.'/ctrl_'.Event::$CONTROL.'/';
         $errors = '';

         // oper
         if(Event::$OPER)
         {
            if(isset($_REQUEST['appstatus-reload'])) {
            }
            else {
               include($path_ctrl.'oper.inc');

               // Redirigir al flow (evita problema de recargas)
               if(!$errors) {
                  header('Location:/'.$seccCtrl->secc.'/?CONTROL='.Event::$CONTROL.
                                                       '&EVENT='  .Event::$EVENT.
                                                       '&ROW_ID=' .Event::$ROW_ID.
                                                       '&OPERED=' .Event::$OPER);
               }
            }
         }

         // flow
         if(file_exists($path_ctrl.'flow.inc')) {
            include($path_ctrl.'flow.inc');
         }
         else {
            if(!(include $path_secc.'/tmpl_main.inc')) {
               WFrame_error('Default "tmpl_main" not found in secc "/'.$seccCtrl->secc.'/".<br />See log for more details.');
            }
         }
      }
      // Default out ------
      else
      {
        if(!(include $path_secc.'/tmpl_main.inc')) {
           echo('Default "tmpl_main" not found in secc "/'.$seccCtrl->secc.'/".<br />See log for more details.');
         }
      }

  }
  //-----------------------------------------------------------------
  private function systemEvents()
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
