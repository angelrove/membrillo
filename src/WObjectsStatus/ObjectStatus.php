<?php
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 */

namespace angelrove\membrillo\WObjectsStatus;

use angelrove\membrillo\WObjectsStatus\Event;

class ObjectStatus
{
    private $id         = '';
    private $persistent = false;
    private $path       = '';

    private $ROW_ID = '';
    private $datos  = array();

    //----------------------------------------------------------------------------
    public function __construct($id, $path = '')
    {
        $this->id = $id;

        // Path
        $this->path = $path;

        if (!$this->path) {
            global $CONFIG_SECCIONES, $seccCtrl;

            $this->path_secc = $CONFIG_SECCIONES->getFolder($seccCtrl->secc);
            $this->path = $this->path_secc . '/ctrl_' . $id;
        }
    }
    //----------------------------------------------------------------------------
    public function updateDatos()
    {
        foreach ($_REQUEST as $name => $value) {
            if ($name == 'secc' || $name == 'PHPSESSID' || $name == '__utma' ||
                $name == 'CONTROL' || $name == 'EVENT' || $name == 'OPER') {
                continue;
            }

            if ($name == 'ROW_ID') {
                $this->ROW_ID = $value;
            } else {
                $this->datos[$name] = $value;
            }
        }
    }
    //----------------------------------------------------------------------------
    public function parse_oper($oper, $row_id)
    {
        global $objectsStatus;
        include $this->path . '/oper.inc';
    }
    //----------------------------------------------------------------------------
    public function parse_event($event)
    {
        global $objectsStatus;

        include $this->path . '/flow.inc';
    }
    //----------------------------------------------------------------------------
    public function parse_event_api($event)
    {
        global $objectsStatus;

        switch (EVENT::$REQUEST_METHOD) {
            case 'GET':
                include $this->path_secc . '/get.inc';
            break;

            case 'POST':
                include $this->path_secc . '/post.inc';
            break;

            case 'DELETE':
                include $this->path_secc . '/delete.inc';
            break;

            case 'PUT':
                include $this->path_secc . '/put.inc';
            break;

            default:
                header("HTTP/1.0 405 Method Not Allowed");
                exit();
        }
    }
    //----------------------------------------------------------------------------
    //----------------------------------------------------------------------------
    public function setPath($path)
    {
        return $this->path = $path;
    }
    //----------------------------------------------------------------------------
    public function getPath()
    {
        return $this->path;
    }
    //----------------------------------------------------------------------------
    public function setPersistent($flag = true)
    {
        return $this->persistent = $flag;
    }
    //----------------------------------------------------------------------------
    public function isPersistent()
    {
        return $this->persistent;
    }
    //----------------------------------------------------------------------------
    //----------------------------------------------------------------------------
    public function setDato($name, $value)
    {
        $this->datos[$name] = $value;
    }
    //----------------------------------------------------------------------------
    public function setDataDefault($name, $value='')
    {
        if (!isset($this->datos[$name])) {
            $this->datos[$name] = $value;
        }
    }
    //----------------------------------------------------------------------------
    public function getDato($name)
    {
        if (isset($this->datos[$name])) {
            return $this->datos[$name];
        }
        return false;
    }
    //----------------------------------------------------------------------------
    public function getDatos()
    {
        return $this->datos;
    }
    //----------------------------------------------------------------------------
    public function delDato($name)
    {
        $this->datos[$name] = '';
    }
    //----------------------------------------------------------------------------
    // ROW_ID
    //----------------------------------------------------------------------------
    public function setRowId($value)
    {
        $this->ROW_ID = $value;
    }
    //----------------------------------------------------------------------------
    public function getRowId()
    {
        return $this->ROW_ID;
    }
    //----------------------------------------------------------------------------
    public function delRowId()
    {
        $this->ROW_ID = '';
    }
    //----------------------------------------------------------------------------
}
