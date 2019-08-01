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
    // Authenticate --------------
    $userData = array();

    // Login -----------
    if( (isset($_REQUEST['LOGIN_USER']) && $_REQUEST['LOGIN_USER']) ||
         isset($_REQUEST['auth_token']) )
    {
       // User data
       $userData = array();

       //--------
       if(isset(self::$iLoginQuery)) {
          $ret = self::$iLoginQuery->get($_REQUEST['LOGIN_USER'], $_REQUEST['LOGIN_PASSWD'], $_REQUEST);

          if(is_array($ret)) {
             $userData = $ret;
          } else {
             $userData = Db_mysql::getRow($ret);
          }
       }
       //--------
       else {
          $sqlQ = self::getQuery($_REQUEST['LOGIN_USER'], $_REQUEST['LOGIN_PASSWD']);
          $userData = Db_mysql::getRow($sqlQ);
       }

       // Login ok
       if(isset($userData['id'])) {
           new Login($userData['id'], $userData['email'], $userData);
           header("Location: /?APP_EVENT=timezone"); exit();
       }

    }

    // Authenticate view -------
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
  private static function getQuery($user, $pass)
  {
      global $CONFIG_APP;

      $login_table = $CONFIG_APP['login']['LOGIN_TABLE'];

      $sqlQ = "SELECT id, login, passwd
               FROM $login_table
               WHERE login = '$user' AND
                     passwd = '$pass' AND
                     deleted_at IS NULL";

      return $sqlQ;
  }
  //-----------------------------------------------------------------
}
