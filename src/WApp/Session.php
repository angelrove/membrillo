<?php
/**
 *
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 */

namespace angelrove\membrillo\WApp;

class Session
{
    static private $sessionId;

    //------------------------------------------------------
    public static function start($sessionId, int $expireHours)
    {
        self::$sessionId = $sessionId;
        $expireSeconds = $expireHours*60*60;

        // Session lifetime ---
        ini_set('session.gc_maxlifetime', $expireSeconds);
        ini_set("session.cookie_lifetime", $expireSeconds);
        session_set_cookie_params($expireSeconds);

        // Session folder ---
        session_save_path(PATH_MAIN.'/_session');

        // start ---
        session_start();
        // self::sessionExpireAt($expireAfter);
    }
    //------------------------------------------------------
    public static function set(string $key, $obj)
    {
        $sessionName = self::getSessionId();
        $_SESSION[$sessionName][$key] = $obj;

        return $_SESSION[$sessionName][$key]; // devuelve una referencia
    }
    //------------------------------------------------------
    public static function unset(string $key)
    {
        $sessionName = self::getSessionId();
        if (isset($_SESSION[$sessionName][$key])) {
            unset($_SESSION[$sessionName][$key]);
        }
    }
    //------------------------------------------------------
    public static function get(string $key)
    {
        $sessionName = self::getSessionId();

        // devuelve una referencia
        return ($_SESSION[$sessionName][$key])?? false;
    }
    //------------------------------------------------------
    public static function session_destroy()
    {
        session_unset();
        session_destroy();
    }
    //------------------------------------------------------
    /**
     * Expire the session if user is inactive for $expireAfter min.
     */
    public static function sessionExpireAt(int $expireAfter)
    {
        // Check to see if our "last action" session variable has been set.
        if (isset($_SESSION['last_action'])) {
            // Figure out how many seconds have passed since the user was last active.
            $secondsInactive = time() - $_SESSION['last_action'];

            // Convert our minutes into seconds.
            $expireAfterSeconds = $expireAfter * 60;

            // Check to see if they have been inactive for too long.
            if ($secondsInactive >= $expireAfterSeconds) {
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
    private static function getSessionId()
    {
        return self::$sessionId;
    }
    //------------------------------------------------------
}
