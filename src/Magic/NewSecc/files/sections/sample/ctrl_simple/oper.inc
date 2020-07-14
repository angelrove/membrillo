<?php
use angelrove\membrillo\WObjectsStatus\Event;
use angelrove\membrillo\Messages;
use App\Models\[Sample];

switch(Event::$OPER) {

    case CRUD_OPER_INSERT:
        [Sample]::createForm();
        break;

    case CRUD_OPER_UPDATE:
        [Sample]::find(Event::$row_id)->updateForm();
        Messages::set("¡Guardado!");
        break;

    case CRUD_OPER_DELETE:
        [Sample]::find(Event::$row_id)->delete();
        Messages::set("Deleted!");
        break;

}
