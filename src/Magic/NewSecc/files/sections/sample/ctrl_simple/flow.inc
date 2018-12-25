<?php
use angelrove\membrillo\WObjectsStatus\Event;

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
