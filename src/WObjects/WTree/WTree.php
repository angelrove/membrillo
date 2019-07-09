<?php
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 * Obligatorios:
 *   DB table: 'categorias' -> 'id_padre'
 *
 * Eventos:
 *   'editNew'     & 'ROW_PARENT_ID'
 *   'editUpdate'  & 'ROW_ID'
 */

namespace angelrove\membrillo\WObjects\WTree;

use angelrove\membrillo\CrudUrl;
use angelrove\utils\CssJsLoad;
use angelrove\utils\Db_mysql;
use angelrove\membrillo\WObjectsStatus\Event;

class WTree
{
    private $dbTable = 'categorias';

    private $id;
    private $id_levels = array();

    private $wTreeData;

    private $width = '';
    private $height;

    private $title = 'Categorías';

    private $niveles = 3;
    private $id_selected;
    private $haveElementsOnAnyCateg = false;
    private $alwaysExpand           = false;

    private $count_nivel;

    private $opNew    = true;
    private $opUpdate = false;
    private $opNewSub = true;
    private $opDelete = true;

    private $sqlWhere;
    private $searcher = false;
    private $f_search;

    //-----------------------------------------------------------------
    public function __construct($idComponent, $dbTable)
    {
        CssJsLoad::set(__DIR__ . '/styles.css');
        CssJsLoad::set(__DIR__ . '/libs.js');

        $this->id      = $idComponent;
        $this->dbTable = $dbTable;

        // Datos selected
        global $objectsStatus;

        $this->id_desplegado     = $objectsStatus->getDato($this->id, 'ID_UP');
        $this->ROW_ID            = $objectsStatus->getRowId($this->id);
        $this->id_nivel_selected = $objectsStatus->getDato($this->id, 'nivel');
    }
    //-----------------------------------------------------------------
    public function setLevels($levels)
    {
        $this->niveles = $levels;
    }
    //-----------------------------------------------------------------
    public function setWidth($width)
    {
        $this->width = $width;
    }
    //-----------------------------------------------------------------
    public function setTitle($title)
    {
        $this->title = $title;
    }
    //-----------------------------------------------------------------
    public function setId_level($level, $id)
    {
        $this->id_levels[$level] = $id;
    }
    //-----------------------------------------------------------------
    public function setWhere($sqlWhere)
    {
        $this->sqlWhere = 'AND ' . $sqlWhere;
    }
    //-----------------------------------------------------------------
    public function haveElementsOnAnyCateg($flag)
    {
        $this->haveElementsOnAnyCateg = $flag;
    }
    //-----------------------------------------------------------------
    public function alwaysExpand($flag)
    {
        $this->alwaysExpand = $flag;
    }
    //-----------------------------------------------------------------
    // Interfaces
    //-----------------------------------------------------------------
    public function setWtreeData(iWTreeData $iWTreeData)
    {
        $this->wTreeData = $iWTreeData;
    }
    //-----------------------------------------------------------------
    // Actions
    //-----------------------------------------------------------------
    public function showOpNew($value)
    {
        $this->opNew = $value;
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
    public function showOpNewSub($value)
    {
        $this->opNewSub = $value;
    }
    //-----------------------------------------------------------------
    public function showSearcher()
    {
        $this->searcher = true;
    }
    //-----------------------------------------------------------------
    private function getSearcher()
    {
        global $objectsStatus;
        $filtros = $objectsStatus->getDatos($this->id);

        return \angelrove\membrillo\WObjects\WList\WList::searcher_complet($this->id, @$filtros['f_text']);
    }
    //-----------------------------------------------------------------
    //-----------------------------------------------------------------
    public function get()
    {
        // Searcher -------
        global $objectsStatus;
        $filtros = $objectsStatus->getDatos($this->id);
        if (isset($filtros['f_text']) && $filtros['f_text']) {
            $this->f_search = $filtros['f_text'];
        }

        // Tupla seleccionada
        $this->id_selected = $this->getSelected();

        // HTML de categorías
        $strCategorias = $this->get_category_tree(0, '');

        // Button "New..."
        $href     = "'" . CrudUrl::get(CRUD_EDIT_NEW, $this->id, '', '', 'ROW_PARENT_ID=0&nivel=1') . "'";
        $strNuevo = '<button type="button" class="btn btn-xs btn-primary" onclick="location.href=' . $href . '">New...</button>';

        if ($this->opNew == false) {
            $strNuevo = '';
        }

        if ($this->width) {
            $this->width = 'min-width:' . $this->width . 'px;';
        }

        // Searcher ------
        $htmSearcher = '';
        if ($this->searcher) {
            $htmSearcher = $this->getSearcher();
        }

        // Out ---------
        $id_tree = 'WTree_' . $this->id;

        $strTree = <<<EOD

<!-- WTree -->
<div id="$id_tree" class="WTree" style="$this->width">

    $htmSearcher

    <table class="WTree_cabecera">
        <tr><th class="title">$this->title</th><th>$strNuevo</th></tr>
    </table>
    <div class="WTree_tuplas">
        $strCategorias
    </div>
</div>
<!-- /WTree -->

EOD;

        return $strTree;
    }
    //-----------------------------------------------------------------
    // PRIVATE
    //-----------------------------------------------------------------
    // Función recursiva
    private function get_category_tree($id_padre, $strTab, $id_top = '')
    {
        $strTree = '';
        if (!$this->count_nivel) {
            $this->count_nivel = 1;
        }

        $listCategorias = $this->getCategorias($this->count_nivel, $id_padre);
        $c              = 0;
        $size           = count($listCategorias);

        foreach ($listCategorias as $id => $categ) {
            $c++;

            $tieneSubc    = $this->tieneSubc($this->count_nivel, $id);
            $isHijo       = ($id_padre > 0);
            $hayMasCateg  = ($size > $c) ? true : false;
            $isDesplegado = ($id == $this->id_desplegado) ? true : false;

            if ($this->alwaysExpand === true) {
                $isDesplegado = true;
            }

            // Selected --------
            $tupla_selected = '';
            if ($id == $this->id_selected && $this->count_nivel == $this->id_nivel_selected) {
                $tupla_selected = 'tuplaSelected';
            }

            // Searcher ------
            $class_searched = '';
            if ($this->f_search) {
                if ($categ['name'] == $this->f_search) {
                    $class_searched = 'searched';
                    // $isDesplegado = true;
                }
            }

            // Imagen árbol ----
            $strImgTree = $this->getImgArbol($isHijo, $tieneSubc, $isDesplegado, $hayMasCateg);

            // Capas -----------
            if (!$isHijo) {
                $id_top = $id;
            }

            $capas = $this->getCapas($id, $isHijo, $strTree, $isDesplegado, $tieneSubc, $hayMasCateg);

            // onSelectRow() ---------
            $strOnClickRow = '';
            if ($this->alwaysExpand === false && $tieneSubc) {
                $strOnClickRow = "WTree_onSelectRow('$id'); return false;";
            }
            if ($this->haveElementsOnAnyCateg || ($this->count_nivel == $this->niveles)) {
                $strOnClickRow = "WTree_onSelectRow_reload('/$_GET[secc]/crd/$this->id/" . CRUD_LIST_DETAIL . "/$id/?ID_UP=$id_top&nivel=$this->count_nivel')";
            }

            // Botonera ------
            $bt_newSub = $this->getBt_newSub($id, $categ);
            $bt_delete = $this->getBt_delete($id, $tieneSubc);
            $bt_edit   = $this->getBt_edit($id, $id_top);

            $htmBotones = '';
            if ($bt_edit || $bt_newSub || $bt_delete) {
                $htmBotones = '<td class="columnOp">' . $bt_edit . $bt_newSub . $bt_delete . '</td>';
            }

            $categoria = $categ['name'];
            // $categoria = "isHijo: $isHijo, tieneSubc: $tieneSubc, hayMasCateg:$hayMasCateg, isDesplegado: $isDesplegado";

            // $strTree .= "\n\n<!-- CAT: $id_padre ------------------------ -->\n".'<div id="cat_'.$id_padre.'" style="display:">';

            $strTree .= <<<EOD
$capas[fin]
  <!-- TUPLA -->
  <table id="WTree_row_$id" class="row_$id tupla $class_searched" param_ctrl="$this->id" param_nivel="$this->count_nivel" param_id_top="$id_top"><tr>
      <td class="title on_row $tupla_selected" onclick="$strOnClickRow">
          $strTab $strImgTree $categoria
      </td>
      $htmBotones
  </tr></table>
  <!-- /TUPLA -->
$capas[inicio]
EOD;

//        $strTree .= <<<EOD
            //   <!-- TUPLA -->
            //   <table id="WTree_row_$id" class="row_$id tupla" param_ctrl="$this->id" param_nivel="$this->count_nivel" param_id_top="$id_top"><tr>
            //     <td class="title on_row $tupla_selected" onclick="$strOnClickRow">
            //       $strTab $strImgTree $categoria
            //     </td>
            //   </tr></table>
            //   <!-- /TUPLA -->
            // EOD;

            // No continuar
            if (($this->count_nivel == $this->niveles) || !$tieneSubc) {
                continue;
            }

            // Contador de niveles
            $this->count_nivel++;

            // Subcategorías (recursividad)
            $strTree .= $this->get_category_tree($id, $strTab . '<div class="tab"></div>', $id_top);
        } //END foreach

// $strTree .= "</div>\n<!-- /CAT: $id_padre --------------------------- -->\n";

        $this->count_nivel--;

        return $strTree;
    }
    //-----------------------------------------------------------------
    // Get data
    //-----------------------------------------------------------------
    private function getCategorias($count_nivel, $id_padre)
    {
        if ($this->wTreeData) {
            return $this->wTreeData->getCategorias($count_nivel, $id_padre);
        }
        else {
            $xx = "";

            $sqlQ = "SELECT id,
                       IF(name <> '', name, '[sin título]') AS name
                FROM $this->dbTable
                WHERE id_padre='$id_padre' $this->sqlWhere
                ORDER BY name";
            // print_r2($sqlQ);
            return Db_mysql::getList($sqlQ);
        }
    }
    //-----------------------------------------------------------------
    private function tieneSubc($count_nivel, $id)
    {
        $tieneSubc = '';

        if ($this->wTreeData) {
            $tieneSubc = $this->wTreeData->tieneSubc($count_nivel, $id);
        } else {
            $sqlQ      = "SELECT id FROM $this->dbTable WHERE id_padre='$id' $this->sqlWhere";
            $tieneSubc = Db_mysql::getValue($sqlQ);
        }

        return $tieneSubc;
    }
    //-----------------------------------------------------------------
    private function getSelected()
    {
        return $this->ROW_ID;
        /*
    $listSelected = array();

    $id = $objectsStatus->getRowId($this->id);
    while($id) {
    $listSelected[] = $id;
    $id = $this->wTreeData->getPadre($id);
    }

    return $listSelected;
     */
    }
    //-----------------------------------------------------------------
    private function getCapas($id, $isHijo, $strTree, $isDesplegado, $tieneSubc, $hayMasCateg)
    {
        $strIdInicio = '';
        $strIdFin    = '';

        if (!$isHijo) {
            $strIdInicio = "\n<!-- --------------------------- -->\n" .
                '<div id="cat_' . $id . '" style="display:none">';
            // Desplegar -------
            if ($isDesplegado) {
                $strIdInicio = "\n<!-- --------------------------- -->\n" .
                    '<div id="cat_' . $id . '" style="display:block">';
            }
        }

        if ($strTree && !$isHijo) {
            $strIdFin = "</div>" .
                "\n<!-- --------------------------- -->\n";
        }

        return array(
            'inicio' => $strIdInicio,
            'fin'    => $strIdFin,
        );
    }
    //-----------------------------------------------------------------
    private function getImgArbol($isHijo, $tieneSubc, $isDesplegado, $hayMasCateg)
    {
        $tipos = array(
            'contraido'  => 'plus',
            'desplegado' => 'minus',
            'empty'      => 'plus tab_0',
            'fin'        => 'caret-right',
        );

        $strImgTree = '';

        if ($isHijo) {
            $tipo = 'fin';
            //$tipo = 'fin';
        } else {
            if ($tieneSubc) {
                $tipo = ($isDesplegado) ? 'desplegado' : 'contraido';
            } else {
                $tipo = 'empty';
            }
        }

        //----------------
        $strImgTree = '<i class="fas fa-' . $tipos[$tipo] . ' fa-fw"></i>';
        return $strImgTree;
    }
    //-----------------------------------------------------------------
    // Buttons
    //-----------------------------------------------------------------
    private function getBt_edit($id, $id_desplegado)
    {
        $bt    = '';
        $event = CRUD_EDIT_UPDATE;

        if ($this->opUpdate) {
            $CONTROL = (isset($this->id_levels[$this->count_nivel])) ? $this->id_levels[$this->count_nivel] : $this->id;
            $href    = CrudUrl::get($event, $CONTROL, $id, '', 'ID_UP=' . $id_desplegado . '&nivel=' . $this->count_nivel);

            $bt = '<a class="op_update level_' . $this->count_nivel . '" href="' . $href . '">'.
                     '<i class="far fa-edit fa-lg"></i>'.
                  '</a>';
        }

        return $bt;
    }
    //-----------------------------------------------------------------
    private function getBt_newSub($id, array $datos)
    {
        if ($this->opNewSub !== true) {
            return '';
        }

        if ($this->wTreeData) {
            if (!$tieneSubc = $this->wTreeData->show_newSub($datos)) {
                return '';
            }
        }

        //----------
        $bt = '';

        if ($this->count_nivel < $this->niveles) {
            $CONTROL = (isset($this->id_levels[$this->count_nivel + 1])) ? $this->id_levels[$this->count_nivel + 1] : $this->id;
            $href    = CrudUrl::get(CRUD_EDIT_NEW, $CONTROL, '', '', 'nivel=' . ($this->count_nivel + 1) . '&ROW_PARENT_ID=' . $id);
            $bt      = '<a class="op_newSub" href="' . $href . '"><i class="fas fa-plus-circle fa-lg" title="Nueva subcategoría"></i></a>';
        }

        return $bt;
    }
    //-----------------------------------------------------------------
    private function getBt_delete($id, $isPadre)
    {
        $bt = '';

        if ($this->opDelete == true && !$isPadre) {
            $CONTROL = (isset($this->id_levels[$this->count_nivel])) ? $this->id_levels[$this->count_nivel] : $this->id;

            $bt = '<a class="op_delete" ' .
            'param_id="' . $id . '" ' .
            'param_ctrl="' . $CONTROL . '" ' .
            'param_nivel="' . $this->count_nivel . '" ' .
                'href="javascript: return false"><i class="fas fa-trash-alt fa-lg"></i></a>';
        }

        return $bt;
    }
    //-----------------------------------------------------------------
}
