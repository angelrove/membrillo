<?php
/**
 * NOTA: también puede ser llamado por un "cron" o por "ajax"
 *
 * @author José A. Romero Vegas <jangel.romero@gmail.com> 2006
 */

namespace angelrove\membrillo;

use angelrove\utils\Db_mysql;
use angelrove\utils\MyErrorHandler;

include_once 'print_r2.php';


class Application
{
    public static $conf    = array();
    public static $conf_db = array();

    //-----------------------------------------------------------------
    public function __construct($document_root, $isConsole=false)
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
        require BASE_DIR . '/cache_version.php';
        require BASE_DIR . '/config/app.php';

        // Config file -------
        $APP_TYPE = '';
        require DOCUMENT_ROOT . '/config_host.php';

        //-------
        self::$conf    = & $CONFIG_APP;
        self::$conf_db = & $CONFIG_DB;

        //-------------------------------------

        if (!$isConsole) {
            /* Error handler */
            if (!$isConsole) {
                MyErrorHandler::init(
                    $CONFIG_APP['errors']['display'],
                    $CONFIG_APP['errors']['path_log'],
                    $CONFIG_APP['errors']['log_file_pref']
                );
            }

            /* DDBB */
            if (isset(self::$conf_db['default'])) {
                $this->init_database(self::$conf_db['default']);
            }

            //-------------------------------------
            /* Config file */
            require DOCUMENT_ROOT . '/config_app.php';

            //-------------------------------------
            /* Session start */
            \angelrove\membrillo\WApp\Session::start(24);
        }
        //-------------------------------------
        else {
            /* DDBB */
            $DB_data = self::$conf_db['default'];
            $DB_data['HOST'] = 'localhost';
            $this->init_database($DB_data);
        }
    }
    //-----------------------------------------------------------------
    private function init_database($datosDb)
    {
        Db_mysql::getConn(
            $datosDb['HOST'], $datosDb['USER'], $datosDb['PASSWORD'], $datosDb['DBNAME']
        );
    }
    //-----------------------------------------------------------------
}
