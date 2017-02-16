<?
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 */

namespace angelrove\membrillo2\WObjectsStatus;


class ObjectStatus
{
  private $id = '';
  private $persistent = false;
  private $path = '';

  private $ROW_ID = '';
  private $datos  = array();

  //----------------------------------------------------------------------------
  public function __construct($id)
  {
     global $path_secc;

     $this->id   = $id;
     $this->path = $path_secc.'/ctrl_'.$id;
  }
  //----------------------------------------------------------------------------
  public function updateDatos()
  {
    foreach($_REQUEST as $name => $value) {
      if($name == 'secc' || $name == 'PHPSESSID' || $name == '__utma' ||
         $name == 'CONTROL' || $name == 'EVENT' || $name == 'OPER') {
         continue;
      }

      if($name == 'ROW_ID') {
         $this->ROW_ID = $value;
      }
      else {
         $this->datos[$name] = $value;
      }
    }
  }
  //----------------------------------------------------------------------------
  public function parse_oper($oper, $row_id)
  {
    global $objectsStatus;
    include($this->path.'/oper.inc');
  }
  //----------------------------------------------------------------------------
  public function parse_event($event)
  {
    global $objectsStatus;
    include($this->path.'/flow.inc');
  }
  //----------------------------------------------------------------------------
  //----------------------------------------------------------------------------
  public function setPath($path)
  {
    return $this->path = $path;
  }
  //----------------------------------------------------------------------------
  public function getPath()
  {
    return $this->path;
  }
  //----------------------------------------------------------------------------
  public function setPersistent($flag=true)
  {
    return $this->persistent = $flag;
  }
  //----------------------------------------------------------------------------
  public function isPersistent()
  {
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
    if(isset($this->datos[$name])) {
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
