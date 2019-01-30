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
    public $type;
    public $orderable;

    //-------------------------------------------------------
    public function __construct($name, $title, $type = '', $orderable = false)
    {
        $this->name  = $name;
        $this->title = $title;
        $this->type = $type;
        $this->orderable = $orderable;
    }
    //-------------------------------------------------------
}
