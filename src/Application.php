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
    public function __construct($document_root, $isConsole = false)
    {
        ini_set('display_errors', 1);

        //-------------------------------------
        /* Globals */
        // Document root ---
        define('DOC_ROOT_MAIN', dirname($document_root));
        define('DOCUMENT_ROOT', $document_root);

        // Cache ---
        define('CACHE_PATH', $document_root.'/_cache');
        define('CACHE_URL', '/_cache');

        // Logs errors ---
        define('PATH_LOG', DOC_ROOT_MAIN.'/_logs');
        define('LOG_FILE_PREF', basename($document_root).'-');

        //-------------------------------------
        /* Config */
        global $CONFIG_APP;
        require DOC_ROOT_MAIN . '/config/app.php';

        //-------
        self::$conf    = & $CONFIG_APP;
        self::$conf_db = & $CONFIG_DB;

        //-------------------------------------

        if (!$isConsole) {
            /* Error handler */
            if (!$isConsole) {
                MyErrorHandler::init(DISPLAY_ERRORS, PATH_LOG, LOG_FILE_PREF);
            }

            /* Config */
            require DOCUMENT_ROOT . '/config.php';

            /* Database */
            $this->initDatabase(self::$conf_db['default']);
            Db_mysql::debug_sql(DEBUG_SQL);

            /* Session start */
            \angelrove\membrillo\WApp\Session::start(48);
        }
        //-------------------------------------
        else {
            /* DDBB */
            $DB_data = self::$conf_db['default'];
            $DB_data['HOST'] = 'localhost';
            $this->initDatabase($DB_data);
        }
    }
    //-----------------------------------------------------------------
    private function initDatabase($datosDb)
    {
        Db_mysql::getConn(
            $datosDb['HOST'],
            $datosDb['USER'],
            $datosDb['PASSWORD'],
            $datosDb['DBNAME']
        );
    }
    //-----------------------------------------------------------------
}
