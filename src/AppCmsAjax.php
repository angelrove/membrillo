<?
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 */

namespace angelrove\membrillo2;

use angelrove\membrillo2\Login\LoginCtrl;

use angelrove\membrillo2\WApp\Session;
use angelrove\membrillo2\WApp\Config_Secciones;
use angelrove\membrillo2\WApp\SeccCtrl;



class AppCmsAjax extends Application
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
     $app = $this;

     //----------------------------------------------------
     /* Globals */
      global $CONFIG_SECCIONES, $appLogin, $seccCtrl;

     //----------------------------------------------------//
      LoginCtrl::init_ajax();

      $CONFIG_SECCIONES = Session::get('CONFIG_SECCIONES');
      $seccCtrl         = Session::get('seccCtrl');
      //$seccCtrl->initPage();

      // System services ---------
      $this->systemEvents();

     //----------------------------------------------------//
     /** Usuario **/
      //include_once('onInitPage.inc');

      // Out
      $secc = '';
      if(isset($_REQUEST['secc'])) {
         if($_REQUEST['secc']) {
            $secc = $_REQUEST['secc'].'/';
         }
      }
      else {
         $secc = $CONFIG_SECCIONES->getFolder($seccCtrl->secc).'/';
      }

      require('./app/'.$secc.'ajax-'.$_REQUEST['service'].'.inc');

  }
  //-----------------------------------------------------------------
  private function systemEvents()
  {
     if(!isset($_REQUEST['sys_service'])) {
        return true;
     }

     switch($_REQUEST['sys_service'])
     {
        case 'Messages_get':
          Messages::ajax_show_msg();
          // echo 'ajax_show_msg-'.date('s');
        break;
     }

     exit();
  }
  //-----------------------------------------------------------------
}