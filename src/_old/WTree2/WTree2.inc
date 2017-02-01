<?
/**
 * José A. Romero Vegas, 2006
 * jangel.romero@gmail.com
 *
 */

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

namespace membrillo\InputComponents;

use membrillo\utils\CssJs_load;
use membrillo\utils\Db_mysql;

// Vendor ----
CssJs_load::set_css(MEMBRILLO_URL.'/_vendor/tree-jquery-treeview-1.4.2/jquery.treeview.css');
CssJs_load::set_js (MEMBRILLO_URL.'/_vendor/tree-jquery-treeview-1.4.2/jquery.treeview.js');
CssJs_load::set_js (MEMBRILLO_URL.'/_vendor/tree-jquery-treeview-1.4.2/lib/jquery.cookie.js');

$object = 'WTree2';
CssJs_load::set_css(MEMBRILLO_URL.'/classes/WObjects/'.$object.'/style.css?v=15');
CssJs_load::set_js (MEMBRILLO_URL.'/classes/WObjects/'.$object.'/libs.js');


class WTree2 {
  private $id;
  private $dbTable = 'categorias';
  private $title   = 'Categorías';
  private $niveles;
  private $width;

  private $opNew    = true;
  private $opUpdate = false;
  private $opNewSub = true;
  private $opDelete = true;
  private $opCheckBox = false;
  private $searchWord = false;

  private $imgNuevo   = 'bt_nuevo_2.png';
  private $imgDetalle = 'list_detalle.gif';

  private $marcas = array();  // Busquedas
  private $statusCiclo = array();  // Status de la generación HTML

  private $sqlWhere    = '';
  private $sqlWhere_L3 = '';

  //-----------------------------------------------------------------
  function WTree2($idComponent, $dbTable, $width, $niveles=3) {
    global $CONFIG_APP;

    $this->id      = $idComponent;
    $this->dbTable = $dbTable;
    $this->width   = $width.'px';
    $this->niveles = $niveles;

    $this->urlImg = MEMBRILLO_URL.'/classes/WObjects/WTree2/images/';
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
  public function getHtm() {
    // HTM de categorías
    $strCategorias = $this->get_category_tree();

    // Button "Nuevo"
    $strNuevo = '<a href="#" class="bt_new" title="Nueva categoría">Nuevo...</a>';
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
<!-- WTree2 -->
 <script>
 var WTree2_CONTROL = '$this->id';

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
 </script>

 <div id="sidetree" class="WTree2" style="width:$this->width">
  <!-- Cabecera -->
  <table class="WTree_cabecera" width="100%" cellpadding="0" cellspacing="0">
    <tr>
      <td id="sidetreecontrol" class="sidetreecontrol">
        <a href="?#"><i class="fa fa-caret-square-o-up fa-lg"></i></a> &nbsp;<a href="?#"><i class="fa fa-caret-square-o-down fa-lg"></i></a>
      </td>
      <td class="title">$this->title</td>
      <td class="btNuevo">$strNuevo</td>
    </tr>
  </table>
  <!-- /Cabecera -->

  <!-- Rows -->
  <div class="WTree_tuplas">
   <ul id="tree">
     $strCategorias
   </ul>
  </div>
  <!-- /Rows -->
 </div>
<!-- WTree2 -->
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

    foreach($listCategorias as $id => $categ) {
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
  private function getCategorias($id_padre, $nivel=0) {
    $sqlWhere_L3 = ($nivel == 3)? $this->sqlWhere_L3 : '';

    $sqlQ = "SELECT id, IF(nombre <> '', nombre, '[sin título]') AS nombre
             FROM $this->dbTable
             WHERE id_padre='$id_padre' $this->sqlWhere $sqlWhere_L3
             ORDER BY nombre";
    //print_r2($sqlQ);
    return Db_mysql::getList($sqlQ);
  }
  //-----------------------------------------------------------------
  function getHtmOpen($id, $nombre, $nivel, $count, $tieneSubc) {
    global $objectsStatus;

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
    $htmCateg =
      "\n<li>".
           $listBt['check'].' '.$listBt['update'].' '.$listBt['new'].' '.$listBt['del'].
           ' <span id="cat_'.$id.'" param_nivel="'.$nivel.'" class="tuplaL '.$classTupla.'" '.$title.'>'.$listBt['nombre'].'</span> '.
           $listBt['detalle'];

    if($tieneSubc) {
       $htmCateg .= "\n<ul>";
    }

    return $htmCateg;
  }
  //-----------------------------------------------------------------
  function getHtmCierre($id, $nivel, $count, $tieneSubc)
  {
    $htmCateg = '';
    if($tieneSubc) {
       $htmCateg .= "\n</ul>\n";
    }
    $htmCateg .= "</li>";

    return $htmCateg;
  }
  //-----------------------------------------------------------------
  //-----------------------------------------------------------------
  function getButtons($id, $nivel, $nombre, $tieneSubc) {
    $listBt = array(
       'nombre' => '',
       'update' => '',
       'new'    => '',
       'del'    => '',
       'detalle'=> '',
       'check'  => '',
    );

    $listBt['nombre'] = $nombre;

    //-------
    if($this->opUpdate == true) {
       $listBt['update'] = '<i class="bt_update fa fa-pencil-square-o fa-lg" param_id="'.$id.'"></i>';
    }
    //-------
    if($this->opDelete == true && !$tieneSubc) {
       $listBt['del'] = '<i class="bt_del fa fa-trash-o fa-lg" param_id="'.$id.'"></i>';
    }
    //-------
    if($this->opNewSub == true) {
      if($nivel < $this->niveles) {
         $listBt['new'] = '<i class="bt_newSub" param_id="'.$id.'"><img src="'.$this->urlImg.$this->imgNuevo.'"></i>';
      }
    }

    //-------
    if($nivel >= $this->haveElementsOnLevel) {
       if(!$tieneSubc) {
          $listBt['nombre'] = '<a href="'.'?CONTROL='.$this->id.'&EVENT=list_rowSelected&ROW_ID='.$id.'&nivel='.$nivel.'">'.$listBt['nombre'].'</a>';
       }
       else {
          $listBt['detalle'] = '<i class="bt_detalle" param_id="'.$id.'"><img src="'.$this->urlImg.$this->imgDetalle.'"></i>';
       }
    }

    //-------
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
?>
