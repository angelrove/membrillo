<?
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 */

namespace angelrove\membrillo\Login;

use angelrove\membrillo\WApp\Session;


class Login
{
  static public $user_id;
  static public $login;

  static public $INFO = array();

  //------------------------------------------------
  public function __construct($user_id, $login, array $INFO)
  {
    self::$user_id = $INFO['id'];
    self::$login   = $INFO['login'];
    self::$INFO    = $INFO;

    // session
    Session::set('Login_user_id', Login::$user_id);
    Session::set('Login_login',   Login::$login);
    Session::set('Login_INFO',    Login::$INFO);
  }
  //------------------------------------------------
  public static function init()
  {
    self::$user_id = Session::get('Login_user_id');
    self::$login   = Session::get('Login_login');
    self::$INFO    = Session::get('Login_INFO');
  }
  //------------------------------------------------
}
