<?
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 */

namespace angelrove\membrillo2\WPage;

use angelrove\utils\CssJsLoad;
use angelrove\utils\FileContent;


class Frame
{
  //------------------------------------------------------------------
  public static function get($title='', $showClose=false, $linkClose='')
  {
   if(!$linkClose) {
      $linkClose = \angelrove\membrillo2\CrudUrl::get_nocrud();
   }

      CssJsLoad::set_script('
   $(document).ready(function() {
     //-----------------
     $(".WFrame>.panel-heading>button.close").click(function() {
        window.location = "'.$linkClose.'";
     });
     //-----------------
   });

   $(document).keydown(function(e) {
     // Esc ------------
     var WFrame_showClose = '.($showClose? "true" : "false").';

     if(WFrame_showClose == true && e.keyCode == 27) {
        window.location = "'.$linkClose.'";
     }
     //-----------------
   });

  ', 'WPage\Frame\get');

     $tmpl_params = array('showClose' => $showClose,
                          'title'     => $title);
     return FileContent::include_return(__DIR__.'/tmpl_frame_init.inc', $tmpl_params);
  }
  //----------------------
  public static function get_end()
  {
     return FileContent::include_return(__DIR__.'/tmpl_frame_end.inc');
  }
  //------------------------------------------------------------------
}
