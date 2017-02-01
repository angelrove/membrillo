<?
namespace angelrove\membrillo2\WObjects\WList;


interface iWListCellEditor
{
  public function getValueAt($id, $columnName, $value, $values);
  public function getBgColorAt($id, $columnName, $value, $values);
  public function getOnClick($id, $columnName, $value, $values);
}
