<?php
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 * Descripción.
 *   define las propiedades de las secciones: identificador, carpeta, título,...
 *
 */

namespace angelrove\membrillo\WApp;

class Config_Secciones
{
    private $defaultSecc  = '';
    private $listSections = array();

    public $listItems    = array();
    public $listSubItems = array();

    //---------------------------------------------------
    public function __construct()
    {
        // System section 'My account' ---
        $this->setSection("myaccount")->hide();
        $this->setSubSection("myaccount", "mydata");
        $this->setSubSection("myaccount", "updatepasswd");
    }
    //---------------------------------------------------
    public function setSections(array $listSections, array $listSubSections = array()): void
    {
        // Sections -----
        foreach ($listSections as $key => $title) {
            $this->setSection($key, $title);
        }

        // Sub sections ---
        $this->setSubSections($listSubSections);
    }
    //---------------------------------------------------
    public function setSection(string $key, string $title = '', bool $isDefault = false): Config_Secciones_Item
    {
        if (!$title) {
            $title = $key;
        }

        $this->listItems[$key]    = $title;
        $this->listSections[$key] = new Config_Secciones_Item($key, $title);

        if ($isDefault == true) {
            $this->defaultSecc = $key;
        }

        return $this->listSections[$key];
    }
    //---------------------------------------------------
    public function setSubSections(array $listSubSections = array()): void
    {
        foreach ($listSubSections as $id_padre => $listSub) {
            foreach ($listSub as $id => $title) {
                $this->setSubSection($id_padre, $id, $title);
            }
        }
    }
    //---------------------------------------------------
    public function setSubSection($id_padre, $id, string $title = ''): Config_Secciones_Item
    {
        if (!$title) {
            $title = $id;
        }
        $this->listSubItems[$id_padre][$id] = $title; // para WMain_menu

        $id = $id_padre . '/' . $id;
        $this->listSections[$id] = new Config_Secciones_Item($id, $title);

        return $this->listSections[$id];
    }
    //---------------------------------------------------
    public function isSeccion($id): bool
    {
        if (isset($this->listSections[$id])) {
            return true;
        }
        return false;
    }
    //---------------------------------------------------
    // SET
    //---------------------------------------------------
    public function setSection_logo($id_section, string $logo): void
    {
        if (isset($this->listSections[$id_section])) {
            $this->listSections[$id_section]->logo($logo);
        }
    }
    //---------------------------------------------------
    public function setSection_hide(): void
    {
        if (isset($this->listSections[$id_section])) {
            $this->listSections[$id_section]->hide();
        }
    }
    //---------------------------------------------------
    public function setSection_path($id_section, string $path): void
    {
        if ($this->listSections[$id_section]) {
            $this->listSections[$id_section]->path = $path;
        }
    }
    //---------------------------------------------------
    public function setSection_folder($id_section, string $folder): void
    {
        if ($this->listSections[$id_section]) {
            $this->listSections[$id_section]->folder = $folder;
        }
    }
    //---------------------------------------------------
    public function setSection_db($id_section, string $db): void
    {
        if ($this->listSections[$id_section]) {
            $this->listSections[$id_section]->db = $db;
        }
    }
    //---------------------------------------------------
    public function setSection_link($id_section, string $link): void
    {
        if ($this->listSections[$id_section]) {
            $this->listSections[$id_section]->link = $link;
        }
    }
    //---------------------------------------------------
    public function setSection_upload(string $id_section, string $uploads_dir, string $uploads_dir_default): void
    {
        if ($this->listSections[$id_section]) {
            $this->listSections[$id_section]->uploads_dir     = $uploads_dir; // personalizado para la sección
            $this->listSections[$id_section]->uploads_default = $uploads_dir_default; // dir. por defecto
        }
    }
    //---------------------------------------------------
    public function setDefault(string $id_secc): void
    {
        $this->defaultSecc = $id_secc;
    }
    //---------------------------------------------------
    // GETS
    //---------------------------------------------------
    public function getDefault()
    {
        if ($this->defaultSecc) {
            return $this->defaultSecc;
        } else {
            $first = each($this->listSections);
            return $first['key'];
        }
    }
    //---------------------------------------------------
    public function getItem($id)
    {
        return $this->listSections[$id];
    }
    //---------------------------------------------------
    public function getList()
    {
        return $this->listSections;
    }
    //---------------------------------------------------
    public function getListItems()
    {
        return $this->listItems;
    }
    //---------------------------------------------------
    public function getListSubItems()
    {
        return $this->listSubItems;
    }
    //---------------------------------------------------
    public function getDb($id)
    {
        if (!isset($this->listSections[$id]->db)) {
            return \App::$conf_db['default']['DBNAME'];
        }
        return $this->listSections[$id]->db;
    }
    //---------------------------------------------------
    public function getFolder($id)
    {
        $path_secciones = PATH_SRC . '/sections/';

        // Path section ---
        if ($this->listSections[$id]->path) {
            $path_secciones = $this->listSections[$id]->path;
        }

        // Folder
        if ($this->listSections[$id]->folder) {
            return $path_secciones . $this->listSections[$id]->folder;
        }

        // Default ---
        return $path_secciones . $id;
    }
    //---------------------------------------------------
    public function getSection_logo($id)
    {
        return $this->listSections[$id]->logo;
    }
    //---------------------------------------------------
    public function getSection_link($id)
    {
        return $this->listSections[$id]->link;
    }
    //---------------------------------------------------
    public function getUploadsDir_default($id)
    {
        return $this->listSections[$id]->uploads_default;
    }
    //---------------------------------------------------
    public function getUploadsDir($id)
    {
        $uploads_dir = $this->listSections[$id]->uploads_dir;
        if ($uploads_dir) {
            return $uploads_dir;
        } else {
            return $this->listSections[$id]->uploads_default;
        }
    }
    //---------------------------------------------------
    public function getTitle($id)
    {
        return $this->listSections[$id]->title;
    }
    //---------------------------------------------------
}
