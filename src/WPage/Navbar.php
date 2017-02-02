<?
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 */

namespace angelrove\membrillo2\WPage;

use angelrove\membrillo2\utils\CssJs_load;


class Navbar
{
  private static $submenu_sep = '/';
  private static $listSep = array();
  private static $set_inverse = '';

  //---------------------------------------------------
  public static function setInverse($flag)
  {
    self::$set_inverse = ($flag)? 'navbar-inverse' : '';
  }
  //---------------------------------------------------
  public static function get()
  {
    $buttons = self::getButtons();
    $set_inverse = self::$set_inverse;

    echo <<<EOD

<!-- Menú -->
<nav id="WMain_menu" class="navbar navbar-default navbar-static-top $set_inverse">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="/"><i class="fa fa-home"></i></a>
    </div>

    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        $buttons
      </ul>
      <ul class="nav navbar-nav navbar-right">
        <li></li>
      </ul>
    </div>
  </div>
</nav>
<!-- /Menú -->

EOD;
  }
  //---------------------------------------------------
  private static function getButtons()
  {
    global $app, $CONFIG_SECCIONES, $seccCtrl;

    $secciones = $CONFIG_SECCIONES->getList();
    $listItems = $CONFIG_SECCIONES->getListItems();

    $selected  = $seccCtrl->secc;
    $selected_padre = $seccCtrl->secc_padre;

    //----
    $ret = '';
    foreach($listItems as $item => $val) {
       $seccion = $secciones[$item];
       if(!$seccion->title) {
          continue;
       }

       if(isset(self::$listSep[$item])) {
          $ret .= '<li><div class="sep">|</div></li>';
       }
       $ret .= self::getButton($seccion->id, $seccion->title, $selected, $selected_padre);
    }

    return $ret;
  }
  //---------------------------------------------------
  public static function setSep($secc)
  {
    self::$listSep[$secc] = true;
  }
  //---------------------------------------------------
  private static function getButton($sc_id, $title, $modActual, $mod_padre='')
  {
    global $CONFIG_SECCIONES;

    $listSubItems = $CONFIG_SECCIONES->listSubItems;

    $li_active = ($sc_id == $modActual || $sc_id == $mod_padre)? ' active' : '';
    $ret = '';

    // submenu ---
    if(isset($listSubItems[$sc_id])) {
      $ret .= '<li class="dropdown'.$li_active.'">'.
                '<a href="#" id="bt_'.$sc_id.'" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">'.
                  $title.' <b class="caret"></b>'.
                '</a>'.
                '<ul class="dropdown-menu">';
                   foreach($listSubItems[$sc_id] as $id => $title)
                   {
                      if(!$title) {
                         $title = $id;
                      }
                      $mod = $sc_id.'/'.$id;
                      $active = ($mod == $modActual)? 'active' : '';
                      $ret .= '<li class="'.$active.'"><a href="/'.$mod.'">'.$title.'</a></li>';
                   }
      $ret .=   '</ul>';
    }
    // menu ------
    else {
      $ret = '<li class="'.$li_active.'"><a href="/'.$sc_id.'/" id="bt_'.$sc_id.'">'.$title.'</a>';
    }

    $ret .=  '</li>';

    return $ret;
  }
  //---------------------------------------------------
}
