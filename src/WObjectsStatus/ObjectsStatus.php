<?php
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 */

namespace angelrove\membrillo\WObjectsStatus;

use angelrove\membrillo\CrudUrl;
use angelrove\membrillo\Messages;

class ObjectsStatus
{
    private $listObjects = [];

    //----------------------------------------------------------------------------
    public function __construct()
    {
    }
    //----------------------------------------------------------------------------
    public function setNewObject(string $idControl, string $path = '')
    {
        if (!isset($this->listObjects[$idControl])) {
            $this->listObjects[$idControl] = new ObjectStatus($idControl, $path);
        }

        return $this->listObjects[$idControl];
    }
    //----------------------------------------------------------------------------
    // public function setNewObject2($idControl, $component)
    // {
    //   if(!isset($this->listObjects[$idControl])) {
    //       $this->listObjects[$idControl] = $component;
    //   }
    //   return $this->listObjects[$idControl];
    // }
    //----------------------------------------------------------------------------
    public function getObject(string $idControl)
    {
        return ($this->listObjects[$idControl])?? false;
    }
    //----------------------------------------------------------------------------
    public function initPage()
    {
        global $seccCtrl;

        // If a new secc: delete data from non-persistent objets
        if ($seccCtrl->isNewSecc) {
            self::clearObjects();
        }
    }
    //----------------------------------------------------------------------------
    public function clearObjects()
    {
        foreach ($this->listObjects as $key => $object) {
            if ($object->isPersistent() == false) {
                unset($this->listObjects[$key]);
            }
        }
    }
    //----------------------------------------------------------------------------
    public function getPath(string $idControl)
    {
        if (isset($this->listObjects[$idControl])) {
            return $this->listObjects[$idControl]->getPath();
        }
        return false;
    }
    //----------------------------------------------------------------------------
    public function parseAjaxEvent(string $path_secc)
    {
        $wObjectStatus = $this->getObject(Event::$CONTROL);

        // flow (view) ------
        if (Event::$EVENT) {
            $wObjectStatus->parseEvent(Event::$EVENT);
        }
    }
    //----------------------------------------------------------------------------
    public function parseEvent(string $path_secc)
    {
        // onInitPage
        @include $path_secc . '/onInitPage.inc';

        // Default view
        if (!Event::$EVENT) {
            include $path_secc . '/tmpl_main.inc';
            return;
        }

        $wObjectStatus = $this->setNewObject(Event::$CONTROL); // if no exist
        $wObjectStatus->updateDatos();

        // oper ------
        if (Event::$OPER) {
            Messages::set_empty();
            $wObjectStatus->parse_oper(Event::$OPER, Event::$ROW_ID);

            // redirect
            if (!error_get_last() && Event::$REDIRECT_AFTER_OPER) {
                // Messages::set_debug('>> Redirected ---');

                if (Event::$REDIRECT_AFTER_OPER_CLEAN) {
                    header('Location:' . CrudUrl::get('', '', '', ''));
                } else {
                    header('Location:' . CrudUrl::get(
                        Event::$EVENT,
                        Event::$CONTROL,
                        Event::$ROW_ID,
                        '',
                        'OPERED=' . Event::$OPER
                    ));
                }

                exit();
            }
        }

        // flow (view) ------
        if (Event::$EVENT) {
            $wObjectStatus->parseEvent(Event::$EVENT);
        }
    }
    //----------------------------------------------------------------------------
    public function parseEvent_api(string $path_secc)
    {
        $wObjectStatus = $this->setNewObject(Event::$CONTROL); // if no exist
        $wObjectStatus->updateDatos();

        // flow (view) ------
        $wObjectStatus->parse_event_api(Event::$EVENT);
    }
    //----------------------------------------------------------------------------
    //----------------------------------------------------------------------------
    public function setDato(string $idControl, string $name, $value)
    {
        $this->setNewObject($idControl);
        $this->listObjects[$idControl]->setDato($name, $value);
    }
    //----------------------------------------------------------------------------
    /* Get object data or create new */
    public function getDatos(string $idControl, array $defaults = [])
    {
        if (!isset($this->listObjects[$idControl])) {
            $this->setNewObject($idControl);
        }

        // Set default values ---
        foreach ($defaults as $name => $value) {
            $this->listObjects[$idControl]->setDataDefault($name, $value);
        }

        return $this->listObjects[$idControl]->getDatos();
    }
    //----------------------------------------------------------------------------
    public function getDato(string $idControl, string $name)
    {
        if (isset($this->listObjects[$idControl])) {
            return $this->listObjects[$idControl]->getDato($name);
        }
        return null;
    }
    //----------------------------------------------------------------------------
    // ROW_ID
    //----------------------------------------------------------------------------
    public function getRowId(string $idControl)
    {
        if (isset($this->listObjects[$idControl])) {
            return $this->listObjects[$idControl]->getRowId();
        }
        return null;
    }
    //----------------------------------------------------------------------------
}
