<?php
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 */

namespace angelrove\membrillo\WApp;

use angelrove\LaraCrud\FormInputs\FormInputs;
use angelrove\membrillo\WApp\Session;
use angelrove\utils\CssJsLoad;

class Local
{
    public static $t = array();

    private static $cookieName = 'cmsLang';
    private static $acceptLang = ['es', 'en'];

    //------------------------------------------------------
    public static function _init()
    {
        // Default ----
        if (!self::getLang()) {
            self::setLang(self::getBrowserLang());
        }

        self::loadLangFiles();
    }
    //------------------------------------------------------
    public static function _init_sections()
    {
        self::loadLangFilesSection();
    }
    //------------------------------------------------------
    private static function loadLangFiles()
    {
        $dir_lang = 'local_t/'.self::getLang().'.inc';

        include_once __DIR__.'/'.$dir_lang;
        include_once PATH_SRC.'/'.$dir_lang;
    }
    //------------------------------------------------------
    private static function loadLangFilesSection()
    {
        global $CONFIG_SECCIONES, $seccCtrl;
        $secc_folder = $CONFIG_SECCIONES->getFolder($seccCtrl->secc);

        $dir_lang = 'local_t/'.self::getLang().'.inc';
        if (file_exists($secc_folder.'/'.$dir_lang)) {
            include_once $secc_folder.'/'.$dir_lang;
        }
    }
    //------------------------------------------------------
    public static function onChangeLang()
    {
        self::setLang($_GET['val']);

        // Reload "CONFIG_SECC"
        Session::unset('CONFIG_SECCIONES');
    }
    //------------------------------------------------------
    public static function setLang($lang)
    {
        setcookie(self::$cookieName, $lang, time()+60*60*24*60, '/');
        $_COOKIE[self::$cookieName] = $lang;
    }
    //------------------------------------------------------
    public static function getLang()
    {
        return $_COOKIE[self::$cookieName]?? false;
    }
    //------------------------------------------------------
    public static function getLangLabel()
    {
        $lang = self::getLang();
        return (!$lang || $lang=='es')? '' : '_'.$lang;
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
        $selectLang = FormInputs::select('local', $lang)->data(['es'=>'Español', 'en'=>'English'])->get();

        return "<style>select[name='local'] { padding: 0px 9px; height: initial !important; }</style>".$selectLang;
    }
    //------------------------------------------------------
    // PRIVATE
    //------------------------------------------------------
    private static function getBrowserLang()
    {
        $lang = '';

        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        } else {
            return 'en';
        }

        return (in_array($lang, self::$acceptLang))? $lang : 'en';
    }
    //------------------------------------------------------
}
