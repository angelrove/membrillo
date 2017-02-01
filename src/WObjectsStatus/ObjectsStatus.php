<?
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 * 2006
 *
 */

namespace angelrove\membrillo2\WObjectsStatus;


class ObjectsStatus
{
  private $listObjects = array();

  //----------------------------------------------------------------------------
  public function __construct()
  {

  }
  //----------------------------------------------------------------------------
  public function initPage()
  {
    global $seccCtrl;

    // Cambio de sección: eliminar objetos no persistentes
    if($seccCtrl->isNewSecc) {
      foreach($this->listObjects as $key => $object)
      {
         if($object->isPersistent() == false) {
            unset($this->listObjects[$key]);
         }
      }
    }

    // Parse events
    Event::updateEvent();
  }
  //----------------------------------------------------------------------------
  public function setNewObject($id_control)
  {
    if (!isset($this->listObjects[$id_control])) {
        $this->listObjects[$id_control] = new ObjectStatus();
    }
    return $this->listObjects[$id_control];
  }
  //----------------------------------------------------------------------------
  //----------------------------------------------------------------------------
  public function getObject($idControl)
  {
    if (isset($this->listObjects[$idControl])) {
       return $this->listObjects[$idControl];
    }
    return false;
  }
  //----------------------------------------------------------------------------
  public function getDatos($idControl)
  {
    if (isset($this->listObjects[$idControl])) {
       return $this->listObjects[$idControl]->getDatos();
    }
    return false;
  }
  //----------------------------------------------------------------------------
  public function getDato($idControl, $name)
  {
    if (isset($this->listObjects[$idControl])) {
       return $this->listObjects[$idControl]->getDato($name);
    }
    return false;
  }
  //----------------------------------------------------------------------------
  // ROW_ID
  //----------------------------------------------------------------------------
  public function getRowId($idControl)
  {
    if (isset($this->listObjects[$idControl])) {
       return $this->listObjects[$idControl]->getRowId();
    }
    return false;
  }
  //----------------------------------------------------------------------------
}
