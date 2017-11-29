<?php
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 */

namespace angelrove\membrillo2\WPage;

use angelrove\membrillo2\Login\Login;

class Navbar
{
    private static $submenu_sep = '/';
    private static $listSep     = array();
    private static $set_inverse = '';

    //---------------------------------------------------
    public static function get()
    {
        global $CONFIG_APP;

        $buttons     = self::getButtons();
        $set_inverse = self::$set_inverse;

        // Right items ---
        $str_close = '';
        if (Login::$login) {
            $str_close = '<a href="/?APP_EVENT=close" title="Exit">'.Login::$login.' <i class="fa fa-sign-out fa-lg"></i></a>';
        }

        echo self::tmpl_navbar($set_inverse, $CONFIG_APP['data']['TITLE'], $buttons, $str_close);
    }
    //---------------------------------------------------
    private static function getButtons()
    {
        global $app, $CONFIG_SECCIONES, $seccCtrl;

        $secciones = $CONFIG_SECCIONES->getList();
        $listItems = $CONFIG_SECCIONES->getListItems();

        $selected       = $seccCtrl->secc;
        $selected_padre = $seccCtrl->secc_padre;

        //----
        $ret = '';
        foreach ($listItems as $item => $val) {
            $seccion = $secciones[$item];

            if (!$seccion->title) {
                continue;
            }

            if (isset(self::$listSep[$item])) {
                $ret .= '<li><div class="sep">|</div></li>';
            }
            $ret .= self::getButton($seccion->id, $seccion->title, $seccion->link, $selected, $selected_padre);
        }

        return $ret;
    }
    //---------------------------------------------------
    public static function setSep($secc)
    {
        self::$listSep[$secc] = true;
    }
    //---------------------------------------------------
    private static function getButton($sc_id, $title, $link, $modActual, $mod_padre = '')
    {
        global $CONFIG_SECCIONES;

        $listSubItems = $CONFIG_SECCIONES->listSubItems;

        $li_active = ($sc_id == $modActual || $sc_id == $mod_padre) ? ' active' : '';
        $ret       = '';

        // submenu ---
        if (isset($listSubItems[$sc_id])) {
            $ret .=
                '<li class="dropdown' . $li_active . '">' .
                '<a href="#" id="bt_' . $sc_id . '"
                    class="dropdown-toggle"
                    data-toggle="dropdown"
                    role="button"
                    aria-haspopup="true"
                    aria-expanded="false">' . $title . ' <b class="caret"></b>' .
                '</a>' .
                '<ul class="dropdown-menu">';

            foreach ($listSubItems[$sc_id] as $id => $title) {
                $mod = $sc_id . '/' . $id;

                if (!$title) {
                    $title = $id;
                }

                //-----
                if ($link = $CONFIG_SECCIONES->getSection_link($mod)) {
                    $ret .= '<li><a href="' . $link . '" target="_blank">' . $title . '</a></li>';
                } else {
                    $href   = '/' . $mod;
                    $active = ($mod == $modActual) ? 'active' : '';
                    $ret .= '<li class="' . $active . '"><a href="' . $href . '">' . $title . '</a></li>';
                }
            }

            $ret .= '</ul>';
        }
        // menu ------
        else {
            $href = ($link) ? $link : '/' . $sc_id . '/';
            $ret  = self::tmpl_item($li_active, $href, $sc_id, $title);
        }

        $ret .= '</li>';

        return $ret;
    }
    //---------------------------------------------------
    // Templates
    //---------------------------------------------------
    // Bootstrap 3
    //---------------------------------------------------
    public static function setInverse($flag)
    {
        self::$set_inverse = ($flag) ? 'navbar-inverse' : '';
    }
    //---------------------------------------------------
    private static function tmpl_item($li_active, $href, $sc_id, $title) {
        return '<li class="'.$li_active.'"><a href="'.$href.'" id="bt_'.$sc_id.'">'.$title.'</a>';
    }
    //---------------------------------------------------
    private static function tmpl_navbar($set_inverse, $title, $buttons, $str_close) {
      return '

      <!-- Navbar -->
      <nav id="WMain_menu" class="navbar navbar-default navbar-static-top ' . $set_inverse . '">
          <div class="container-fluid">
              <div class="navbar-header">
                  <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                  </button>
                  <a class="navbar-brand" href="/"><span class="hidden-sm">' . $title . '</span></a>
              </div>

              <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                  <ul class="nav navbar-nav">
                    ' . $buttons . '
                  </ul>
                  <ul class="nav navbar-nav navbar-right">
                    <li>' . $str_close . '</li>
                  </ul>
              </div>
          </div>
      </nav>
      <!-- /Navbar -->

      ';
    }
    //---------------------------------------------------
    // Bootstrap 4
    //---------------------------------------------------
    // public static function setInverse($flag)
    // {
    //     self::$set_inverse = ($flag) ? 'navbar-dark bg-dark' : '';
    // }
    // //---------------------------------------------------
    // private static function tmpl_item($li_active, $href, $sc_id, $title)
    // {
    //     return '<li class="nav-item ' . $li_active . '"><a class="nav-link" href="' . $href . '" id="bt_' . $sc_id . '">' . $title . '</a>';
    // }
    // //---------------------------------------------------
    // private static function tmpl_navbar($set_inverse, $title, $buttons, $str_close)
    // {
    //     return '

    //   <!-- Navbar -->
    //   <nav id="WMain_menu" class="navbar navbar-expand-lg navbar-static-top ' . $set_inverse . '">
    //       <a class="navbar-brand" href="/"><span class="hidden-sm">' . $title . '</span></a>

    //       <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    //         <span class="navbar-toggler-icon"></span>
    //       </button>

    //       <div class="collapse navbar-collapse" id="navbarNav">
    //           <ul class="navbar-nav mr-auto">
    //             ' . $buttons . '
    //           </ul>
    //           <span class="navbar-text">
    //              <ul class="navbar-nav">
    //                <li class="nav-item">' . $str_close . '</li>
    //              </ul>
    //           </span>
    //       </div>
    //   </nav>
    //   <!-- /Navbar -->

    //   ';
    // }
    //---------------------------------------------------
}
