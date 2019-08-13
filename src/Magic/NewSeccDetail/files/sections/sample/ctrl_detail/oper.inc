<?php
use angelrove\membrillo\WObjectsStatus\Event;
use angelrove\membrillo\Messages;
use App\Models\[Model_detail];

switch (Event::$OPER) {
    case CRUD_OPER_INSERT:
        [Model_detail]::create();
        break;

  //--------------------

    case CRUD_OPER_UPDATE:
        [Model_detail]::update();
        Messages::set("¡Saved!");
        break;

  //--------------------

    case CRUD_OPER_DELETE:
        [Model_detail]::delete();
        Messages::set("¡Deleted!");
        break;
}
