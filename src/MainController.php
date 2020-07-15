<?php

namespace angelrove\membrillo;

use angelrove\membrillo\WObjectsStatus\Event;

class MainController
{
    //----------------------------------------------------------------------------
    // Operations
    static public function parseOper($wObjectStatus)
    {
        if (!Event::$OPER) {
            return;
        }

        Messages::set_empty();

        //---
        global $objectsStatus;

        $path  = $wObjectStatus->getPath();
        $path2 = $wObjectStatus->getPathSecc().'/ctrl_global';

        if (is_dir($path)) {
            include $path . '/oper.inc';
        }
        elseif (is_dir($path2)) {
            include $path2.'/oper.inc';
        }
        else {
            throw new \Exception("Error accessing to control dir: \n $path \n or\n $path2 \n", 1);
        }

        // Redirect ---
        if (!error_get_last() && Event::$REDIRECT_AFTER_OPER) {
            // Messages::set_debug('>> Redirected ---');

            if (Event::$REDIRECT_AFTER_OPER_CLEAN) {
                header('Location:' . CrudUrl::get('', '', '', ''));
            } else {
                header('Location:' . CrudUrl::get(
                    Event::$EVENT,
                    Event::$CONTROL,
                    Event::$ROW_ID,
                    '', 'OPERED=' . Event::$OPER
                ));
            }

            exit();
        }
    }
    //----------------------------------------------------------------------------
    // Flow
    static public function parseEvent($wObjectStatus)
    {
        // No event ---
        if (!Event::$EVENT) {
            throw new \Exception("membrillo MainControler error: Event not exists!");
        }

        global $objectsStatus;

        $path  = $wObjectStatus->getPath();
        $path2 = $wObjectStatus->getPathSecc().'/ctrl_global';

        if (is_dir($path)) {
            include $path . '/flow.inc';
        }
        elseif (is_dir($path2)) {
            include $path2.'/flow.inc';
        }
        else {
            throw new \Exception("Error accessing to control dir: \n $path \n or\n $path2 \n", 1);
        }
    }
    //----------------------------------------------------------------------------
    // Flow
    static public function parseAjaxEvent($wObjectStatus)
    {
        // No event ---
        if (!Event::$EVENT) {
            throw new \Exception("membrillo Ajax MainControler error: Event not exists!");
        }

        global $objectsStatus;

        $path  = $wObjectStatus->getPath();
        $path2 = $wObjectStatus->getPathSecc().'/ctrl_global';

        if (is_dir($path)) {
            include $path . '/flow.inc';
        }
        elseif (is_dir($path2)) {
            include $path2.'/flow.inc';
        }
        else {
            throw new \Exception("Error accessing to control dir: \n $path \n or\n $path2 \n", 1);
        }
    }
    //----------------------------------------------------------------------------
    // Flow
    static public function parseApiEvent($wObjectStatus)
    {
        // No event ---
        // if (!Event::$EVENT) {
        //     throw new \Exception("membrillo ajax error: Event is empty");
        // }

        // View ------
        $pathSecc = $wObjectStatus->getPathSecc();

        switch (EVENT::$REQUEST_METHOD) {
            case 'GET':
                include $pathSecc . '/get.inc';
            break;

            case 'POST':
                include $pathSecc . '/post.inc';
            break;

            case 'DELETE':
                include $pathSecc . '/delete.inc';
            break;

            case 'PUT':
                include $pathSecc . '/put.inc';
            break;

            default:
                header("HTTP/1.0 405 Method Not Allowed");
                exit();
        }
    }
    //----------------------------------------------------------------------------
}