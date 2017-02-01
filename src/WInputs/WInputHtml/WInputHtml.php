<?
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 */

namespace angelrove\membrillo2\WInputs\WInputHtml;

use angelrove\utils\CssJsLoad;
use angelrove\utils\FileContent;


class WInputHtml
{
  //----------------------------------------------------
  public function __construct()
  {
     CssJsLoad::set_js('https://cdnjs.cloudflare.com/ajax/libs/tinymce/4.5.3/tinymce.min.js');
     CssJsLoad::set(__DIR__.'/lib.js');
  }
  //-----------------------------------------------------------
  public function get($name, $value, $height='200')
  {
     $selector = 'WInputHtml2_'.$name;
     $value = stripslashes(str_replace('\r\n' , "\n" , $value));

     //----------
     $params = array('name'    => $name,
                     'selector'=> $selector,
                     'height'  => $height,
                     'value'   => $value);

     return FileContent::include_return(__DIR__.'/tmpl.inc', $params);
  }
  //-----------------------------------------------------------
}
