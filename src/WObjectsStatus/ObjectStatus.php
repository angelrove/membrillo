<?php
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 */

namespace angelrove\membrillo\WObjectsStatus;


class ObjectStatus
{
    private $id         = '';
    private $path       = '';
    private $path_secc  = '';

    private $ROW_ID = '';
    private $datos  = array();

    private $persistent = false;

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

    public function setPath(string $path)
    {
        return $this->path = $path;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getPathSecc(): string
    {
        return $this->path_secc;
    }

    public function setPersistent(bool $flag = true)
    {
        return $this->persistent = $flag;
    }

    public function isPersistent()
    {
        return $this->persistent;
    }

    public function getDatos()
    {
        return $this->datos;
    }

    public function setDataDefault(string $name, $value = '')
    {
        if (!isset($this->datos[$name])) {
            $this->datos[$name] = $value;
        }
    }

    public function setDato(string $name, $value)
    {
        $this->datos[$name] = $value;
    }

    public function getDato(string $name)
    {
        return ($this->datos[$name])?? false;
    }

    public function delDato(string $name)
    {
        $this->datos[$name] = '';
    }

    // ROW_ID

    public function setRowId($value)
    {
        $this->ROW_ID = $value;
    }

    public function getRowId()
    {
        return $this->ROW_ID;
    }

    public function delRowId()
    {
        $this->ROW_ID = '';
    }

}
