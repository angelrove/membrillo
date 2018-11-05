<?php
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 */

namespace angelrove\membrillo2\WApp;
use angelrove\membrillo2\WApp\Session;
use angelrove\membrillo2\WInputs\WInputSelect;
use angelrove\utils\CssJsLoad;

class Local
{
    public static $t = array();
    private static $acceptLang = ['es', 'en'];

    //------------------------------------------------------
    public static function _init()
    {
        // Default ----
        if (!self::getLang()) {
            self::setLang(self::getBrowserLang());
        }

        //----
        self::loadLangFiles();
    }
    //------------------------------------------------------
    private static function loadLangFiles()
    {
        $lang = self::getLang();

        include_once 'local_t/'.$lang.'.inc';
        include_once 'app/local_t/'.$lang.'.inc';
    }
    //------------------------------------------------------
    public static function onChangeLang()
    {
        self::setLang($_GET['val']);

        // Load lang files ----
        self::loadLangFiles();

        // Reload "CONFIG_SECC"
        require DOCUMENT_ROOT . '/app/CONFIG_SECC.inc';
    }
    //------------------------------------------------------
    public static function setLang($lang)
    {
        setcookie("userLang", $lang, time()+60*60*24*60);
        $_COOKIE["userLang"] = $lang;
    }
    //------------------------------------------------------
    public static function getLang()
    {
        return $_COOKIE["userLang"];
    }
    //------------------------------------------------------
    public static function getSelector()
    {
        $lang = self::getLang();
        $lang_code = $lang.'-'.strtoupper($lang);

        CssJsLoad::set_script(
<<<EOD
  var Local_lang_code1 = '$lang';
  var Local_lang_code2 = '$lang_code';

  $(document).ready(function() {
    $("select[name='local']").change(function() {
        location.href = './?APP_EVENT=local&val='+$(this).val();
    });
  });
EOD
);
        return
        "<style>select[name='local'] { width:initial; display:initial; }</style>".
        WInputSelect::getFromArray(
                            ['es'=>'Español', 'en'=>'English'],
                            $lang,
                            'local'
                        );
    }
    //------------------------------------------------------
    // PRIVATE
    //------------------------------------------------------
    private static function getBrowserLang()
    {
        $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        $lang = in_array($lang, self::$acceptLang) ? $lang : 'en';

        return $lang;
    }
    //------------------------------------------------------
}
