<?php
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 */

namespace angelrove\membrillo\WApp;

class Config_Secciones_Item
{
    public $id;
    public $title;
    public $logo;
    public $hide;
    public $folder;
    public $path;
    public $link;
    public $uploads_default;
    public $uploads_dir;

    public function __construct($id_section, $title)
    {
        $this->id    = $id_section;
        $this->title = $title;
    }

    public function logo($logo)
    {
        $this->logo = $logo;
        return $this;
    }

    public function hide()
    {
        $this->hide = true;
        return $this;
    }

    public function folder($folder)
    {
        $this->folder = $folder;
        return $this;
    }

    public function path($path)
    {
        $this->path = $path;
        return $this;
    }
}
