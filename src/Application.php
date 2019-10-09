<?php
/**
 * NOTA: también puede ser llamado por un "cron" o por "ajax"
 *
 * @author José A. Romero Vegas <jangel.romero@gmail.com> 2006
 */

namespace angelrove\membrillo;

use angelrove\utils\Db_mysql;
use angelrove\utils\MyErrorHandler;
use angelrove\membrillo\WApp\Session;
use Illuminate\Database\Capsule\Manager as DB;

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
        define('PATH_MAIN', realpath($document_root.'/../..'));
        define('PATH_APP', realpath($document_root.'/..'));
        define('PATH_SRC', PATH_APP.'/src');
        define('PATH_PUBLIC', PATH_APP.'/public');

        // Cache ---
        define('CACHE_PATH', PATH_PUBLIC.'/cache');
        define('CACHE_URL', '/cache');

        // Logs ---
        define('PATH_LOG', PATH_MAIN.'/_logs');
        define('LOG_FILE_PREF', basename(PATH_APP).'-');

        //-------------------------------------
        /* Config */
        global $CONFIG_APP;
        require PATH_MAIN . '/config/app.php';

        //-------
        self::$conf    = & $CONFIG_APP;
        self::$conf_db = & $CONFIG_DB;

        // Web --------------------------------
        if (!$isConsole) {
            /* Error handler */
            if (!$isConsole) {
                MyErrorHandler::init(DISPLAY_ERRORS, PATH_LOG, LOG_FILE_PREF);
            }

            /* Config */
            require PATH_APP . '/config.php';

            /* Database */
            $this->initDatabase(self::$conf_db['default']);
            Db_mysql::debug_sql(DEBUG_SQL);

            /* Session start */
            Session::start(48);
        }
        // Console ----------------------------
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
        // "illuminate/database" ---
        $capsule = new DB;

        $capsule->addConnection([
            'driver'    => 'mysql',
            'host'      => $datosDb['HOST'],
            'database'  => $datosDb['DBNAME'],
            'username'  => $datosDb['USER'],
            'password'  => $datosDb['PASSWORD'],
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ]);

        // Make this Capsule instance available globally via static methods...
        $capsule->setAsGlobal();

        // Setup the Eloquent ORM...
        $capsule->bootEloquent();
    }
    //-----------------------------------------------------------------
}
