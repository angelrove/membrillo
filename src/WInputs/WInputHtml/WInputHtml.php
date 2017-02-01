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
    CssJsLoad::set_js('//cdn.tinymce.com/4/tinymce.min.js');
    CssJsLoad::set(__DIR__.'/lib.js');
  }
  //-----------------------------------------------------------
  public function get($name, $value, $height='200')
  {
    $selector = 'WInputHtml2_'.$name;
    $value = stripslashes(str_replace('\r\n' , "\n" , $value));

    //----------
    CssJsLoad::set_script('
  // var WInputHtml_selector = "'.$selector.'";
  // var WInputHtml_height   = "'.$height.'";

  tinymce.init({

    mode : "specific_textareas",
    editor_selector : "'.$selector.'",
    height: '.$height.',
    code_dialog_width: 900,

    force_br_newlines : false,
    force_p_newlines  : true,
    forced_root_block : "",

    plugins: [
      "hr advlist autolink lists link textcolor colorpicker image charmap print preview anchor",
      "searchreplace visualblocks code fullscreen",
      "insertdatetime media table contextmenu paste"
    ],
    toolbar: "code | insertfile undo redo | forecolor | fontsizeselect | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"

  });
');

    //----------
    $params = array('name'    => $name,
                    'selector'=> $selector,
                    'value'   => $value);

    return FileContent::include_return(__DIR__.'/tmpl.inc', $params);
  }
  //-----------------------------------------------------------
}
