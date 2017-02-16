<?
/**
 *  @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 */

namespace angelrove\membrillo2\WObjectsStatus;

use angelrove\membrillo2\WApp\Session;
use angelrove\membrillo2\Messages;


class Event
{
  public  static $CONTROL;
  public  static $EVENT;
  private static $EVENT_PREV;
  public  static $OPER;
  public  static $ROW_ID;

  public static $REDIRECT_AFTER_OPER = true;

  //----------------------------------------------------------------------------
  public static function initPage()
  {
    // No hay evento ---
    if(!isset($_REQUEST['CONTROL']) || !isset($_REQUEST['EVENT'])) {
       return;
    }

    // Event ----
    self::setEvent($_REQUEST['EVENT']);

    self::$CONTROL = $_REQUEST['CONTROL'];
    self::$OPER    = (isset($_REQUEST['OPER']))?   $_REQUEST['OPER']   : '';
    self::$ROW_ID  = (isset($_REQUEST['ROW_ID']))? $_REQUEST['ROW_ID'] : '';

    //---
    self::$REDIRECT_AFTER_OPER = true;
  }
  //----------------------------------------------------------------------------
  // EVENT
  //----------------------------------------------------------------------------
  public static function setEvent($event)
  {
    //---
    Session::set('WEvent_EVENT_PREV', Session::get('WEvent_EVENT'));
    self::$EVENT_PREV = Session::get('WEvent_EVENT_PREV');

    //---
    Session::set('WEvent_EVENT', $event);
    self::$EVENT = $event;
  }
  //----------------------------------------------------------------------------
  // ROW_ID
  //----------------------------------------------------------------------------
  public static function delRowId()
  {
    global $objectsStatus;

    self::$ROW_ID = '';
    if($wo = $objectsStatus->getObject(self::$CONTROL)) {
       $wo->delRowId();
    }
  }
  //----------------------------------------------------------------------------
  public static function setRowId($value)
  {
    global $objectsStatus;

    self::$ROW_ID = $value;
    if($wo = $objectsStatus->getObject(self::$CONTROL)) {
       $wo->setRowId($value);
    }
  }
  //----------------------------------------------------------------------------
}
