<?
/**
 *
 * José A. Romero Vegas, 2006
 * jangel.romero@gmail.com
 */

function WFrameAlert($msg='', $onclick='history.back()') {
?>

<?WMain()?>

 <div style="height:80px">&nbsp;</div>

 <?WFrame('Aviso')?>
   <?=$msg?>
   <div style="text-align:center; padding-top:7px">
    <input type="button" value="Cerrar" onClick="<?=$onclick?>">
   </div>
 <?WFrame_END()?>

 <div style="height:80px">&nbsp;</div>

<?WMain_END();exit();?>

<?
}
?>
