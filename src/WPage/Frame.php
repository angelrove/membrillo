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
      $linkClose = './';
   }

      CssJsLoad::set_script('
   var WFrame_showClose = '.($showClose? "true" : "false").';

   $(document).ready(function() {
     //-----------------
     $(".WFrame .close").click(function() {
       Frame_close();
     });
     //-----------------
   });
   $(document).keydown(function(e) {
     // Esc ------------
     if(WFrame_showClose == true && e.keyCode == 27) {
        Frame_close();
     }
     //-----------------
   });

   //-------------------
   function Frame_close() {
      window.location = "'.$linkClose.'";
   }
   //-------------------

  ', 'WFrame');

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
