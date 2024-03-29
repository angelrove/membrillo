<?php
/**
 * NOTA: también puede ser llamado por un "cron" o por "ajax"
 *
 * @author José A. Romero Vegas <jangel.romero@gmail.com> 2006
 */

namespace angelrove\membrillo;

use angelrove\membrillo\WApp\Session;
use angelrove\utils\MyErrorHandler;
use angelrove\utils\Db_mysql;
use angelrove\membrillo\Laravel\AliasLoader;
use Illuminate\Database\Capsule\Manager as DB;

class Application
{
    public static $conf    = array();
    public static $conf_db = array();

    //-----------------------------------------------------------------
    public function __construct(string $document_root, bool $isConsole = false)
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

        if (!file_exists(PATH_MAIN . '/config/app.php')) {
            die("Unable to load configuration file: './config/app.php'");
        }

        require PATH_MAIN . '/config/app.php';
        require __DIR__   . '/config_aliases.php';

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
            foreach (self::$conf_db as $key => $dbData) {
                $this->initDatabase($dbData, $key);
            }
            Db_mysql::debug_sql(DEBUG_SQL);

            /* Session start */
            $sessionId = $CONFIG_DB['default']['DBNAME'];
            Session::start($sessionId, 48);

            /* Alias loader */
            $this->aliases();
        }
        // Console ----------------------------
        else {
            /* DDBB */
            $DB_data = self::$conf_db['default'];
            $DB_data['HOST'] = 'localhost';
            $this->initDatabase($DB_data, 'default');
        }
    }
    //-----------------------------------------------------------------
    private function initDatabase(array $datosDb, $key='default'): void
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
        ], $key);

        // Make this Capsule instance available globally via static methods...
        $capsule->setAsGlobal();

        // Setup the Eloquent ORM...
        $capsule->bootEloquent();

        // Scape Request vars ---
        Db_mysql::parseRequest();
    }
    //-----------------------------------------------------------------
    /**
     * Get all of the aliases for all packages.
     *
     * @return array
     */
    private function aliases(): void
    {
        global $CONFIG_APP;

        AliasLoader::getInstance($CONFIG_APP['aliases'])->register();
    }
    //-----------------------------------------------------------------
}
