<?php
/**
 *
 * José A. Romero Vegas, 2006
 * jangel.romero@gmail.com
 */

use angelrove\utils\CssJsLoad;

CssJsLoad::set(__DIR__.'/styles.css');


//------------------------------------------------------------------
function WFormRow($control, $isUpdate=false, $title, $listFields)
{
  global $seccCtrl;

  $event = 'form_insert';
  $row_id = '';
  if($isUpdate === true) {
    $event = 'form_update';
    $row_id = $objectsStatus->getRowId($control);
  }

  $strFields = '';
  foreach($listFields as $field) {
    $strFields .= '<td class="row_edit_title">'. $field .'</td>';
  }

  echo <<<EOD
  <form name="form_edit" method="POST" action="/<?=$_GET['secc']?>/crd/$event/">
  <table align="center" class="row_edit" cellpadding="0" cellspacing="0">
   <input type="hidden" name="ROW_ID"  value="$row_id">

    <tr><td class="row_edit_title" colspan="15">$title</td></tr>
    <tr>$strFields</tr>
EOD;
}

function WFormRow_END($submitButtons=true) {
  if(!$submitButtons) $strDisabled = 'disabled';

  echo <<<EOD
   <!-- Botones -->
   <tr><td align="right" colspan="10" style="padding:4px">
    <input $strDisabled class="bt_form" type="submit" value="Aceptar">
    <input $strDisabled class="bt_form" type="button" value="Guardar" onclick="form_edit.EVENT.value += 'Cont';form_edit.submit()">
   </td></tr>
   <!-- FIN Botones -->

  </table>
  </form>
EOD;

}
//------------------------------------------------------------------
