<?php
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 * Status and properties of current section
 *
 */

namespace angelrove\membrillo\WApp;

use angelrove\utils\Db_mysql;

class SeccCtrl
{
    // Sección actual
    public $isNewSecc = false;
    public $secc;
    public $secc_padre;

    // Parámetros de la sección
    public $title;
    public $SECC_DB;
    public $UPLOADS_DIR;
    public $include_path;

    //----------------------------------------------------------------------------
    public function __construct($secc)
    {
        global $CONFIG_SECCIONES;

        // ¿Existe la sección?
        if (!$CONFIG_SECCIONES->isSeccion($secc)) {
            $strErr = "SeccCtrl: the section \"$secc\" no exist!";
           //user_error($strErr, E_USER_WARNING);
            require('404.php');
        }

        /** Sección **/
        $this->secc = $secc;

        /** Sección_padre (cuando es subsección) **/
        $parseSecc = explode('/', $this->secc);
        if (isset($parseSecc[1])) {
            $this->secc_padre = $parseSecc[0];
        }

        /** Secc. params **/
        $this->title = $CONFIG_SECCIONES->getTitle($this->secc);
        $this->SECC_DB = $CONFIG_SECCIONES->getDb($this->secc);
        $this->UPLOADS_DIR = $CONFIG_SECCIONES->getUploadsDir($this->secc);
        $this->UPLOADS_DIR_DEFAULT = $CONFIG_SECCIONES->getUploadsDir_default($this->secc);

        /** 'include_path' **/
        $this->include_path = ini_get('include_path') . PATH_SEPARATOR.
                           $CONFIG_SECCIONES->getFolder($this->secc) . PATH_SEPARATOR;
    }
    //----------------------------------------------------------------------------
    public function initSecc()
    {
        $this->isNewSecc = true;
        $this->init();
    }
    //----------------------------------------------------------------------------
    public function initPage()
    {
        $this->isNewSecc = false;
        $this->init();
    }
    //----------------------------------------------------------------------------
    public function init()
    {
        /** 'include_path' **/
        ini_set('include_path', $this->include_path);

        /** select_db **/
        // mysqli_select_db(Db_mysql::$db_dbconn, $this->SECC_DB);
    }
    //----------------------------------------------------------------------------
    public function getSecc()
    {
        return $this->secc;
    }
    //----------------------------------------------------------------------------
    public function getKey()
    {
        return str_replace('/', '-', $this->secc);
    }
    //----------------------------------------------------------------------------
}
