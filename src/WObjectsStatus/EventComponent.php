<?
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 * 2006
 *
 */

namespace angelrove\membrillo2\WObjectsStatus;



abstract class EventComponent
{
  protected $id_object;
  protected $WEvent;
  protected $objectStatus;

  //------------------------------------------------
  public function __construct($id_object)
  {
    global $objectsStatus;

    $this->id_object = $id_object;

    $this->WEvent = $this->get_WEvent($id_object);
    $this->wObjectStatus = $objectsStatus->setNewObject($id_object);

    // $this->parse_event($this->WEvent); // se llama desde el propio objeto
  }
  //----------------------------------------------------------------------------
  private function get_WEvent($id_object)
  {
    $event = new \stdClass();
    $event->EVENT  = '';
    $event->OPER   = '';
    $event->ROW_ID = '';

    if(Event::$CONTROL == $id_object) {
       $event->EVENT  = Event::$EVENT;
       $event->OPER   = Event::$OPER;
       $event->ROW_ID = Event::$ROW_ID;
    }

    return $event;
  }
  //------------------------------------------------
  abstract function parse_event($WEvent);
  //------------------------------------------------
}
