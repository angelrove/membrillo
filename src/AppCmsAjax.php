<?php
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 * >> $_REQUEST: 'ajaxsv', 'sys_ajaxsv'
 */

namespace angelrove\membrillo;

use angelrove\membrillo\Login\LoginCtrl;
use angelrove\membrillo\WApp\Session;
use angelrove\membrillo\WObjectsStatus\Event;

class AppCmsAjax extends Application
{
    //-----------------------------------------------------------------
    public function __construct(string $document_root)
    {
        parent::__construct($document_root);

        //----------------------------------------------------
        /* Globals */
        global $CONFIG_SECCIONES,
               $seccCtrl,
               $objectsStatus;

        //----------------------------------------------------//
        LoginCtrl::init_ajax();

        $CONFIG_SECCIONES = Session::get('CONFIG_SECCIONES');
        $seccCtrl         = Session::get('seccCtrl'); //$seccCtrl->initPage();
        $objectsStatus    = Session::get('objectsStatus');

        //----------------------------------------------------
        /* System services */
        $this->systemServices();

        //----------------------------------------------------
        /* Load service */
        // Parse event (get object_id, event, oper, item_id) ---
        Event::initPage();
        if (!Event::$EVENT) {
            throw new \Exception("membrillo ajax error: Service not found");
        }

        // Object status
        $wObjectStatus = $objectsStatus->setNewObject(Event::$CONTROL); // if no exist
        $wObjectStatus->updateDatos();

        //----------------------------------------------------
        // onInitPage ---
        require PATH_SRC . '/onInitPage.inc';

        // onInitPage
        $path_secc = $CONFIG_SECCIONES->getFolder($seccCtrl->secc);
        @include $path_secc . '/onInitPage.inc';

        //----------------------------------------------------
        // Main controller
        EventController::parseAjaxEvent($wObjectStatus);
    }
    //-----------------------------------------------------------------
    private function systemServices()
    {
        if (!isset($_REQUEST['sys_ajaxsv'])) {
            return true;
        }

        switch ($_REQUEST['sys_ajaxsv']) {
            case 'Messages_get':
                Messages::get();
                break;

            default:
                throw new \Exception('membrillo error: service not found');
                break;
        }

        exit();
    }
    //-----------------------------------------------------------------
}
