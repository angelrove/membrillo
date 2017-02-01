<?
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 */

namespace angelrove\membrillo\WInputs\WInputHtml2;

use angelrove\utils\CssJsLoad;


class WInputHtml2
{
  //----------------------------------------------------
  public function __construct()
  {
    CssJsLoad::set_js ('//cdn.tinymce.com/4/tinymce.min.js');
  }
  //-----------------------------------------------------------
  public function get($name, $value, $height='200')
  {
    $selector = 'WInputHtml2_'.$name;
    $value = stripslashes(str_replace('\r\n' , "\n" , $value));

    ?>
    <!-- WInputHtml -->
    <script>
    tinymce.init({

      mode : "specific_textareas",
      editor_selector : "<?=$selector?>",
      height: <?=$height?>,
      code_dialog_width: 900,

      force_br_newlines : false,
      force_p_newlines  : true,
      forced_root_block : '',

      plugins: [
        "hr advlist autolink lists link textcolor colorpicker image charmap print preview anchor",
        "searchreplace visualblocks code fullscreen",
        "insertdatetime media table contextmenu paste"
      ],
      toolbar: "code | insertfile undo redo | forecolor | fontsizeselect | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"

    });
    </script>
    <style>
    textarea.mce-textbox {
      font-family: "Courier New";
      background-color: #333;
      color: #eee;
    }
    </style>

    <!-- <form method="post" action="somepage"> -->
    <textarea name="<?=$name?>" class="<?=$selector?>"><?=html_entity_decode($value)?></textarea>
    <!-- </form> -->
    <?

  }
  //-----------------------------------------------------------
}
