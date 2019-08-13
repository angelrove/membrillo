<?php
use angelrove\membrillo\WObjectsStatus\Event;

switch(Event::$EVENT) {

  case CRUD_EDIT_NEW:
  case CRUD_EDIT_UPDATE:
    include('ctrl_parent/tmpl_edit.inc');
  break;

  default:
    include('tmpl_main.inc');
  break;

}
