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

    // Authenticate --------------
    $userData = array();

    if(isset($_REQUEST['LOGIN_USER']) && $_REQUEST['LOGIN_USER'])
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
                   WHERE login='$_REQUEST[LOGIN_USER]' AND passwd='$_REQUEST[LOGIN_PASSWD]'";
       }

       if(is_array($sqlQ)) {
          $userData = $sqlQ;
       } else {
          $userData = Db_mysql::getRow($sqlQ);
       }

       // Timezone
       $timezone_name = timezone_name_from_abbr("", $_REQUEST['timezone_offset']*60, false);

       // Login
       if(isset($userData['id'])) {
           new Login($userData['id'], $userData['login'], $userData, $timezone_name);
       }
    }

    // Authenticate form view -------
    if(!Login::$user_id)
    {
       global $CONFIG_APP;

       if($CONFIG_APP['login']['LOGIN_URL']) {
          header("Location:".$CONFIG_APP['login']['LOGIN_URL']."?msg=ko");
       }
       else {
          $template = 'tmpl_form.php';
          if($CONFIG_APP['login']['LOGIN_VIEW']) {
              $template = $CONFIG_APP['login']['LOGIN_VIEW'];
          }

          $msg = (isset($_REQUEST['LOGIN_USER']))? 'Username or password is incorrect' : '';
          include($template);
       }

       exit();
    }
  }
  //-----------------------------------------------------------------
}
