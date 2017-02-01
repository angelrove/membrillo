<?
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 */

namespace angelrove\membrillo2\WApp;


class Session
{
  //------------------------------------------------------
  public static function set($key, $obj)
  {
    $sessionName = self::getNameSession();
    $_SESSION[$sessionName][$key] = $obj;

    return $_SESSION[$sessionName][$key]; // devuelve una referencia
  }
  //------------------------------------------------------
  public static function get($key)
  {
    $sessionName = self::getNameSession();
    if (isset($_SESSION[$sessionName][$key])) {
       return $_SESSION[$sessionName][$key]; // devuelve una referencia
    }
    return false;
  }
  //------------------------------------------------------
  public static function session_destroy()
  {
    global $CONFIG_APP;

    $loginUrl = '';
    if($CONFIG_APP['domain']['login_url']) {
       $loginUrl = $CONFIG_APP['domain']['login_url'];
    } else {
       $loginUrl = '/';
    }

    // Eliminar sesión
    session_unset();
    session_destroy();

    // Redirect (login)
    if(isset($_GET['LOGIN_USER'])) header("Location: $loginUrl?LOGIN_USER=$_GET[LOGIN_USER]&LOGIN_PASSWD=$_GET[LOGIN_PASSWD]");
    else                           header("Location: $loginUrl");
    exit();
  }
  //------------------------------------------------------
  // Private
  //------------------------------------------------------
  private static function getNameSession() {
    global $CONFIG_DB;

    return $CONFIG_DB['default']['DBNAME'];
  }
  //------------------------------------------------------
}
?>
