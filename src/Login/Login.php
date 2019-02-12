<?php
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 */

namespace angelrove\membrillo\Login;

use angelrove\membrillo\WApp\Session;

class Login
{
    public static $user_id;
    public static $login;
    public static $timezone;

    public static $INFO = array();

    //------------------------------------------------
    public function __construct($user_id, $login, array $INFO, $timezone)
    {
        self::set_data($user_id, $login, $INFO, $timezone);
    }
    //------------------------------------------------
    public static function set_data($user_id, $login, array $INFO, $timezone)
    {
        self::$user_id = $INFO['id'];
        self::$login   = $INFO['login'];
        self::$INFO    = $INFO;

        self::$timezone = $timezone;

        // session
        Session::set('Login_user_id', Login::$user_id);
        Session::set('Login_login', Login::$login);
        Session::set('Login_INFO', Login::$INFO);
        Session::set('Login_timezone', Login::$timezone);
    }
    //------------------------------------------------
    public static function init()
    {
        self::$user_id = Session::get('Login_user_id');
        self::$login   = Session::get('Login_login');
        self::$INFO    = Session::get('Login_INFO');
        self::$timezone = Session::get('Login_timezone');
    }
    //------------------------------------------------
}
