<?
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 */

namespace angelrove\membrillo2\WObjectsStatus;

use angelrove\membrillo2\Messages;
use angelrove\membrillo2\CrudUrl;


class ObjectsStatus
{
  private $listObjects = array();

  //----------------------------------------------------------------------------
  public function __construct()
  {
  }
  //----------------------------------------------------------------------------
  public function setNewObject($idControl, $path='')
  {
    if(!isset($this->listObjects[$idControl])) {
        $this->listObjects[$idControl] = new ObjectStatus($idControl, $path);
    }
    return $this->listObjects[$idControl];
  }
  //----------------------------------------------------------------------------
  // public function setNewObject2($idControl, $component)
  // {
  //   if(!isset($this->listObjects[$idControl])) {
  //       $this->listObjects[$idControl] = $component;
  //   }
  //   return $this->listObjects[$idControl];
  // }
  //----------------------------------------------------------------------------
  public function getObject($idControl)
  {
    if(isset($this->listObjects[$idControl])) {
       return $this->listObjects[$idControl];
    }
    return false;
  }
  //----------------------------------------------------------------------------
  //----------------------------------------------------------------------------
  public function initPage()
  {
    global $seccCtrl;

    // If a new secc: delete data from non-persistent objets
    if($seccCtrl->isNewSecc) {
       self::clearObjects();
    }
  }
  //----------------------------------------------------------------------------
  public function clearObjects()
  {
    foreach($this->listObjects as $key => $object) {
       if($object->isPersistent() == false) {
          unset($this->listObjects[$key]);
       }
    }
  }
  //----------------------------------------------------------------------------
  public function getPath($idControl)
  {
    if(isset($this->listObjects[$idControl])) {
       return $this->listObjects[$idControl]->getPath();
    }
    return false;
  }
  //----------------------------------------------------------------------------
  public function parseEvent($path_secc)
  {
      // onInitPage
      include($path_secc.'/onInitPage.inc');

      // Default view
      if(!Event::$EVENT) {
         include($path_secc.'/tmpl_main.inc');
         return;
      }

      $wObjectStatus = $this->setNewObject(Event::$CONTROL); // if no exist
      $wObjectStatus->updateDatos();

      // oper ------
      if(Event::$OPER) {
        Messages::set_empty();
        $wObjectStatus->parse_oper(Event::$OPER, Event::$ROW_ID);

        // redirect
        if(!error_get_last() && Event::$REDIRECT_AFTER_OPER) {
           Messages::set_debug('>> Redirected ---');
           header('Location:'.CrudUrl::get(Event::$EVENT, Event::$CONTROL, Event::$ROW_ID, '', 'OPERED='.Event::$OPER));
           exit();
        }
      }

      // flow (view) ------
      if(Event::$EVENT) {
         $wObjectStatus->parse_event(Event::$EVENT);
      }
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
