<?
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 * 2006
 *
 * NOTA: también puede ser llamado por un "cron" o por "ajax"
 *
 */

namespace angelrove\membrillo;

use angelrove\utils\MyErrorHandler;
use angelrove\utils\UtilsBasic;
use angelrove\utils\Db_mysql;

include_once('print_r2.php');


class Application
{
  //-----------------------------------------------------------------
  function __construct($document_root)
  {
    define('DOCUMENT_ROOT', $document_root);
    define('BASE_DIR', dirname($document_root));
  }
  //-----------------------------------------------------------------
  function run()
  {
    global $CONFIG_APP, $CONFIG_DB;

    define('IS_LOCALHOST', ($_SERVER['REMOTE_ADDR'] == '::1')? true : false);

    //----------------------------------------------------
    ini_set('display_errors', 1);

    //----------------------------------------------------
    /* Config */
    $CONFIG_APP = array(
      'errors' => array(
         'path_log' => '',
         'log_file_pref' => '',
         'display'       => ''
      )
    );

    //-------
    $pref_file = (IS_LOCALHOST)? 'dev': 'prod';
    require(BASE_DIR.'/config_host_'.$pref_file.'.inc');

    //-------
    $APP_TYPE = '';
    require(DOCUMENT_ROOT.'/config_host.inc');

    //----------------------------------------------------
    /* Error handler */
    MyErrorHandler::init($CONFIG_APP['errors']['display'],
                         $CONFIG_APP['errors']['path_log'],
                         $CONFIG_APP['errors']['log_file_pref']);

    //----------------------------------------------------
    /* BBDD */
     if(isset($CONFIG_DB['default'])) {
        $datosDb = $CONFIG_DB['default'];
        Db_mysql::getConn($datosDb['HOST'], $datosDb['USER'], $datosDb['PASSWORD'], $datosDb['DBNAME']);
     }

    //----------------------------------------------------
    /* Config app */
     require('config_app.inc');

    //----------------------------------------------------
    /* Session start */
     session_start();
  }
  //-----------------------------------------------------------------
}
