<?
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 * Obligatorios:
 *   DB table: 'categorias' -> 'id_padre'
 *
 * Eventos:
 *   'editNew'     & 'ROW_PADRE_ID'
 *   'editUpdate'  & 'ROW_ID'
 *   'list_delete'      & 'ROW_ID'
 *   'list_rowSelected' & 'ROW_ID'
 */

namespace angelrove\membrillo2\WObjects\WTree;

use angelrove\utils\Db_mysql;
use angelrove\utils\CssJsLoad;
use angelrove\utils\Vendor;


class WTree
{
  private $dbTable = 'categorias';

  private $id;
  private $id_levels = array();

  private $wTreeData;

  private $width;
  private $height;

  private $title = 'Categorías';

  private $niveles;
  private $id_selected;
  private $haveElementsOnAnyCateg = false;
  private $alwaysExpand = false;

  private $count_nivel;

  private $opNew    = true;
  private $opUpdate = false;
  private $opNewSub = true;
  private $opDelete = true;

  //-----------------------------------------------------------------
  /**
   * Params:
   *   $idComponent: id del control
   *   $width:       anchura
   *   $height:      altura
   *   $niveles:     nivel de profundidad del árbol
   */
  function __construct($idComponent, $dbTable, $width, $niveles=3)
  {
    CssJsLoad::set(__DIR__.'/styles.css');
    CssJsLoad::set(__DIR__.'/libs.js');

    $this->id      = $idComponent;
    $this->dbTable = $dbTable;
    $this->width   = $width.'px';
    $this->niveles = $niveles;

    // Datos selected
    global $seccCtrl;
    // print_r2($seccCtrl);

    $this->id_desplegado = $objectsStatus->getDato($this->id, 'ROW_ID_DESPLEGADO');
    $this->ROW_ID        = $objectsStatus->getRowId($this->id);
    $this->id_nivel_selected = $objectsStatus->getDato($this->id, 'nivel');
  }
  //-----------------------------------------------------------------
  public function setId_level($level, $id) {
    $this->id_levels[$level] = $id;
  }
  //-----------------------------------------------------------------
  public function setWhere($sqlWhere) {
    $this->sqlWhere = 'AND '.$sqlWhere;
  }
  //-----------------------------------------------------------------
  public function setWtreeData($wTreeData) {
    $this->wTreeData = $wTreeData;
  }
  //-----------------------------------------------------------------
  public function setWidth($width) {
    $this->width = $width.'px';
  }
  //-----------------------------------------------------------------
  public function setTitle($title) {
    $this->title = $title;
  }
  //-----------------------------------------------------------------
  public function haveElementsOnAnyCateg($flag) {
    $this->haveElementsOnAnyCateg = $flag;
  }
  //-----------------------------------------------------------------
  public function alwaysExpand($flag) {
    $this->alwaysExpand = $flag;
  }
  //-----------------------------------------------------------------
  // Actions
  //-----------------------------------------------------------------
  public function showOpNew($value) {
    $this->opNew = $value;
  }
  //-----------------------------------------------------------------
  public function showOpUpdate($flag) {
    $this->opUpdate = $flag;
  }
  //-----------------------------------------------------------------
  public function showOpDelete($value) {
    $this->opDelete = $value;
  }
  //-----------------------------------------------------------------
  public function showOpNewSub($value) {
    $this->opNewSub = $value;
  }
  //-----------------------------------------------------------------
  //-----------------------------------------------------------------
  public function getHtm() {
    global $LOCAL;

    // Tupla seleccionada
    $this->id_selected = $this->getSelected();

    // HTM de categorías
    $strCategorias = $this->get_category_tree(0, '');

    // Button "Nuevo"
    $href = "'".'?CONTROL='.$this->id.'&EVENT=editNew&ROW_PADRE_ID=0&nivel=1'."'";
    $strNuevo = '<input type="button" class="bt_new" onclick="location.href='.$href.'" value="'.$LOCAL['WList_new'].'">';
    if($this->opNew == false) {
       $strNuevo = '';
    }

    // Out
    $id_tree = 'WTree_'.$this->id;

    $strTree = <<<EOD

<!-- WTree -->
<div id="$id_tree" class="WTree" style="width:$this->width">
  <table class="WTree_cabecera" width="100%" cellpadding="0" cellspacing="0">
    <tr>
     <td class="cabecera_title">$this->title</td>
     <td class="cabecera_title" align="center" style="width:74px">$strNuevo</td>
    </tr>
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
  private function get_category_tree($id_padre, $strTab, $id_top='')
  {
    $strTree = '';
    if(!$this->count_nivel) {
       $this->count_nivel = 1;
    }

    $listCategorias = $this->getCategorias($this->count_nivel, $id_padre);
    $c = 0; $size = count($listCategorias);

    foreach($listCategorias as $id => $categ) {
      $c++;

      $tieneSubc    = $this->tieneSubc($this->count_nivel, $id);
      $isHijo       = ($id_padre > 0);
      $hayMasCateg  = ($size > $c)? true : false;
      $isDesplegado = ($id == $this->id_desplegado)? true : false;

      if($this->alwaysExpand === true) {
         $isDesplegado = true;
      }

      // Selected --------
       $tupla_selected = '';
       if($id == $this->id_selected && $this->count_nivel == $this->id_nivel_selected) {
          $tupla_selected = 'tuplaSelected';
       }

      // Imagen árbol ----
       $strImgTree = $this->getImgArbol($isHijo, $tieneSubc, $isDesplegado, $hayMasCateg);

      // Capas -----------
       if(!$isHijo) {
          $id_top = $id;
       }

       $capas = $this->getCapas($id, $isHijo, $strTree, $isDesplegado, $tieneSubc, $hayMasCateg);

       // onSelectRow() ---------
       $strOnClickRow = '';
       if($this->alwaysExpand === false && $tieneSubc) {
          $strOnClickRow = "WTree_onSelectRow('$id'); return false;";
       }
       if($this->haveElementsOnAnyCateg || ($this->count_nivel == $this->niveles)) {
          $strOnClickRow = "WTree_onSelectRow_reload('?CONTROL=$this->id&EVENT=list_rowSelected&ROW_ID=$id&ROW_ID_DESPLEGADO=$id_top&nivel=$this->count_nivel')";
       }

      // Botonera ------
       $bt_newSub = $this->getBt_newSub($id);
       $bt_delete = $this->getBt_delete($id, $tieneSubc);
       $bt_edit   = $this->getBt_edit($id, $id_top);

      // OUT -----------
       $categoria = $categ['nombre'];

       $htmBotones = '';
       if($bt_edit || $bt_newSub || $bt_delete) {
          $htmBotones = '<td class="columnOp">'.$bt_edit.$bt_newSub.$bt_delete.'</td>';
       }

       // $categoria = "isHijo: $isHijo, tieneSubc: $tieneSubc, hayMasCateg:$hayMasCateg, isDesplegado: $isDesplegado";

// $strTree .= "\n\n<!-- CAT: $id_padre ------------------------ -->\n".'<div id="cat_'.$id_padre.'" style="display:">';

       $strTree .= <<<EOD
$capas[fin]
  <!-- TUPLA -->
  <table id="WTree_row_$id" class="row_$id tupla" param_ctrl="$this->id" param_nivel="$this->count_nivel" param_id_top="$id_top"><tr>
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
       if(($this->count_nivel == $this->niveles) || !$tieneSubc) {
          continue;
       }

      // Contador de niveles
       $this->count_nivel++;

      // Subcategorías (recursividad)
       $strTree .= $this->get_category_tree($id, $strTab.'<div class="tab"></div>', $id_top);
    }//END foreach

// $strTree .= "</div>\n<!-- /CAT: $id_padre --------------------------- -->\n";

    $this->count_nivel--;

    return $strTree;
  }
  //-----------------------------------------------------------------
  // Get data
  //-----------------------------------------------------------------
  private function getCategorias($count_nivel, $id_padre)
  {
    if($this->wTreeData) {
       return $this->wTreeData->getCategorias($count_nivel, $id_padre);
    }
    else {
       $sqlQ = "SELECT id,
                       IF(nombre <> '', nombre, '[sin título]') AS nombre
                FROM $this->dbTable
                WHERE id_padre='$id_padre' $this->sqlWhere
                ORDER BY nombre";
       return Db_mysql::getList($sqlQ);
    }
  }
  //-----------------------------------------------------------------
  private function tieneSubc($count_nivel, $id)
  {
    $tieneSubc = '';

    if($this->wTreeData) {
       $tieneSubc = $this->wTreeData->tieneSubc($count_nivel, $id);
    }
    else {
       $sqlQ = "SELECT id FROM $this->dbTable WHERE id_padre='$id' $this->sqlWhere";
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

     if(!$isHijo) {
        $strIdInicio = "\n<!-- --------------------------- -->\n".
                       '<div id="cat_'.$id.'" style="display:none">';
        // Desplegar -------
        if($isDesplegado) {
           $strIdInicio = "\n<!-- --------------------------- -->\n".
                          '<div id="cat_'.$id.'" style="display:block">';
        }
     }

     if($strTree && !$isHijo) {
        $strIdFin = "</div>".
                    "\n<!-- --------------------------- -->\n";
     }

     return array(
       'inicio'=> $strIdInicio,
       'fin'   => $strIdFin,
      );
  }
  //-----------------------------------------------------------------
  private function getImgArbol($isHijo, $tieneSubc, $isDesplegado, $hayMasCateg)
  {
    $tipos = array(
       'contraido' => 'plus fa-lg',
       'desplegado'=> 'minus fa-lg',
       'empty'     => 'plus tab_0 fa-lg',
       'fin'       => 'caret-right',
    );

    $strImgTree = '';

    if($isHijo) {
       $tipo = 'fin';
       //$tipo = 'fin';
    }
    else {
       if($tieneSubc) {
          $tipo = ($isDesplegado)? 'desplegado' : 'contraido';
       }
       else {
          $tipo = 'empty';
       }
    }

    //----------------
    $strImgTree = '<i class="fa fa-'.$tipos[$tipo].' fa-fw"></i>';
    return $strImgTree;
  }
  //-----------------------------------------------------------------
  // Buttons
  //-----------------------------------------------------------------
  private function getBt_edit($id, $id_desplegado)
  {
    $bt = '';
    $event = 'editUpdate';

    if($this->opUpdate)
    {
      $CONTROL = ($this->id_levels[$this->count_nivel])? $this->id_levels[$this->count_nivel] : $this->id;

      $bt = '<a class="op_update level_'.$this->count_nivel.'" href="?CONTROL='.$CONTROL.'&EVENT='.$event.'&ROW_ID='.$id.'&ROW_ID_DESPLEGADO='.$id_desplegado.'&nivel='.$this->count_nivel.'">'.
              '<i class="fa fa-pencil-square-o fa-lg"></i>'.
            '</a>';
    }

    return $bt;
  }
  //-----------------------------------------------------------------
  private function getBt_newSub($id)
  {
    if($this->opNewSub !== true) {
       return '';
    }

    $bt = '';

    if($this->count_nivel < $this->niveles)
    {
       $event   = 'editNew';
       $CONTROL = ($this->id_levels[$this->count_nivel+1])? $this->id_levels[$this->count_nivel+1] : $this->id;

       $href = '?CONTROL='.$CONTROL.
               '&EVENT='.$event.
               '&nivel='.($this->count_nivel+1).
               '&ROW_PADRE_ID='.$id;

       $bt = '<a class="op_newSub" href="'.$href.'"><i class="fa fa-plus-circle fa-lg" title="Nueva subcategoría"></i></a>';
    }

    return $bt;
  }
  //-----------------------------------------------------------------
  private function getBt_delete($id, $isPadre)
  {
    $bt = '';

    if($this->opDelete == true && !$isPadre)
    {
       $CONTROL = ($this->id_levels[$this->count_nivel])? $this->id_levels[$this->count_nivel] : $this->id;

       $bt = '<a class="op_delete" param_id="'.$id.'" param_ctrl="'.$CONTROL.'" param_nivel="'.$this->count_nivel.'" href="javascript: return false">'.
                '<i class="fa fa-trash-o fa-lg"></i>'.
             '</a>';
    }

    return $bt;
  }
  //-----------------------------------------------------------------
}
