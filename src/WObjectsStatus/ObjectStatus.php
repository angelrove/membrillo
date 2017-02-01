<?
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 * 2006
 *
 */

namespace angelrove\membrillo\WObjectsStatus;


class ObjectStatus
{
  private $persistent = false;
  private $ROW_ID = '';
  private $datos = array();

  //----------------------------------------------------------------------------
  public function __construct()
  {

  }
  //----------------------------------------------------------------------------
  public function updateDatos()
  {
    foreach($_REQUEST as $name => $value) {
      if($name == 'secc' || $name == 'PHPSESSID' || $name == '__utma' ||
         $name == 'CONTROL' || $name == 'EVENT' || $name == 'OPER') {
         continue;
      }

      if ($name == 'ROW_ID') {
         $this->ROW_ID = $value;
      }
      else {
         $this->datos[$name] = $value;
      }
    }
  }
  //----------------------------------------------------------------------------
  //----------------------------------------------------------------------------
  public function setPersistent($flag=true) {
    return $this->persistent = $flag;
  }
  //----------------------------------------------------------------------------
  public function isPersistent() {
    return $this->persistent;
  }
  //----------------------------------------------------------------------------
  //----------------------------------------------------------------------------
  public function setDato($name, $value)
  {
    $this->datos[$name] = $value;
  }
  //----------------------------------------------------------------------------
  public function getDato($name)
  {
    if (isset($this->datos[$name])) {
       return $this->datos[$name];
    }
    return false;
  }
  //----------------------------------------------------------------------------
  public function getDatos()
  {
    return $this->datos;
  }
  //----------------------------------------------------------------------------
  public function delDato($name)
  {
    $this->datos[$name] = '';
  }
  //----------------------------------------------------------------------------
  // ROW_ID
  //----------------------------------------------------------------------------
  public function setRowId($value)
  {
    $this->ROW_ID = $value;
  }
  //----------------------------------------------------------------------------
  public function getRowId()
  {
    return $this->ROW_ID;
  }
  //----------------------------------------------------------------------------
  public function delRowId()
  {
    $this->ROW_ID = '';
  }
  //----------------------------------------------------------------------------
}
