<?php
namespace angelrove\membrillo2\WObjects\WList;


interface iWListCellOptionsEditor
{
  public function showBtDelete($id, $values);
  public function showBtUpdate($id, $values);
  /*
   * return:
   *     true/false
   *     array('label'=>'[lable]', 'disabled'=>[true/false], 'href'=>'[xxx]');
   */
  public function showBtOp($key, $id, $values);
}
