<?php
use angelrove\membrillo2\WObjectsStatus\Event;
use App\Models\User;

switch(Event::$EVENT) {

  case CRUD_EDIT_NEW:
    include('tmpl_edit.inc');
  break;

  case CRUD_EDIT_UPDATE:
    include('tmpl_edit.inc');
  break;

  default:
    include('tmpl_main.inc');
  break;

}
