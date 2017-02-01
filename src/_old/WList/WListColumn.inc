<?
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 * 2006
 *
 */

namespace membrillo\components;

class WListColumn {
  public $name;
  public $title;
  public $width;
  public $align;
  public $order;
  public $onClick;

  //-------------------------------------------------------
  function __construct($name, $title, $width, $align='') {
    $this->name  = $name;
    $this->title = $title;
    $this->size  = $width;
    $this->align = $align;
  }
  //-------------------------------------------------------
  public function setWidth($width) {
    $this->size = $width;
  }
  //-------------------------------------------------------
  public function setOrder($field='') {
    $this->order = (!$field)? $this->name : $field;
  }
  //-------------------------------------------------------
  public function setOnClick() {
    $this->onClick = $this->name;
  }
  //-------------------------------------------------------
}
?>
