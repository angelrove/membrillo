<?php
/**
 * NOTA: también puede ser llamado por un "cron" o por "ajax"
 *
 * @author José A. Romero Vegas <jangel.romero@gmail.com> 2006
 */

namespace angelrove\membrillo2;

use angelrove\utils\Db_mysql;
use angelrove\utils\MyErrorHandler;

include_once 'print_r2.php';


class Application
{
    public static $conf    = array();
    public static $conf_db = array();

    //-----------------------------------------------------------------
    public function __construct($document_root)
    {
        ini_set('display_errors', 1);

        //-------------------------------------
        /* Globals */
        define('DOCUMENT_ROOT', $document_root);
        define('DOC_ROOT', $document_root);
        define('BASE_DIR', dirname($document_root));

        //-------------------------------------
        /* Config */
        global $CONFIG_APP;

        $CONFIG_APP = array(
            'errors' => array(
                'path_log'      => '',
                'log_file_pref' => '',
                'display'       => '',
            ),
        );

        // Config file -------
        require BASE_DIR . '/config_host.php';

        // Config file -------
        $APP_TYPE = '';
        require DOCUMENT_ROOT . '/config_host.php';

        //-------
        self::$conf    = & $CONFIG_APP;
        self::$conf_db = & $CONFIG_DB;

        //-------------------------------------
        /* Error handler */
        MyErrorHandler::init(
            $CONFIG_APP['errors']['display'],
            $CONFIG_APP['errors']['path_log'],
            $CONFIG_APP['errors']['log_file_pref']
        );

        //-------------------------------------
        /* DDBB */
        $this->init_database();

        //-------------------------------------
        /* Config file */
        require DOCUMENT_ROOT . '/config_app.php';

        //-------------------------------------
        /* Session start */
        \angelrove\membrillo2\WApp\Session::start(24);
    }
    //-----------------------------------------------------------------
    private function init_database()
    {
        if (!isset(self::$conf_db['default'])) {
            return;
        }

        $datosDb = self::$conf_db['default'];

        Db_mysql::getConn(
            $datosDb['HOST'], $datosDb['USER'], $datosDb['PASSWORD'], $datosDb['DBNAME']
        );
    }
    //-----------------------------------------------------------------
}
