<?php
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 * Globals: $CONFIG_APP
 *          $CONFIG_DB
 *          $CONFIG_SECCIONES
 *
 *          $seccCtrl
 *          $objectsStatus
 *          Event
 *          $LOCAL
 *
 */

namespace angelrove\membrillo2;

use angelrove\membrillo2\WApp\Config_Secciones;
use angelrove\membrillo2\WApp\SeccCtrl;
use angelrove\membrillo2\WObjectsStatus\Event;
use angelrove\membrillo2\WObjectsStatus\ObjectsStatus;


class AppApi extends Application
{
    public static $lang = array();

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
               $objectsStatus,
               $LOCAL;

        //----------------------------------------------------
        /* System objects */
        //----------------------------------------------------
        // >> $CONFIG_SECCIONES -----
        $CONFIG_SECCIONES = new Config_Secciones();
        require DOCUMENT_ROOT . '/app/CONFIG_SECC.inc';

        // >> $seccCtrl -------------
        $seccCtrl = new SeccCtrl($_REQUEST['secc']);
        $seccCtrl->initSecc();

        // >> $objectsStatus --------
        $objectsStatus = new ObjectsStatus();
        $objectsStatus->initPage();

        //----------------------------------------------------
        /* Config front */
        //----------------------------------------------------
        // Lang ----------------------
        include_once 'lang/es.inc';

        //----------------------------------------------------
        /* Load on init */
        //----------------------------------------------------
        require DOCUMENT_ROOT . '/app/onInitPage.inc';

        //----------------------------------------------------
        /* Parse event */
        //----------------------------------------------------
        header('Content-Type: application/json');

        Event::initPage_api();

        $path_secc = $CONFIG_SECCIONES->getFolder($seccCtrl->secc);
        $objectsStatus->parseEvent_api($path_secc);
    }
    //-----------------------------------------------------------------
}
