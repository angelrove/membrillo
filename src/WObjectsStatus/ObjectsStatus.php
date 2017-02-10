<?
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
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

    // If a new secc: delete data from non-persistent objets
    if($seccCtrl->isNewSecc) {
      foreach($this->listObjects as $key => $object) {
         if($object->isPersistent() == false) {
            unset($this->listObjects[$key]);
         }
      }
    }
  }
  //----------------------------------------------------------------------------
  //----------------------------------------------------------------------------
  public function setNewObject($idControl)
  {
    if(!isset($this->listObjects[$idControl])) {
        $this->listObjects[$idControl] = new ObjectStatus();
    }
    return $this->listObjects[$idControl];
  }
  //----------------------------------------------------------------------------
  public function getObject($idControl)
  {
    if (isset($this->listObjects[$idControl])) {
       return $this->listObjects[$idControl];
    }
    return false;
  }
  //----------------------------------------------------------------------------
  //----------------------------------------------------------------------------
  public function setDato($idControl, $name, $value)
  {
    if(!isset($this->listObjects[$idControl])) {
       $this->setNewObject($idControl); // Crea el nuevo objeto
    }

    return $this->listObjects[$idControl]->setDato($name, $value);
  }
  //----------------------------------------------------------------------------
  public function getDatos($idControl)
  {
    if(isset($this->listObjects[$idControl])) {
       return $this->listObjects[$idControl]->getDatos();
    }
    return false;
  }
  //----------------------------------------------------------------------------
  public function getDato($idControl, $name)
  {
    if(isset($this->listObjects[$idControl])) {
       return $this->listObjects[$idControl]->getDato($name);
    }
    return false;
  }
  //----------------------------------------------------------------------------
  // ROW_ID
  //----------------------------------------------------------------------------
  public function getRowId($idControl)
  {
    if(isset($this->listObjects[$idControl])) {
       return $this->listObjects[$idControl]->getRowId();
    }
    return false;
  }
  //----------------------------------------------------------------------------
}
