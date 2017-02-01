<?
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 */

namespace angelrove\membrillo2\WPage;

use angelrove\utils\CssJsLoad;


class Frame
{
  //------------------------------------------------------------------
  public static function start($title='', $width='', $linkClose='', $showClose=false)
  {
   if(!$linkClose) {
      $linkClose = './';
   }

   if($width) {
      $width = 'width:'.$width.'px';
   }

      CssJsLoad::set_script('
   var WFrame_showClose = '.($showClose? "true" : "false").';

   $(document).ready(function() {
     //-----------------
     // Ocultar "Cerrar" solo si no existe un botón "Cerrar" del form no muestra el X de cerrar del frame
     if(WFrame_showClose == false) {
        if($("#WForm_btClose").length == 0) {
           $(".WFrame .close").hide();
        }
     }
     //-----------------
     $(".WFrame .close").click(function() {
       Frame_close();
     });
     //-----------------
   });

   // Esc --------------
   $(document).keydown(function(e) {
     if(WFrame_showClose == true && e.keyCode == 27) {
        Frame_close();
     }
   });

   //-------------------
   function Frame_close() {
      window.location = "'.$linkClose.'";
   }
   //-------------------

  ', 'WFrame');
   ?>

   <!-- WFrame -->
  <div class="WFrame panel panel-default" style="<?=$width?>">
     <?if($title) {?>
       <div class="panel-heading">
         <button class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
         <div class="panel-title"><?=$title?></div>
       </div>
     <?}?>

     <!-- WFrame content -->
     <div class="panel-body">
     <?

  }
  //----------------------
  public static function end() {
     ?>

     </div>
     <!-- /WFrame content -->
   </div>
   <!-- /WFrame -->
   <?
  }
  //------------------------------------------------------------------
}
