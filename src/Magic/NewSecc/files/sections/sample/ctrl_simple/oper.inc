<?php
use angelrove\membrillo2\WObjectsStatus\Event;
use angelrove\membrillo2\Messages;
use App\Models\[Sample];

switch(Event::$OPER) {

  case CRUD_OPER_INSERT:
    [Sample]::create();
  break;

  //--------------------

  case CRUD_OPER_UPDATE:
    [Sample]::update();
    Messages::set("¡Guardado!");
  break;

  //--------------------

  case CRUD_OPER_DELETE:
    [Sample]::delete();
    Messages::set("¡Deleted!");
  break;

}
