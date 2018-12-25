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
    }
    else {
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

    // Login --------------
    if(isset($_REQUEST['LOGIN_USER']) && $_REQUEST['LOGIN_USER'])
    {
       // Query
       $sqlQ = '';

       if(isset(self::$iLoginQuery)) {
          $sqlQ = self::$iLoginQuery->get($_REQUEST['LOGIN_USER'], $_REQUEST['LOGIN_PASSWD']);
       }

       if(!$sqlQ) {
          $login_table = $CONFIG_APP['login']['LOGIN_TABLE'];
          $sqlQ = "SELECT * FROM $login_table WHERE login='$_REQUEST[LOGIN_USER]' AND passwd='$_REQUEST[LOGIN_PASSWD]'";
       }

       $datos = Db_mysql::getRow($sqlQ);

       // Login
       if($datos) {
          new Login($datos['id'], $datos['login'], $datos);
       }
    }

    // Authenticate form -------
    if(!Login::$user_id)
    {
       global $CONFIG_APP;

       if($CONFIG_APP['login']['LOGIN_URL']) {
          header("Location:".$CONFIG_APP['login']['LOGIN_URL']."?msg=ko");
       }
       else {
          $template = 'tmpl_form.php';
          $msg = (isset($_REQUEST['LOGIN_USER']))? 'Usuario no válido.' : '';
          include($template);
       }

       exit();
    }
  }
  //-----------------------------------------------------------------
}
