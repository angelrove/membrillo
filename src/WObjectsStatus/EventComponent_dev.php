<?php
namespace angelrove\membrillo\WObjectsStatus;

abstract class EventComponent2
{
    //----------------------------------------------------------------------------
    public function __construct($id_component)
    {
        global $objectsStatus;

        $objectsStatus->setNewObject2($id_component, $this);
    }
    //----------------------------------------------------------------------------
    abstract public function parse_oper($oper, $row_id);
    abstract public function parse_event($event);
    //----------------------------------------------------------------------------
}
