<?php
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
 *          Local
 *
 */

namespace angelrove\membrillo2;

use angelrove\membrillo2\Login\LoginCtrl;
use angelrove\membrillo2\WApp\Config_Secciones;
use angelrove\membrillo2\WApp\SeccCtrl;
use angelrove\membrillo2\WApp\Session;
use angelrove\membrillo2\WApp\Local;
use angelrove\membrillo2\WObjectsStatus\Event;
use angelrove\membrillo2\WObjectsStatus\ObjectsStatus;
use angelrove\utils\CssJsLoad;
use angelrove\utils\Vendor;


class AppCms extends Application
{
    public static $t = array();

    //-----------------------------------------------------------------
    public function __construct($document_root)
    {
        parent::__construct($document_root);

        //----------------------------------------------------
        /* Globals */
        global $CONFIG_APP,
               $CONFIG_DB,
               $CONFIG_SECCIONES,
               $seccCtrl,
               $objectsStatus;

        //----------------------------------------------------
        /* Login */
        LoginCtrl::init();

        //----------------------------------------------------
        /* Local */
        Local::_init();

        //----------------------------------------------------
        /* System Events */
        $this->system_services();

        //----------------------------------------------------
        /* System objects */
        //----------------------------------------------------
        // >> $CONFIG_SECCIONES -----
        $CONFIG_SECCIONES = Session::get('CONFIG_SECCIONES');
        if (!$CONFIG_SECCIONES) {
            $CONFIG_SECCIONES = Session::set('CONFIG_SECCIONES', new Config_Secciones());
            require DOCUMENT_ROOT . '/app/CONFIG_SECC.inc';

            // Usuario: cuando se ha cargado el último objeto de sesión
            require DOCUMENT_ROOT . '/app/onInitSession.inc';
        }

        // Sección por defecto
        if (!isset($_REQUEST['secc']) || !$_REQUEST['secc']) {
            header('Location: /' . $CONFIG_SECCIONES->getDefault() . '/');exit();
        }

        // >> $seccCtrl -------------
        $seccCtrl = Session::get('seccCtrl');

        // Inicio de la app o cambio de seccion: reiniciar
        if (!$seccCtrl || ($_REQUEST['secc'] != $seccCtrl->secc)) {
            $seccCtrl = Session::set('seccCtrl', new SeccCtrl($_REQUEST['secc']));
            $seccCtrl->initSecc();
        } else {
            $seccCtrl->initPage();
        }

        // >> $objectsStatus --------
        $objectsStatus = Session::get('objectsStatus');
        if (!$objectsStatus) {
            $objectsStatus = Session::set('objectsStatus', new ObjectsStatus());
        }
        $objectsStatus->initPage();

        //----------------------------------------------------
        /* Config front */
        //----------------------------------------------------

        // CssJsLoad -----------------
        CssJsLoad::__init(CACHE_PATH, CACHE_URL);
        CssJsLoad::set_minify(CSSJSLOAD_MINIFY);
        CssJsLoad::set_version(CACHE_VERSION);
        CssJsLoad::set_cache_disabled(CACHE_CSSJS_DISABLED);

        //----------------------------------------------------
        /* Load on init */
        //----------------------------------------------------
        require __DIR__ . '/_vendor_cssjs.inc';
        CssJsLoad::set(__DIR__ . '/_themes/_basics.css');

        require DOCUMENT_ROOT . '/_vendor_cssjs.inc';
        require DOCUMENT_ROOT . '/app/onInitPage.inc';

        // Basics vendor css/js -----
        Vendor::usef('jquery');
        Vendor::usef('bootstrap');
        Vendor::usef('font-awesome');
        Vendor::usef('material-icons');
        Vendor::usef('lightbox');

        //----------------------------------------------------
        /* Parse event */
        //----------------------------------------------------
        Event::initPage();

        $path_secc = $CONFIG_SECCIONES->getFolder($seccCtrl->secc);
        $objectsStatus->parseEvent($path_secc);
    }
    //-----------------------------------------------------------------
    private function system_services()
    {
        if (!isset($_REQUEST['APP_EVENT'])) {
            return true;
        }

        switch ($_REQUEST['APP_EVENT']) {
            case 'close':
                Session::session_destroy();
                break;

            case 'local':
                Local::setLang($_GET['val']);
                Session::session_destroy();
                break;

            case 'download':
                $file     = $_REQUEST['f'];
                $fileUser = $_REQUEST['fu'];
                $mime     = $_REQUEST['mime'];

                header("Content-type: $mime;");
                header('Content-Disposition: attachment; filename="' . $fileUser . '";');
                readfile($file);

                exit();
                break;
        }
    }
    //-----------------------------------------------------------------
}
