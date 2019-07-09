<?php
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 * use http://bassistance.de/jquery-plugins/jquery-plugin-treeview/
 *     https://github.com/jzaefferer/jquery-treeview
 */

namespace angelrove\membrillo\WObjects\WTree2;

use angelrove\membrillo\CrudUrl;
use angelrove\utils\CssJsLoad;
use angelrove\utils\Db_mysql;

/*
 * Obligatorios:
 *   DB table: 'categorias' -> 'id_padre'
 *
 */

class WTree2
{
    private $id;
    private $dbTable;
    private $title   = 'Categorías';
    private $niveles;

    private $width;
    private $height;

    private $opNew    = true;
    private $opUpdate = false;
    private $opNewSub = true;
    private $opDelete = true;

    private $marcas      = array(); // Busquedas
    private $statusCiclo = array(); // Status de la generación HTML

    private $sqlWhere    = '';
    private $sqlWhere_L3 = '';

    private $haveElementsOnLevel;
    private $opCheckBox;
    private $searchWord;

    //-----------------------------------------------------------------
    public function __construct($idComponent, $dbTable, $width=300, $niveles = 3)
    {
        CssJsLoad::set(DOCUMENT_ROOT.'/public/app/_libs/jquery-treeview/jquery.treeview.css');
        CssJsLoad::set(DOCUMENT_ROOT.'/public/app/_libs/jquery-treeview/jquery.treeview.js');
        CssJsLoad::set(DOCUMENT_ROOT.'/public/app/_libs/jquery-treeview/lib/jquery.cookie.js');

        CssJsLoad::set(__DIR__ . '/styles.css');
        CssJsLoad::set(__DIR__ . '/libs2.js');

        $this->id      = $idComponent;
        $this->dbTable = $dbTable;
        $this->width   = $width . 'px';
        $this->niveles = $niveles;
    }
    //-----------------------------------------------------------------
    public function setTitle($title)
    {
        $this->title = $title;
    }
    //-----------------------------------------------------------------
    public function haveElementsOnLevel($level)
    {
        $this->haveElementsOnLevel = $level;
    }
    //-----------------------------------------------------------------
    public function showOpNew($value)
    {
        $this->opNew = $value;
    }
    //-----------------------------------------------------------------
    public function showOpNewSub($value)
    {
        $this->opNewSub = $value;
    }
    //-----------------------------------------------------------------
    public function showOpUpdate($flag)
    {
        $this->opUpdate = $flag;
    }
    //-----------------------------------------------------------------
    public function showOpDelete($value)
    {
        $this->opDelete = $value;
    }
    //-----------------------------------------------------------------
    public function showCheckBox($name, $onLevel, array $listSelected)
    {
        $this->opCheckBox         = true;
        $this->check_name         = $name;
        $this->check_onLevel      = $onLevel;
        $this->check_listSelected = $listSelected;
    }
    //-----------------------------------------------------------------
    public function setWhere($sqlWhere)
    {
        $this->sqlWhere = 'AND ' . $sqlWhere;
    }
    //-----------------------------------------------------------------
    public function setWhere_L3($sqlWhere)
    {
        $this->sqlWhere_L3 = 'AND ' . $sqlWhere;
    }
    //---------------------------------------------------------------------
    public function setReadOnly($isReadonly)
    {
        $this->isReadonly = $isReadonly;
        $this->opDelete   = false;
        $this->opNew      = false;
        $this->opNewSub   = false;
    }
    //-----------------------------------------------------------------
    public function showSearcher()
    {
        $this->searcher = true;

        global $objectsStatus;
        $filtros = $objectsStatus->getDatos($this->id);
        $this->searchWord = @$filtros['f_text'];
    }
    //-----------------------------------------------------------------
    private function getSearcher()
    {
        return \angelrove\membrillo\WObjects\WList\WList::searcher_complet($this->id, $this->searchWord);
    }
    //-----------------------------------------------------------------
    public function setSearchWord($searchWord)
    {
        $this->searchWord = $searchWord;
    }
    //-----------------------------------------------------------------
    //-----------------------------------------------------------------
    public function get()
    {
        // HTM de categorías
        $strCategorias = $this->get_category_tree();

        // Button "Nuevo"
        $href     = CrudUrl::get(CRUD_EDIT_NEW, $this->id, '', '', 'level=1');
        $strNuevo = '<a class="btNuevo" href="' . $href . '">New...</a>';

        if ($this->opNew == false) {
            $strNuevo = '';
        }

        // Searcher ------
        $htmSearcher = '';
        if ($this->searcher) {
            $htmSearcher = $this->getSearcher();
        }

        // Marcar búsquedas ---
        $strMarcar = '';
        foreach ($this->marcas as $id_cat => $xx) {
            $strMarcar .= '$("#cat_' . $id_cat . '").addClass("selected");';
        }

        CssJsLoad::set_script('
/*
prerendered: true,
unique:      true,
persist:     "location",
cookieId:    "treeview-black",
*/
$(document).ready(function() {
  $("#tree").treeview({
     control:   "#sidetreecontrol",
     collapsed: true,
     animated:  "fast",
     persist:   "cookie"
  });

  // Marcar búsquedas
  ' . $strMarcar . '
});
');

        // Out -----
        return <<<EOD
<script>
var WTree2_CONTROL = '$this->id';
</script>

<div id="sidetree" style="width:$this->width">
    $htmSearcher

    <!-- Cabecera -->
    <table class="WTree_cabecera" width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td id="sidetreecontrol">
               <a href="?#"><i class="fas fa-angle-double-up fa-lg"></i></a>
               &nbsp;
               <a href="?#"><i class="fas fa-angle-double-down fa-lg"></i></a>
            </td>
            <td class="title">$this->title</td>
            <td class="btNuevo">$strNuevo</td>
        </tr>
    </table>
    <!-- /Cabecera -->

    <!-- TREE DATA -->
    <div class="WTree_tuplas">
        <ul id="tree">$strCategorias
        </ul>
    </div>
    <!-- /TREE DATA -->
</div>
EOD;
    }
    //-----------------------------------------------------------------
    // PRIVATE
    //-----------------------------------------------------------------
    private function get_category_tree($id_padre = 0, $nivel = 0)
    {
        $strTree = '';
        $nivel++;
        $count = 0;

        //$sqlWhere_L3 = ($nivel == 3)? $this->sqlWhere_L3 : '';
        $listCategorias = $this->getCategorias($id_padre, $nivel);

        foreach ($listCategorias as $id => $categ) {
            $count++;

            $tieneSubc = Db_mysql::getValue("SELECT id FROM $this->dbTable WHERE id_padre='$id' $this->sqlWhere");
            $strTree .= $this->getHtmOpen($id, $categ['name'], $nivel, $count, $tieneSubc);
            $strTree .= $this->get_category_tree($id, $nivel);
            $strTree .= $this->getHtmCierre($id, $nivel, $count, $tieneSubc);

            // Search -----
            if ($this->searchWord) {
                if (stripos($categ['name'], $this->searchWord) !== false) {
                    $this->marcas[$id] = true;
                }
                if (isset($this->marcas[$id]) && $this->marcas[$id] && $id_padre != 0) {
                    // padres
                    $this->marcas[$id_padre] = true;
                }
            }
        }
        $nivel--;

        return $strTree;
    }
    //-----------------------------------------------------------------
    //-----------------------------------------------------------------
    private function getCategorias($id_padre, $nivel = 0)
    {
        $sqlWhere_L3 = ($nivel == 3) ? $this->sqlWhere_L3 : '';

        $sqlQ = "SELECT id, IF(name <> '', name, '[sin título]') AS name
             FROM $this->dbTable
             WHERE id_padre='$id_padre' $this->sqlWhere $sqlWhere_L3
             ORDER BY name";
        // print_r2($sqlQ);
        return Db_mysql::getList($sqlQ);
    }
    //-----------------------------------------------------------------
    public function getHtmOpen($id, $name, $nivel, $count, $tieneSubc)
    {
        global $seccCtrl, $objectsStatus;

        //$strDebug = " ($id)($nivel)($count)";
        $strTab     = $this->getStrTab($nivel);
        $classTupla = 'tuplaL' . $nivel;

        // Selected ---
        if ($id == $objectsStatus->getRowId($this->id)) {
            $classTupla .= ' tuplaSelected';
        }

        // Formatear name ---
        $title = '';
        if (strlen($name) > 36) {
            $title  = 'title="' . $name . '"';
            $name = substr($name, 0, 36) . '...';
        }

        // Buttons ---
        $listBt = $this->getButtons($id, $nivel, $name, $tieneSubc);

        //---
        $classHover = ($tieneSubc)? ' hover' : '';

        $htmCateg = "\n$strTab<li>" .
                        $listBt['check'] .
                        '<span class="btOptions">'.
                          $listBt['update'] . ' ' .
                          $listBt['new'] . ' ' .
                          $listBt['del'] .
                        '</span>'.

                        ' <span id="cat_'.$id.'" class="tuplaL '.$classTupla.$classHover.'" '.$title.'>'.
                            $listBt['name'].
                        '</span> '.$listBt['detalle'];

        if ($tieneSubc) {
            $htmCateg .= "\n$strTab<ul>";
        }

        return $htmCateg;
    }
    //-----------------------------------------------------------------
    public function getHtmCierre($id, $nivel, $count, $tieneSubc)
    {
        //$strDebug = "($id)($nivel)($count)";
        $strTab = $this->getStrTab($nivel);

        $htmCateg = '';
        if ($tieneSubc) {
            $htmCateg .= "\n$strTab</ul>\n$strTab";
        }
        $htmCateg .= "</li>";

        return $htmCateg;
    }
    //-----------------------------------------------------------------
    //-----------------------------------------------------------------
    public function getStrTab($nivel)
    {
        $strTab = ' ';
        for ($c = 0; $c <= $nivel; $c++) {
            $strTab .= ' ';
        }

        return $strTab;
    }
    //-----------------------------------------------------------------
    public function getButtons($id, $nivel, $name, $tieneSubc)
    {
        $listBt = array(
            'update'=>'',
            'new'  =>'',
            'del'  =>'',
            'detalle'=>'',
            'check'=>'',
        );
        $listBt['name'] = $name;

        // Update -------
        if ($this->opUpdate == true) {
            $listBt['update'] = '<span class="wtree_onUpdate" data-id="'.$id.'" data-level="'.$nivel.'">'.
                                    '<i class="fas fa-pen-square" aria-hidden="true"></i>'.
                                 '</span>';
        }

        // New ----------
        if ($this->opNewSub == true) {
            if ($nivel < $this->niveles) {
                $listBt['new'] = '<span class="wtree_onNewSub" data-id="'.$id.'" data-level="'.$nivel.'">'.
                                    '<i class="fas fa-plus-square" aria-hidden="true"></i>'.
                                 '</span>';
            }
        }

        // Delete -------
        if ($this->opDelete == true && !$tieneSubc) {
            $listBt['del'] = '<span class="wtree_onDel red" data-id="'.$id.'" data-level="'.$nivel.'">'.
                                 '<i class="fas fa-trash-alt" aria-hidden="true"></i>'.
                             '</span>';
        }

        // Detail -------
        if ($nivel >= $this->haveElementsOnLevel) {
            if (!$tieneSubc) {
                // $link = CrudUrl::get(CRUD_LIST_DETAIL, $this->id, $id, '', 'level=' . $nivel);
                // $listBt['name'] = '<a href="' . $link . '">' . $listBt['name'] . '</a>';
                $listBt['name'] = $listBt['name'];
            } else {
                $listBt['detalle'] = '<span class="wtree_onDetalle red" data-id="'.$id.'" data-level="'.$nivel.'">'.
                                        '<i class="fas fa-arrow-right" aria-hidden="true"></i>'.
                                     '</span>';
            }
        }

        // Checkbox -----
        if ($this->opCheckBox == true && $nivel >= $this->check_onLevel) {
            $checked = '';
            if (in_array($id, $this->check_listSelected)) {
                $checked = 'checked';
            }
            $listBt['check'] = '<input type="checkbox" class="check" name="'.$this->check_name.'[]" value="'.$id.'" '.$checked.'>';
        }

        return $listBt;
    }
    //-----------------------------------------------------------------
}
