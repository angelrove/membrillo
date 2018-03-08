<?php
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
    $sessionName = self::getSessionName();
    $_SESSION[$sessionName][$key] = $obj;

    return $_SESSION[$sessionName][$key]; // devuelve una referencia
  }
  //------------------------------------------------------
  public static function get($key)
  {
    $sessionName = self::getSessionName();
    if(isset($_SESSION[$sessionName][$key])) {
       return $_SESSION[$sessionName][$key]; // devuelve una referencia
    }

    return false;
  }
  //------------------------------------------------------
  public static function session_destroy()
  {
    global $CONFIG_APP;

    $loginUrl = '';
    if($CONFIG_APP['login']['LOGIN_URL']) {
       $loginUrl = $CONFIG_APP['login']['LOGIN_URL'];
    }
    else {
       $loginUrl = '/';
    }

    // Eliminar sesión
    session_unset();
    session_destroy();

    // Redirect (login)
    if(isset($_GET['LOGIN_USER'])) {
       header("Location: $loginUrl?LOGIN_USER=$_GET[LOGIN_USER]&LOGIN_PASSWD=$_GET[LOGIN_PASSWD]");
    }
    else {
      header("Location: $loginUrl");
    }

    exit();
  }
  //------------------------------------------------------
  /**
   * Expire the session if user is inactive for $expireAfter min.
   */
  public function sessionExpireAt($expireAfter)
  {
      // Check to see if our "last action" session variable has been set.
      if(isset($_SESSION['last_action'])) {

          // Figure out how many seconds have passed since the user was last active.
          $secondsInactive = time() - $_SESSION['last_action'];

          // Convert our minutes into seconds.
          $expireAfterSeconds = $expireAfter * 60;

          // Check to see if they have been inactive for too long.
          if($secondsInactive >= $expireAfterSeconds){
              session_unset();
              session_destroy();
          }

      }

      // Assign the current timestamp as the user's latest activity
      $_SESSION['last_action'] = time();
  }
  //------------------------------------------------------
  // Private
  //------------------------------------------------------
  private static function getSessionName()
  {
    global $CONFIG_DB;

    return $CONFIG_DB['default']['DBNAME'];
  }
  //------------------------------------------------------
}
