<?php
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 */

namespace angelrove\membrillo\WObjectsStatus;


class ObjectsStatus
{
    private $listObjects = [];


    public function initPage()
    {
        global $seccCtrl;

        // New section: clear data from non-persistent objets
        if ($seccCtrl->isNewSecc) {
            self::clearObjects();
        }
    }

    public function setNewObject(string $idControl, string $path = '')
    {
        if (!$idControl) {
            throw new \Exception("membrillo error: Event is empty");
        }

        if (!isset($this->listObjects[$idControl])) {
            $this->listObjects[$idControl] = new ObjectStatus($idControl, $path);
        }

        return $this->listObjects[$idControl];
    }

    public function getObject(string $idControl)
    {
        return ($this->listObjects[$idControl])?? false;
    }

    public function clearObjects()
    {
        foreach ($this->listObjects as $key => $object) {
            if ($object->isPersistent() == false) {
                unset($this->listObjects[$key]);
            }
        }
    }

    /*
     * ObjectStatus
     */

    public function getPath(string $idControl)
    {
        if (isset($this->listObjects[$idControl])) {
            return $this->listObjects[$idControl]->getPath();
        }
        return false;
    }

    public function setDato(string $idControl, string $name, $value)
    {
        $this->setNewObject($idControl);
        $this->listObjects[$idControl]->setDato($name, $value);
    }

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

    public function getDato(string $idControl, string $name)
    {
        if (isset($this->listObjects[$idControl])) {
            return $this->listObjects[$idControl]->getDato($name);
        }
        return null;
    }

    public function getRowId(string $idControl)
    {
        if (isset($this->listObjects[$idControl])) {
            return $this->listObjects[$idControl]->getRowId();
        }
        return null;
    }
}
