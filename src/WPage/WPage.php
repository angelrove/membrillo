<?
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 */

namespace angelrove\membrillo2\WPage;

use angelrove\membrillo2\DebugTrace;
use angelrove\membrillo2\Login\Login;
use angelrove\membrillo2\Messages;
use angelrove\membrillo2\WObjectsStatus\Event;
use angelrove\utils\CssJsLoad;

class WPage
{
    public static $title    = false;
    private static $pagekey = '';

    //----------------------------------------------------
    public static function add_pagekey($key)
    {
        self::$pagekey .= $key . ' ';
    }
    //----------------------------------------------------
    //----------------------------------------------------
    public static function get_main()
    {
        global $CONFIG_APP, $seccCtrl;

        // Page title ---
        if (self::$title === false) {
            self::$title = $seccCtrl->title;
        }

        ?><!DOCTYPE html>
      <html lang="es">
      <head>
        <meta charset="utf-8">
        <meta name="author" content="https://github.com/angelrove">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">

        <title><?=$CONFIG_APP['data']['TITLE'] . ' - ' . self::$title?></title>

        <!-- css -->
        <?CssJsLoad::get_css();?>
        <!-- /css -->
        <script>
        var CRUD_DEFAULT = '<?=CRUD_DEFAULT?>';

        var CRUD_EDIT_NEW    = '<?=CRUD_EDIT_NEW?>';
        var CRUD_EDIT_UPDATE = '<?=CRUD_EDIT_UPDATE?>';

        var CRUD_LIST_SEARCH = '<?=CRUD_LIST_SEARCH?>';
        var CRUD_LIST_DETAIL = '<?=CRUD_LIST_DETAIL?>';

        var CRUD_OPER_INSERT = '<?=CRUD_OPER_INSERT?>';
        var CRUD_OPER_UPDATE = '<?=CRUD_OPER_UPDATE?>';
        var CRUD_OPER_DELETE = '<?=CRUD_OPER_DELETE?>';

        var main_secc = "<?=$_GET['secc']?>";
        </script>
      </head>
      <body class="pagekey_<?=$seccCtrl->getKey()?> pagekey_<?=self::$pagekey?>">
        <?self::debug_objects()?>
        <?

    }
    //----------------------------------------------------
    public static function get_main_end()
    {
        ?>
       <!-- js -->
       <?CssJsLoad::get_js();?>
       <!-- /js -->
      </body>
      </html>
      <?
    }
    //----------------------------------------------------
    // CMS
    //----------------------------------------------------
    public static function get()
    {
        self::get_main();
        Navbar::get();

        ?>
      <!-- content -->
      <main class="container-fluid">
        <?self::get_page_header()?>
        <?Messages::show()?>
        <?
    }
    //---------------------------------
    public static function get_end()
    {

        ?>
      </main>
      <!-- /main -->
      <?

        self::get_footer();
        self::get_main_end();
    }
    //----------------------------------------------------
    // Components
    //----------------------------------------------------
    public static function debug_objects()
    {
        if (!DEBUG_VARS) {
            return;
        }

        ?><!-- debug_objects --><?
        global $CONFIG_APP, $CONFIG_DB, $CONFIG_SECCIONES, $seccCtrl, $objectsStatus;
        $const = get_defined_constants(true);

        DebugTrace::out('- CONSTANTS -', $const['user']);
        DebugTrace::out('CONFIG_APP', $CONFIG_APP);
        DebugTrace::out('CONFIG_DB', $CONFIG_DB);
        DebugTrace::out('CONFIG_SECCIONES', $CONFIG_SECCIONES);
        DebugTrace::out('seccCtrl', $seccCtrl);
        DebugTrace::out('objectsStatus', $objectsStatus);
        DebugTrace::out('Event', array('::EVENT' => Event::$EVENT, '::OPER' => Event::$OPER, '::CONTROL' => Event::$CONTROL, '::ROW_ID' => Event::$ROW_ID));
        DebugTrace::out('Login', 'User login: ' . Login::$login);
        ?><!-- /debug_objects --><?
    }
    //----------------------------------------------------
    public static function get_web_header()
    {
        global $CONFIG_APP;

        ?>
      <header class="page-header container-fluid">
        <h1 class="pull-left"><?=$CONFIG_APP['data']['TITLE']?></h1>
      </header>
      <?
    }
    //----------------------------------------------------
    public static function get_page_header()
    {
        if (!self::$title) {
            return;
        }

        ?>
      <div class="page-header"><h2 id="forms"><?=self::$title?></h1></div>
      <?
    }
    //----------------------------------------------------
    public static function get_footer()
    {
        include 'tmpl_page_footer.inc';
    }
    //----------------------------------------------------
}
