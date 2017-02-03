<?
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 */

namespace angelrove\membrillo2\WObjects\WTree2;

use angelrove\utils\CssJsLoad;


/*
 * Obligatorios:
 *   DB table: 'categorias' -> 'id_padre'
 *
 * Eventos:
 *   'editNew'     & 'ROW_PADRE_ID'
 *   'editUpdate'  & 'ROW_ID'
 *   'list_delete'      & 'ROW_ID'
 *   'list_rowSelected' & 'ROW_ID'
 */

class WTree2
{
  private $id;
  private $dbTable = 'categorias';
  private $title   = 'Categorías';
  private $niveles;
  private $width;
  private $height;

  private $opNew    = true;
  private $opUpdate = false;
  private $opNewSub = true;
  private $opDelete = true;

  private $imgNuevo   = 'bt_nuevo_2.png';
  private $imgUpdate  = 'bt_update.gif';
  private $imgDelete  = 'delete.png';
  private $imgDetalle = 'list_detalle.gif';

  private $marcas = array();  // Busquedas
  private $statusCiclo = array();  // Status de la generación HTML

  private $sqlWhere    = '';
  private $sqlWhere_L3 = '';

  //-----------------------------------------------------------------
  function __construct($idComponent, $dbTable, $width, $niveles=3)
  {
    CssJsLoad::set(__DIR__.'/styles.css');
    CssJsLoad::set(__DIR__.'/libs.js');

    $this->id      = $idComponent;
    $this->dbTable = $dbTable;
    $this->width   = $width.'px';
    $this->niveles = $niveles;
  }
  //-----------------------------------------------------------------
  public function setTitle($title) {
    $this->title = $title;
  }
  //-----------------------------------------------------------------
  public function haveElementsOnLevel($level) {
    $this->haveElementsOnLevel = $level;
  }
  //-----------------------------------------------------------------
  public function showOpNew($value) {
    $this->opNew = $value;
  }
  //-----------------------------------------------------------------
  public function showOpNewSub($value) {
    $this->opNewSub = $value;
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
  public function showCheckBox($name, $onLevel, $listSelected) {
    $this->opCheckBox         = true;
    $this->check_name         = $name;
    $this->check_onLevel      = $onLevel;
    $this->check_listSelected = $listSelected;
  }
  //-----------------------------------------------------------------
  public function setWhere($sqlWhere) {
    $this->sqlWhere = 'AND '.$sqlWhere;
  }
  //-----------------------------------------------------------------
  public function setWhere_L3($sqlWhere) {
    $this->sqlWhere_L3 = 'AND '.$sqlWhere;
  }
  //---------------------------------------------------------------------
  public function setReadOnly($isReadonly) {
    $this->isReadonly = $isReadonly;
    $this->opDelete = false;
    $this->opNew    = false;
    $this->opNewSub = false;
  }
  //-----------------------------------------------------------------
  public function setSearchWord($searchWord) {
    $this->searchWord = $searchWord;
  }
  //-----------------------------------------------------------------
  //-----------------------------------------------------------------
  public function getHtm()
  {
    // HTM de categorías
    $strCategorias = $this->get_category_tree();

    // Button "Nuevo"
    $strNuevo = '<a class="btNuevo" href="?CONTROL='.$this->id.'&EVENT=editNew&nivel=1" title="Nueva categoría">New...</a>';
    if($this->opNew == false) {
       $strNuevo = '';
    }

    // Marcar búsquedas
    $strMarcar = '';
    foreach($this->marcas as $id_cat => $xx) {
       $strMarcar .= '$("#cat_'.$id_cat.'").addClass("selected");';
    }

    // Out
    $strTree = <<<EOD
  <style>
   .selected { background:#9F9; border:1px solid #3C3; }
   .selected a { background:#9F9; }
  </style>

 <!-- TreeView -->
 <script>
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
    $strMarcar
 });

 function wtree2_onUpdate(id, nivel) {
   location.href = '?CONTROL=$this->id&EVENT=editUpdate&nivel='+nivel+'&ROW_ID='+id;
 }
 function wtree2_onNewSub(id, nivel) {
   location.href = '?CONTROL=$this->id&EVENT=editNew&ROW_PADRE_ID='+id+'&nivel='+(nivel+1);
 }
 function wtree2_onDel(id, nivel) {
   if(confirm('¿Está seguro...?')) {
      location.href = '?CONTROL=$this->id&EVENT=list_delete&OPER=delete&nivel='+nivel+'&ROW_ID='+id;
   }
 }
 function wtree2_onDetalle(id, nivel) {
   location.href = '?CONTROL=$this->id&EVENT=list_rowSelected&nivel='+nivel+'&ROW_ID='+id;
 }
 </script>
 <!-- /TreeView -->

 <div id="sidetree" style="width:$this->width">
  <!-- Cabecera -->
  <table class="WTree_cabecera" width="100%" cellpadding="0" cellspacing="0" border="0">
   <tr>
    <td class="sidetreecontrol">&nbsp;<span id="sidetreecontrol"><a href="?#">&nbsp;^ </a>&nbsp;<a href="?#">&nbsp;V&nbsp;</a></span></td>
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

    return $strTree;
  }
  //-----------------------------------------------------------------
  // PRIVATE
  //-----------------------------------------------------------------
  private function get_category_tree($id_padre=0, $nivel=0) {
    $strTree = '';
    $nivel++;
    $count = 0;

    //$sqlWhere_L3 = ($nivel == 3)? $this->sqlWhere_L3 : '';
    $listCategorias = $this->getCategorias($id_padre, $nivel);

    foreach($listCategorias as $id => $categ)
    {
       $count++;

       $tieneSubc = Db_mysql::getValue("SELECT id FROM $this->dbTable WHERE id_padre='$id' $this->sqlWhere");
       $strTree .= $this->getHtmOpen($id, $categ['nombre'], $nivel, $count, $tieneSubc);
       $strTree .= $this->get_category_tree($id, $nivel);
       $strTree .= $this->getHtmCierre($id, $nivel, $count, $tieneSubc);

       // Marcar búsqueda
       if($this->searchWord) {
          if(stripos($categ['nombre'], $this->searchWord) !== false) {
             $this->marcas[$id] = true;
          }
          if($this->marcas[$id] && $id_padre != 0) { // padres
             $this->marcas[$id_padre] = true;
          }
       }
    }
    $nivel--;

    return $strTree;
  }
  //-----------------------------------------------------------------
  //-----------------------------------------------------------------
  private function getCategorias($id_padre, $nivel=0)
  {
    $sqlWhere_L3 = ($nivel == 3)? $this->sqlWhere_L3 : '';

    $sqlQ = "SELECT id, IF(nombre <> '', nombre, '[sin título]') AS nombre
             FROM $this->dbTable
             WHERE id_padre='$id_padre' $this->sqlWhere $sqlWhere_L3
             ORDER BY nombre";
    //print_r2($sqlQ);
    return Db_mysql::getList($sqlQ);
  }
  //-----------------------------------------------------------------
  function getHtmOpen($id, $nombre, $nivel, $count, $tieneSubc)
  {
    global $seccCtrl;

    //$strDebug = " ($id)($nivel)($count)";
    $strTab = $this->getStrTab($nivel);
    $classTupla = 'tuplaL'.$nivel;
    if($id == $objectsStatus->getRowId($this->id)) {
       $classTupla .= ' tuplaSelected';
    }

    // Formatear nombre
    $title = '';
    if(strlen($nombre) > 36) {
       $title = 'title="'.$nombre.'"';
       $nombre = substr($nombre, 0, 36).'...';
    }

    // Buttons
    $listBt = $this->getButtons($id, $nivel, $nombre, $tieneSubc);

    //---
    $htmCateg = "\n$strTab<li>".$listBt['check'].$listBt['update'].' '.$listBt['new'].' '.$listBt['del'].' <span id="cat_'.$id.'" class="tuplaL '.$classTupla.'" '.$title.'>'.$listBt['nombre'].'</span> '.$listBt['detalle'];
    if($tieneSubc) {
       $htmCateg .= "\n$strTab<ul>";
    }

    return $htmCateg;
  }
  //-----------------------------------------------------------------
  function getHtmCierre($id, $nivel, $count, $tieneSubc)
  {
    //$strDebug = "($id)($nivel)($count)";
    $strTab = $this->getStrTab($nivel);

    $htmCateg = '';
    if($tieneSubc) {
       $htmCateg .= "\n$strTab</ul>\n$strTab";
    }
    $htmCateg .= "</li>";

    return $htmCateg;
  }
  //-----------------------------------------------------------------
  //-----------------------------------------------------------------
  function getStrTab($nivel)
  {
    $strTab = ' ';
    for($c=0; $c<=$nivel; $c++) $strTab .= ' ';
    return $strTab;
  }
  //-----------------------------------------------------------------
  function getButtons($id, $nivel, $nombre, $tieneSubc)
  {
    $listBt = array();
    $listBt['nombre'] = $nombre;

    //-------
    if($this->opUpdate == true) {
       $listBt['update'] = '<i class="fa fa-pencil-square-o fa-lg" aria-hidden="true" onClick="wtree2_onUpdate('.$id.', '.$nivel.')"></i>';
    }

    //-------
    if($this->opNewSub == true) {
      if($nivel < $this->niveles) {
         $listBt['new'] = '<i class="fa fa-pencil-square-o fa-lg" aria-hidden="true" onClick="wtree2_onNewSub('.$id.', '.$nivel.')"></i>';
      }
    }

    //-------
    if($this->opDelete == true && !$tieneSubc) {
       $listBt['del'] = '<span class="bt_del" onClick="wtree2_onDel('.$id.', '.$nivel.')">X</span>';
    }

    //-------
    if($nivel >= $this->haveElementsOnLevel) {
       if(!$tieneSubc) {
          $link = '?CONTROL='.$this->id.'&EVENT=list_rowSelected&ROW_ID='.$id.'&nivel='.$nivel;
          $listBt['nombre'] = '<a href="'.$link.'">'.$listBt['nombre'].'</a>';
       }
       else {
          $listBt['detalle'] = '<i class="fa fa-arrow-right fa-lg" aria-hidden="true" onClick="wtree2_onDetalle('.$id.', '.$nivel.')"></i>';
       }
    }

    if($this->opCheckBox == true && $nivel >= $this->check_onLevel) {
       $checked  = '';
       if(in_array($id, $this->check_listSelected)) {
          $checked = 'checked';
       }
       $listBt['check'] = '<input type="checkbox" class="check" name="'.$this->check_name.'[]" value="'.$id.'" '.$checked.'>';
    }

    return $listBt;
  }
  //-----------------------------------------------------------------
}
