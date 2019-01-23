<?php
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 */

namespace angelrove\membrillo\WObjects\WDataTable;

class Column
{
    public $name;
    public $title;
    public $orderable;

    //-------------------------------------------------------
    public function __construct($name, $title, $orderable = false)
    {
        $this->name  = $name;
        $this->title = $title;
        $this->orderable = $orderable;
    }
    //-------------------------------------------------------
}
