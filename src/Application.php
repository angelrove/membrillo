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
        define('IS_LOCALHOST', ($_SERVER['REMOTE_ADDR'] == '::1') ? true : false);

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

        //-------
        $pref_file = (IS_LOCALHOST) ? 'dev' : 'prod';
        require BASE_DIR . '/config_host_' . $pref_file . '.inc';

        //-------
        $APP_TYPE = '';
        require DOCUMENT_ROOT . '/config_host.inc';

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
        /* Config app */
        require 'config_app.inc';

        //-------------------------------------
        /* Session start */
        session_start();
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
