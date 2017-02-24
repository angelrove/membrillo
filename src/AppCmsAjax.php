<?php
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 */

namespace angelrove\membrillo2;

use angelrove\membrillo2\Login\LoginCtrl;
use angelrove\membrillo2\WApp\Session;

class AppCmsAjax extends Application
{
    //-----------------------------------------------------------------
    public function __construct($document_root)
    {
        parent::__construct($document_root);
    }
    //-----------------------------------------------------------------
    public function run()
    {
        parent::run();
        $app = $this;

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
        $this->system_services();

        //----------------------------------------------------//
        /* User Service */
        $secc_dir = '';
        if (isset($_REQUEST['secc']) && $_REQUEST['secc']) {
            $secc_dir = DOCUMENT_ROOT . '/app/' . $_REQUEST['secc'];
        } else {
            $secc_dir = $CONFIG_SECCIONES->getFolder($seccCtrl->secc);
        }

        $service_path = $secc_dir . '/ajax-' . $_REQUEST['service'] . '.inc';

        // Load service ----
        try {
            if ((file_exists($service_path))) {
                include $service_path;
            } else {
                throw new \Exception("membrillo2 error: Service not found [$service_path]");
            }
        } catch (\Exception $e) {
            throw $e;
        }

    }
    //-----------------------------------------------------------------
    private function system_services()
    {
        if (!isset($_REQUEST['sys_service'])) {
            return true;
        }

        switch ($_REQUEST['sys_service']) {
            case 'Messages_get':
                Messages::get();
                break;
            default:
                throw new \Exception('membrillo2 error: service not found');
                break;
        }

        exit();
    }
    //-----------------------------------------------------------------
}
