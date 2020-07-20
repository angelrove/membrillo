<?php
namespace angelrove\membrillo\WObjectsStatus;

abstract class EventComponentDev
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
