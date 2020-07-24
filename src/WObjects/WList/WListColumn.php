<?php
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 */

namespace angelrove\membrillo\WObjects\WList;

class WListColumn
{
    public $name;
    public $title;
    public $width;
    public $max_width;
    public $align;
    public $type;
    public $param1;
    public $order;
    public $class;
    public $onClick;
    public $preventDefault;

    //-------------------------------------------------------
    public function __construct($name, $title, $width = '', $align = '', $type = '')
    {
        $this->name  = $name;
        $this->title = $title;
        if ($width) {
            $this->width = $width.'px';
        }
        $this->align = $align;
        $this->type  = $type;
    }
    //-------------------------------------------------------
    public function order(string $field = '')
    {
        $this->order = (!$field)? $this->name : $field;
        return $this;
    }
    //-------------------------------------------------------
    public function type(string $type, $param1 = null)
    {
        $this->type = $type;
        $this->param1 = $param1;
        return $this;
    }
    //-------------------------------------------------------
    public function width($width)
    {
        $this->width = $width.'px';
        return $this;
    }
    //-------------------------------------------------------
    public function align(string $align)
    {
        $this->align = $align;
        return $this;
    }
    //-------------------------------------------------------
    public function setClass()
    {
        $this->class = $this->name;
        return $this;
    }
    //-------------------------------------------------------
    public function onClick()
    {
        $this->onClick = $this->name;
        return $this;
    }
    //-------------------------------------------------------
    public function preventDefault()
    {
        $this->preventDefault = true;
        return $this;
    }
    //-------------------------------------------------------
}
