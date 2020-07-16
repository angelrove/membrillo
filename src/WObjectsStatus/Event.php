<?php
/**
 *  @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 */

namespace angelrove\membrillo\WObjectsStatus;

use angelrove\membrillo\WApp\Session;

// CRUD events --------------------------------
// Note: added to WPage!

define('CRUD_DEFAULT', 'default');

define('CRUD_EDIT_NEW', 'new'); // Read
define('CRUD_EDIT_UPDATE', 'edit');
define('CRUD_EDIT_PASS', 'edit_pass');

define('CRUD_LIST_SEARCH', 'search'); // List
define('CRUD_LIST_DETAIL', 'select');

define('CRUD_OPER_INSERT', 'insert'); // Opers
define('CRUD_OPER_UPDATE', 'update');
define('CRUD_OPER_DELETE', 'delete');
//---------------------------------------------

// API events ---------------------------------
define('API_GET_READ', 'read');
define('API_GET_FIND', 'find');
define('API_GET_EXIST', 'filter');
//---------------------------------------------


class Event
{
    public static $CONTROL;
    public static $EVENT;
    private static $EVENT_PREV;
    public static $OPER;

    public static $ROW_ID;
    public static $row_id;

    public static $REQUEST_METHOD;

    public static $REDIRECT_AFTER_OPER = true;
    public static $REDIRECT_AFTER_OPER_CLEAN = false;

    //----------------------------------------------------------------------------
    public static function initPage()
    {
        // No hay evento ---
        if (!isset($_REQUEST['CONTROL']) || !isset($_REQUEST['EVENT'])) {
            return;
        }

        // Event ----
        self::setEvent($_REQUEST['EVENT']);

        self::$CONTROL = $_REQUEST['CONTROL'];
        self::$OPER    = ($_REQUEST['OPER'])?? '';
        self::$ROW_ID  = ($_REQUEST['ROW_ID'])?? '';
        self::$row_id = self::$ROW_ID;

        //---
        self::$REDIRECT_AFTER_OPER = true;
    }
    //----------------------------------------------------------------------------
    /*
     * Events:
     * create, read, update, delete
     * edit_update, edit_new
     */
    public static function initPage_laravel()
    {

    }
    //----------------------------------------------------------------------------
    public static function initPage_api()
    {
        // Event ----
        self::$REQUEST_METHOD = @$_SERVER["REQUEST_METHOD"];
        self::$EVENT = ($_REQUEST['EVENT'])?? '';

        self::$ROW_ID = ($_REQUEST['ROW_ID'])?? '';
        self::$row_id = self::$ROW_ID;

        if (!self::$EVENT && self::$REQUEST_METHOD == 'GET') {
            if (self::$ROW_ID) {
                self::$EVENT = API_GET_FIND;
            } elseif (isset($_GET['exist'])) {
                self::$EVENT = API_GET_EXIST;
            } else {
                self::$EVENT = API_GET_READ;
            }
        }
    }
    //----------------------------------------------------------------------------
    // EVENT
    //----------------------------------------------------------------------------
    public static function setEvent(string $event)
    {
        //---
        Session::set('WEvent_EVENT_PREV', Session::get('WEvent_EVENT'));
        self::$EVENT_PREV = Session::get('WEvent_EVENT_PREV');

        //---
        Session::set('WEvent_EVENT', $event);
        self::$EVENT = $event;
    }
    //----------------------------------------------------------------------------
    // ROW_ID
    //----------------------------------------------------------------------------
    public static function delRowId()
    {
        global $objectsStatus;

        self::$ROW_ID = '';
        self::$row_id = self::$ROW_ID;

        if ($wo = $objectsStatus->getObject(self::$CONTROL)) {
            $wo->delRowId();
        }
    }
    //----------------------------------------------------------------------------
    public static function setRowId($value)
    {
        global $objectsStatus;

        self::$ROW_ID = $value;
        self::$row_id = self::$ROW_ID;

        if ($wo = $objectsStatus->getObject(self::$CONTROL)) {
            $wo->setRowId($value);
        }
    }
    //----------------------------------------------------------------------------
}
