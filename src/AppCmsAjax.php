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

        //----------------------------------------------------//
        /* Load on init */
        require PATH_SRC . '/onInitPage.inc';

        //----------------------------------------------------
        /* Load service old version */
        if (isset($_REQUEST['service'])) {
            $this->loadService_old($seccCtrl);
        }
        /* Load service */
        else {
            Event::initPage();
            if (!Event::$EVENT) {
                throw new \Exception("membrillo error: Service not found");
            }

            $path_secc = $CONFIG_SECCIONES->getFolder($seccCtrl->secc);
            $objectsStatus->parseEvent($path_secc);
        }
    }
    //-----------------------------------------------------------------
    private function loadService_old($seccCtrl)
    {
        global $CONFIG_SECCIONES;
        $path_secc = $CONFIG_SECCIONES->getFolder($seccCtrl->secc);

        // Load service ----
        try {
            $service_path = $path_secc . '/ajax-' . $_REQUEST['service'] . '.inc';
            if (file_exists($service_path)) {
                include $service_path;
            } else {
                throw new \Exception("membrillo error: Service not found [$service_path]");
            }
        } catch (\Exception $e) {
            throw $e;
        }

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
