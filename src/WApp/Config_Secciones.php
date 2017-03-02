<?
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 * Descripción.
 *   define las propiedades de las secciones: identificador, carpeta, título,...
 *
 */

namespace angelrove\membrillo2\WApp;

class Config_Secciones
{
    private $defaultSecc  = '';
    private $listSections = array();

    public $listItems    = array();
    public $listSubItems = array();

    //---------------------------------------------------
    public function __construct()
    {

    }
    //---------------------------------------------------
    public function setSections(array $listSections, array $listSubSections = array())
    {
        $this->listItems    = $listSections; // para WMain_menu
        $this->listSubItems = $listSubSections; // para WMain_menu

        // Sections
        foreach ($listSections as $id => $title) {
            if (!$title) {
                $title = $id;
            }
            $this->listSections[$id] = new Config_Secciones_Item($id, $title);
        }

        // Sub sections
        foreach ($listSubSections as $id_padre => $listSub) {
            foreach ($listSub as $id => $title) {
                if (!$title) {
                    $title = $id;
                }

                $id                      = $id_padre . '/' . $id;
                $this->listSections[$id] = new Config_Secciones_Item($id, $title);
            }
        }
    }
    //---------------------------------------------------
    public function isSeccion($id)
    {
        if (isset($this->listSections[$id])) {
            return true;
        }
        return false;
    }
    //---------------------------------------------------
    // SET
    //---------------------------------------------------
    public function setSection_folder($id_section, $folder)
    {
        if ($this->listSections[$id_section]) {
            $this->listSections[$id_section]->folder = $folder;
        }
    }
    //---------------------------------------------------
    public function setSection_db($id_section, $db)
    {
        if ($this->listSections[$id_section]) {
            $this->listSections[$id_section]->db = $db;
        }
    }
    //---------------------------------------------------
    public function setSection_link($id_section, $link)
    {
        if ($this->listSections[$id_section]) {
            $this->listSections[$id_section]->link = $link;
        }
    }
    //---------------------------------------------------
    public function setSection_upload($id_section, $uploads_dir, $uploads_dir_default)
    {
        if ($this->listSections[$id_section]) {
            $this->listSections[$id_section]->uploads_dir     = $uploads_dir; // personalizado para la sección
            $this->listSections[$id_section]->uploads_default = $uploads_dir_default; // dir. por defecto
        }
    }
    //---------------------------------------------------
    public function setDefault($id_secc)
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
        global $CONFIG_DB;
        if (!isset($this->listSections[$id]->db)) {
            return $CONFIG_DB['default']['DBNAME'];
        }
        return $this->listSections[$id]->db;
    }
    //---------------------------------------------------
    public function getFolder($id)
    {
        $path_secciones = DOCUMENT_ROOT . '/app/';

        if (!$this->listSections[$id]->folder) {
            return $path_secciones . $id;
        }
        return $path_secciones . $this->listSections[$id]->folder;
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
