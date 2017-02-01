<?
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 */

namespace angelrove\membrillo\WObjects\WList;

use angelrove\utils\CssJsLoad;
use angelrove\utils\Db_mysql;
use angelrove\front_components\Pagination;


/**
 * WList
 */
class WList
{
  private $id, $sqlQuery, $dbFields;
  private $title;

  private $defaultOrder    = 'id';
  private $defaultSelected = false;

  // Paginación
  private $paging_showOn  = 'top'; // top, bottom, none
  private $paging_numRows = 100;

  // Eventos
  private $event_fOrder  = 'fieldOrder';
  private $event_fOnClick= 'fieldOnClick';
  private $event_numPage = 'list_numPage';
  private $event_detalle = 'detalle';
  private $event_search  = 'search';

  private $events = array('new'   =>'editNew',
                          'update'=>'editUpdate',
                          'delete'=>'delete');

  private $event_delete  = 'delete';

  // Botones
  private $onClickRow = '';
  private $list_Op = array();

  private $bt_new     = false;
  private $bt_update  = false;
  private $bt_detalle = false;
  private $bt_delete  = false;
  private $bt_delete_confirm = true;

  // Editores
  private $rowEditor;
  private $cellEditor;
  private $optionsEditor;

  //-------------------------------------------------------
  // PUBLIC
  //-------------------------------------------------------
  function __construct($control, $sqlQ, $dbFields)
  {
    $this->control  = $control;
    $this->sqlQuery = $sqlQ;
    $this->dbFields = $dbFields;

    //------
    CssJsLoad::set(__DIR__.'/styles.css');
    CssJsLoad::set(__DIR__.'/scripts.js');
  }
  //-------------------------------------------------------
  // Setter
  //-------------------------------------------------------
  public function showTitle($title) {
    $this->title = $title;
  }
  //-------------------------------------------------------
  public function setListFields($dbFields) {
    $this->dbFields = $dbFields;
  }
  //-------------------------------------------------------
  public function setDefaultOrder($defaultOrder) {
    $this->defaultOrder = $defaultOrder;
  }
  //-------------------------------------------------------
  /* Selección de la primera tupla por defecto */
  public function setDefaultSelected() {
    $this->defaultSelected = true;
  }
  //-------------------------------------------------------
  public function setReadOnly($isReadonly) {
    if($isReadonly) {
       $this->bt_new    = false;
       $this->bt_delete = false;
    }
  }
  //-------------------------------------------------------
  public function setScroll($height) {
    $this->showScroll = true;
    $this->height     = $height;
  }
  //-------------------------------------------------------
  // Editors
  //-------------------------------------------------------
  public function setRowEditor(iWListRowEditor $rowEditor) {
    $this->rowEditor = $rowEditor;
  }
  //-------------------------------------------------------
  public function setCellEditor(iWListCellEditor $cellEditor) {
    $this->cellEditor = $cellEditor;
  }
  //-------------------------------------------------------
  public function setOptionsEditor(iWListCellOptionsEditor $optionsEditor) {
    $this->optionsEditor = $optionsEditor;
  }
  //-------------------------------------------------------
  // Eventos
  //-------------------------------------------------------
  public function onClickRow($event) {
    $this->onClickRow = $event;
  }
  //-------------------------------------------------------
  public function showNew($showButton=true) {
    $this->event_new = $this->events['new'];
    $this->bt_new    = $showButton;
  }
  //-------------------------------------------------------
  public function showUpdate($showButton=false) {
    $this->event_update = $this->events['update'];

    if($showButton === true) {
       $this->bt_update = true;
       if(!$this->onClickRow) { // si no se ha asignado previamente
          $this->onClickRow = $this->event_update;
       }
    }
    else {
       $this->onClickRow = $this->event_update;
    }
  }
  //-------------------------------------------------------
  public function showDelete($isConfirm=true) {
    $this->event_delete = $this->events['delete'];
    $this->bt_delete    = true;
    $this->bt_delete_confirm = $isConfirm;

    global $LOCAL;
    if($this->bt_delete_confirm) {
       $this->msgConfirmDel = $LOCAL['WList_del_m'];
    }
  }
  //-------------------------------------------------------
  public function showDetalle($showButton=true) {
    $this->bt_detalle = $showButton;

    if($showButton !== true) {
       $this->onClickRow = $this->event_detalle;
    }
  }
  //-------------------------------------------------------
  public function setBtOpc($event, $image, $label='', $onClick=false, $title='') {
    $this->list_Op[$event] = array('event'  => $event,
                                   'oper'   => $event,
                                   'image'  => $image,
                                   'label'  => $label,
                                   'onClick'=> $onClick,
                                   'title'  => $title,
                                   );
  }
  //-------------------------------------------------------
  // Paginación
  //-------------------------------------------------------
  // $position: top, bottom, none,
  // $config:   basic
  public function showPaginacion($position, $config='', $numRows=100) {
    $this->paging_showOn  = $position;
    $this->paging_config  = $config;
    $this->paging_numRows = $numRows;
  }
  //-------------------------------------------------------
  // deprecated !!
  public function setNumRowsPage($paging_numRows) {
    $this->paging_numRows = $paging_numRows;
  }
  //-------------------------------------------------------
  // OUT
  //-------------------------------------------------------
  public function getHtmRows() {
    global $seccCtrl;
    $controlID = $this->control;

    /** Eventos **/
     $this->parseEvents();

    /** Cuerpo **/
     // Datos
     $listDatos = array();
     if($this->sqlQuery) {
        $sqlQ = $this->getQuery($this->sqlQuery);
        //print_r2($sqlQ);

        if($this->paging_showOn == 'none') {
           $listDatos = Db_mysql::getListObject($sqlQ);
        } else {
           list($htmPagination, $listDatos) = $this->getPaginacion($sqlQ);
        }
        //print_r2($listDatos);
     }

    /** Default selected **/
     if($this->defaultSelected == true && !$seccCtrl->getRowId($controlID)) {
        $primero = current($listDatos);
        $seccCtrl->setRowId($controlID, $primero->id);
     }

    /** HTM: datos **/
     $htmListDatos = '';
     if($listDatos) {
        $id_selected = $seccCtrl->getRowId($this->control);
        $htmListDatos = $this->getHtmRowsValues($listDatos, $id_selected);
     }

    /** HTM: Cabecera **/
     $htmColumnas = $this->getHead();

    /** OUT **/
     ob_start(); /* ¡¡Paraaaa retornar el contenido del include!! */
     include('tmpl_list.inc');
     return ob_get_clean();
  }
  //--------------------------------------------------------------
  public function getDebug() {
    $sqlQ = $this->getQuery($this->sqlQuery);
    print_r2($sqlQ);

    $listDatos = Db_mysql::getList($sqlQ);
    print_r2($listDatos);
  }
  //--------------------------------------------------------------
  // Form search
  //--------------------------------------------------------------
  public function formSearch() {
    echo <<<EOD
<form name="search_form" method="get" action="./">
<input type="hidden" name="CONTROL" value="$this->control">
<input type="hidden" name="EVENT"   value="$this->event_search">
<div class="FormSearch">

EOD;
  }
  //--------------------------------------------------------------
  public function formSearch_END() {
    echo ' </div></form>
';
  }
  //--------------------------------------------------------------
  public function formSearch_btBuscar()
  {
    echo '<input type="submit" value="&nbsp Buscar &nbsp">';
  }
  //--------------------------------------------------------------
  // PRIVATE
  //-------------------------------------------------------
  private function parseEvents()
  {
    global $seccCtrl;
    $datos = $seccCtrl->getDatos($this->control);

    if($seccCtrl->CONTROL == $this->control) {
       switch($seccCtrl->getEvent($this->control)) {
         case $this->event_fOrder:
           // invertir la ordenación
           if($datos['param_fieldPrev'] == $datos['param_field']) {
              $order_asc = ($datos['order_asc'] == 'DESC')? 'ASC':'DESC';
              $seccCtrl->setDato($this->control, 'order_asc', $order_asc);
           }
           $seccCtrl->setDato($this->control, 'param_fieldPrev', $datos['param_field']);

           $seccCtrl->delDato($this->control, 'id_page'); // reiniciar la paginación
         break;

         case $this->event_search:
           //$seccCtrl->delRowId($this->control);           // quitar la tupla seleccionada
           $seccCtrl->delDato($this->control, 'id_page'); // reiniciar la paginación
         break;
       }
    }
    /* Eventos de otros controles WList */
    else {
       switch($seccCtrl->EVENT) {
         case $this->event_detalle: // reiniciar la paginación
           $seccCtrl->delDato($this->control, 'id_page');
         break;
       }
    }
  }
  //--------------------------------------------------------------
  private function getQuery($sqlQ) {
    global $seccCtrl;

    /** 'ORDER' **/
     $param_field = $seccCtrl->getDato($this->control, 'param_field');
     $order_asc   = $seccCtrl->getDato($this->control, 'order_asc');

     $sqlOrder = ' ORDER BY ';
     if($param_field) { // Ordenamiento de usuario
        $sqlOrder .= $param_field.' '.$order_asc;
     }
     else { // para no solapar el ordenamiento del usuario con ordenamiento por defecto
        $sqlOrder .= $this->defaultOrder;
     }

    /** OUT **/
     $sqlQ .= $sqlOrder;

     return $sqlQ;
  }
  //--------------------------------------------------------------
  private function getPaginacion($sqlQuery)
  {
    $htmPagination = '';
    $rows          = '';

    // Páginas
    global $seccCtrl;
    $urlFormat = "?CONTROL=".$this->control."&EVENT=".$this->event_numPage."&id_page=[id_page]&tpages=[tpages]";

    $id_page = $seccCtrl->getDato($this->control, 'id_page');
    if(!$id_page) $id_page = 1;

    $htmPagination = new Pagination($sqlQuery, $this->paging_numRows, $id_page);
    if($this->paging_config == 'basic') {
       $htmPagination->setNumPages(3);
    } else {
       $htmPagination->setNumPages(10);
    }
    $htmPagination->setUrlFormat($urlFormat);

    $rows = $htmPagination->getListRows();

    // HTML
    $listPaginas = $htmPagination->getHtmPaginas();

    $numTotal  = $htmPagination->getNumRows();
    $str_desde = $htmPagination->getItemDesde();
    $str_hasta = $htmPagination->getItemHasta();

    $htmPagination = '';
    if($this->paging_config == 'basic') {
       $htmPagination = $listPaginas;
    }
    else {
       $htmPagination = <<<EOD
         <table align="center"><tr>
           <td>$listPaginas</td>
           <td class="resumen">&nbsp; ($str_desde a $str_hasta) de <b>$numTotal</b></td>
         <tr></table>
EOD;
    }

    return array($htmPagination, $rows);
  }
  //--------------------------------------------------------------
  // HEAD
  //--------------------------------------------------------------
  private function getHead() {
    global $seccCtrl, $LOCAL;

    /** Títulos de los campos **/
     $orderSimbol   = ($seccCtrl->getDato($this->control, 'order_asc') == 'DESC')? 'v ' : '^ ';
     $param_field   = $seccCtrl->getDato($this->control, 'param_field');
     $onClick_field = $seccCtrl->getDato($this->control, 'onClick_field');

     $htmTitles = '';
     foreach($this->dbFields as $dbField) {
        if(!$dbField) continue;

        // Campos de ordenación
        if($dbField->order) {
           $simbolo   = ($param_field == $dbField->order)? $orderSimbol : '=';
           $linkOrder = "?CONTROL=$this->control&EVENT=$this->event_fOrder&param_field=$dbField->order";
           $dbField->title = '<a class="btFieldOrder" href="'.$linkOrder.'">'.$simbolo.' '.$dbField->title.'</a>';
        }
        // OnClick
        if($dbField->onClick) {
           $linkOnClick = "?CONTROL=$this->control&EVENT=$this->event_fOnClick&onClick_field=$dbField->onClick&id_page=0";
           $dbField->title = '<a class="btFieldOrder" href="'.$linkOnClick.'">'.$dbField->title.'</a>';
        }

        // width
        $width = ($dbField->size)? ' style="min-width:'.$dbField->size.'px"' : '';

        // Out
        $htmTitles .= '<th id="fieldTitle_'.$dbField->name.'"'.$width.'>'.$dbField->title.'</th>';
     }

    /** Botón 'Nuevo...' **/
     $strBt_new = '';
     if($this->bt_new) {
        $strBt_new = '<input class="on_new" type="button" value="'.$LOCAL['WList_new'].'">';
     }

    return $htmTitles.'<th id="WList_cabButtons_'.$this->control.'">'.$strBt_new.'</th>';
  }
  //-------------------------------------------------------
  // Datos
  //-------------------------------------------------------
  private function getHtmRowsValues($rows, $id_selected) {
    global $seccCtrl;

    /** TRs **/
     $htmList = '';
     $count = 0;
     foreach($rows as $id => $row)
     {
       /** RowEditor **/
        $row_bgColor = '';
        if(isset($this->rowEditor)) {
           $row_bgColor = $this->rowEditor->getBgColorAt($id, $id_selected, $row);
        }

       /** Datos **/
        $strCols = '';
        foreach($this->dbFields as $dbField) {
           if(!$dbField) continue;

           $strField = $this->getHtmField($id, $row, $dbField, $id_selected);
           if($strField == "EXIT_ROW") { $strCols = ''; break; }
           $strCols .= $strField;
        }
        if($strCols == '') continue;

       /** Botones **/
        $strButtons = $this->getHtmButtons($id, $row, ++$count);

       /** Color de la tupla **/
        $styleColor    = ($row_bgColor)? " style=\"background:$row_bgColor\"" : '';
        $styleSelected = ($id == $id_selected)? ' class="selected"' : '';

       /** Tupla **/
        $htmList .= "\n<tr id='$id'".$styleSelected.$styleColor.">\n$strCols $strButtons\n</tr>";
    }

    return $htmList;
  }
  //-------------------------------------------------------
  private function getHtmField($id, $row, $dbField)
  {
    $f_value = $row->{$dbField->name};

    $f_valueCampo = $f_value;
    $style_align  = ($dbField->align)? 'text-align:'.$dbField->align.';' : '';

    /** CellEditor **/
     $f_bgColor = '';
     $f_onClick = '';
     if(isset($this->cellEditor)) {
        $f_valueCampo = $this->cellEditor->getValueAt($id, $dbField->name, $f_value, $row);

        if($f_bgColor = $this->cellEditor->getBgColorAt($id, $dbField->name, $f_value, $row)) {
           $f_bgColor = 'background:'.$f_bgColor.';';
        }

        if($f_onClick = $this->cellEditor->getOnClick($id, $dbField->name, $f_value, $row)) {
           $f_onClick = ' class="onClickUser '.$f_onClick.'"';
        }
     }

    /** OUT **/
     $style = '';
     if($style_align || $f_bgColor) {
        $style = ' style="'.$style_align.$f_bgColor.'"';
     }

     return '<td'.$f_onClick.$style.'>'.nl2br($f_valueCampo).'</td>';
  }
  //-------------------------------------------------------
  // Buttons
  //-------------------------------------------------------
  private function getHtmButtons($id, $row, $numRow) {
    $htmButtons = array();

    /** Buttons **/
     if($this->bt_update) {
        $label = ''; // $label = <span>Update</span>
        $htmButtons['bt_update'] = '<a href="#" class="on_update"><span class="icon i_update"></span>'.$label.'</a>';
        if($this->optionsEditor && $this->optionsEditor->showBtUpdate($id, $row) === false) {
           $htmButtons['bt_update'] = '';
        }
     }

     if($this->bt_delete) {
        $label = ''; // $label = <span>Delete</span>
        $htmButtons['bt_delete'] = '<a href="#" class="on_delete"><i class="fa fa-trash fa-lg" aria-hidden="true"></i></span>'.$label.'</a>';
        if($this->optionsEditor && $this->optionsEditor->showBtDelete($id, $row) === false) {
           $htmButtons['bt_delete'] = '';
        }
     }

     if($this->bt_detalle) {
        $label = ''; // $label = <span>Detalle</span>
        $htmButtons['bt_detalle'] = '<a href="#" class="on_detalle"><span class="icon i_detalle"></span>'.$label.'</a>';
     }

     // Opcional ---
     $hrefOption = "?CONTROL=$this->control&ROW_ID=$id";
     foreach($this->list_Op as $key => $bt_opc) {
        // optionsEditor ----
        $ret_optionsEditor = '';
        if($this->optionsEditor) {
           $ret_optionsEditor = $this->optionsEditor->showBtOp($key, $id, $row);

           if($ret_optionsEditor === false) {     // ocultar
              continue;
           }
           elseif($ret_optionsEditor === true) {  // mostrar
              // show
           }
           elseif(is_array($ret_optionsEditor)) { // parámetros
              if($ret_optionsEditor['label']) $bt_opc['label'] = $ret_optionsEditor['label'];
              $bt_opc['disabled'] = $ret_optionsEditor['disabled'];
              $bt_opc['href']     = $ret_optionsEditor['href'];
              $bt_opc['target']   = $ret_optionsEditor['target'];
           }
        }

        //--------
        $bt_href = "$hrefOption&EVENT=$bt_opc[event]&OPER=$bt_opc[oper]";
        $bt_img  = ($bt_opc['image'])? "<span class=\"icon\" style=\"background-image:url('$bt_opc[image]')\"></span>" : '';

        if($bt_opc['href']) {
           $bt_href = $bt_opc['href'];
        }
        if(!$bt_opc['title']) {
           $bt_opc['title'] = $bt_opc['label'];
        }

        if($bt_opc['target']) {
           $bt_target = $bt_opc['target'];
        }
        if($bt_opc['disabled'] === true) {
           $htmButtons['op_'.$key] = '<span class="disabled_'.$key.'">'.$bt_img.$bt_opc['label'].'</span>';
        } else {
           $htmButtons['op_'.$key] = "<a class='$key' href='$bt_href' target='$bt_target' title='$bt_opc[title]'>$bt_img$bt_opc[label]</a>";
        }
     }

    /** Out **/
     return '<td class="optionsBar">'.implode('', $htmButtons).'</td>';
  }
  //--------------------------------------------------------------
}
?>
