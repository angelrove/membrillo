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
    public function __construct(string $id, string $path = '')
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
    // Operations
    public function parse_oper(string $oper, $row_id)
    {
        global $objectsStatus;

        $path = $this->path;
        if (is_dir($this->path)) {
            include $this->path . '/oper.inc';
            return;
        }

        $path2 = $this->path_secc.'/ctrl_global';
        if (is_dir($path2)) {
            include $path2.'/oper.inc';
            return;
        }

        throw new \Exception("Error accessing to control dir: \n $path \n or\n $path2 \n", 1);
    }
    //----------------------------------------------------------------------------
    // Flow
    public function parseEvent(string $event)
    {
        global $objectsStatus;

        $path = $this->path;
        if (is_dir($path)) {
            include $path . '/flow.inc';
            return;
        }

        $path2 = $this->path_secc.'/ctrl_global';
        if (is_dir($path2)) {
            include $path2.'/flow.inc';
            return;
        }

        throw new \Exception("Error accessing to control dir: \n $path \n or\n $path2 \n", 1);
    }
    //----------------------------------------------------------------------------
    public function parse_event_api(string $event)
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
    public function setPath(string $path)
    {
        return $this->path = $path;
    }
    //----------------------------------------------------------------------------
    public function getPath(): string
    {
        return $this->path;
    }
    //----------------------------------------------------------------------------
    public function setPersistent(bool $flag = true)
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
    public function setDato(string $name, $value)
    {
        $this->datos[$name] = $value;
    }
    //----------------------------------------------------------------------------
    public function setDataDefault(string $name, $value = '')
    {
        if (!isset($this->datos[$name])) {
            $this->datos[$name] = $value;
        }
    }
    //----------------------------------------------------------------------------
    public function getDato(string $name)
    {
        return ($this->datos[$name])?? false;
    }
    //----------------------------------------------------------------------------
    public function getDatos()
    {
        return $this->datos;
    }
    //----------------------------------------------------------------------------
    public function delDato(string $name)
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
