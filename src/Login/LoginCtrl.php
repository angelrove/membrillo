<?php
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 */

namespace angelrove\membrillo\Login;

use angelrove\utils\Db_mysql;


class LoginCtrl
{
  private static $iLoginQuery;

  //----------------------------------------------------------
  public static function init_ajax()
  {
    self::init(true);
  }
  //----------------------------------------------------------
  public static function init($isAjax=false)
  {
    global $CONFIG_APP;

    // No login
    if(!$CONFIG_APP['login']['LOGIN']) {
       return true;
    }

    // Logged
    Login::init();
    if(Login::$user_id) {
       return true;
    }

    // Set login
    if($isAjax == true) {
       die('Restricted area.');
    } else {
       self::initPage();
    }
  }
  //------------------------------------------------
  public static function set_iLoginQuery(LoginQueryInterface $iLoginQuery)
  {
    self::$iLoginQuery = $iLoginQuery;
  }
  //------------------------------------------------
  public static function initPage()
  {
    global $CONFIG_APP;

    // Authenticate --------------
    $userData = array();

    if( (isset($_REQUEST['LOGIN_USER']) && $_REQUEST['LOGIN_USER']) || isset($_REQUEST['auth_token']) )
    {
       // User query
       $sqlQ = '';
       if(isset(self::$iLoginQuery)) {
          $sqlQ = self::$iLoginQuery->get($_REQUEST['LOGIN_USER'], $_REQUEST['LOGIN_PASSWD'], $_REQUEST);
       }
       // Default query
       else {
          $login_table = $CONFIG_APP['login']['LOGIN_TABLE'];
          $sqlQ = "SELECT * FROM $login_table
                   WHERE login  = '$_REQUEST[LOGIN_USER]' AND
                         passwd = '$_REQUEST[LOGIN_PASSWD]' AND
                         deleted_at IS NULL";
       }

       if(is_array($sqlQ)) {
          $userData = $sqlQ;
       } else {
          $userData = Db_mysql::getRow($sqlQ);
       }

       // Login ok
       if(isset($userData['id'])) {
           new Login($userData['id'], $userData['login'], $userData);
           header("Location: /?APP_EVENT=timezone"); exit();
       }

    }

    // Authenticate form view -------
    if(!Login::$user_id)
    {
        global $CONFIG_APP;

        $msg = (isset($_REQUEST['LOGIN_USER']))? 'Username or password is incorrect' : '';

        if($CONFIG_APP['login']['LOGIN_VIEW']) {
            include $CONFIG_APP['login']['LOGIN_VIEW'];
        } else {
            include 'tmpl_form.php';
        }

        exit();
    }
  }
  //-----------------------------------------------------------------
}
