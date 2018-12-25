<?php
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 * Esta clase la deben extender los componentes que quieran
 * gestionar sus eventos: WList, WForm,...
 *
 */

namespace angelrove\membrillo\WObjectsStatus;

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

        $this->WEvent        = $this->get_event($id_object);
        $this->wObjectStatus = $objectsStatus->setNewObject($id_object);

        // $this->parse_event($this->WEvent); // se llama desde el propio objeto
    }
    //----------------------------------------------------------------------------
    private function get_event($id_object)
    {
        $event         = new \stdClass();
        $event->EVENT  = '';
        $event->OPER   = '';
        $event->ROW_ID = '';

        if (Event::$CONTROL == $id_object) {
            $event->EVENT  = Event::$EVENT;
            $event->OPER   = Event::$OPER;
            $event->ROW_ID = Event::$ROW_ID;
        }

        return $event;
    }
    //------------------------------------------------
    abstract public function parse_event($WEvent);
    //------------------------------------------------
}
