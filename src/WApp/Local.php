<?php
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 */

namespace angelrove\membrillo2\WApp;

class Local
{
    public static $t = array();

    //------------------------------------------------------
    public static function _init()
    {
        //----
        $lang = '';
        if (!self::getLang()) {
            $lang = self::getBrowserLang();
            self::setLang($lang);
        }
        else {
            $lang = self::getLang();
        }

        //----
        include_once 'local_t/'.$lang.'.inc';
        include_once 'app/local_t/'.$lang.'.inc';
    }
    //------------------------------------------------------
    public static function setLang($lang)
    {
        setcookie("userLang", $lang, time()+60*60*24*60);
    }
    //------------------------------------------------------
    public static function getLang()
    {
        return $_COOKIE["userLang"] ?? false;
    }
    //------------------------------------------------------
    public static function getBrowserLang()
    {
        $acceptLang = ['es', 'en'];

        $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        $lang = in_array($lang, $acceptLang) ? $lang : 'en';

        return $lang;
    }
    //------------------------------------------------------
}
