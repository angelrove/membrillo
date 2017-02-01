<?
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 * 2006
 *
 */

namespace membrillo\components;

use membrillo\utils\Db_mysql;
use membrillo\utils\Paging;
use membrillo\utils\CssJs_load;
use membrillo\Event;


CssJs_load::set(MEMBRILLO_URL.'/_vendor/shortcut.js');

CssJs_load::set(MEMBRILLO_URL.'/components/WList/style.css');
CssJs_load::set(MEMBRILLO_URL.'/components/WList/lib.js');

/**
 * Interfaces
 */
interface iWListRowEditor {
  public function getBgColorAt($id, $idSelected, $values);
}

interface iWListCellEditor {
  public function getValueAt($id, $columnName, $value, $values);
  public function getBgColorAt($id, $columnName, $value, $values);
  public function getOnClick($id, $columnName, $value, $values);
}

interface iWListCellOptionsEditor {
  public function showBtDelete($id, $values);
  public function showBtUpdate($id, $values);
  /*
   * return:
   *     true/false
   *     array('label'=>'[lable]', 'disabled'=>[true/false], 'href'=>'[xxx]');
   */
  public function showBtOp($key, $id, $values);
}


class WList extends EventComponent
{
  private $sqlQuery, $dbFields;
  private $title;
  private $showScroll;

  private $defaultOrder    = 'id';
  private $defaultSelected = false;

  // Paginación
  private $paging_showOn  = 'top'; // top, bottom, none
  private $paging_numRows = 100;
  private $paging_config  = '';

  // Eventos
  private $event_fOrder  = 'fieldOrder';
  private $event_fOnClick= 'fieldOnClick';
  private $event_numPage = 'list_numPage';
  private $event_detalle = 'detalle';
  private $event_search  = 'search';
  private $event_new     = '';

  private $events = array('new'   =>'editNew',
                          'update'=>'editUpdate',
                          'delete'=>'delete');

  private $event_delete = 'delete';
  private $msgConfirmDel = '';

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
  function __construct($id_control, $sqlQ, $dbFields)
  {
    parent::__construct($id_control);

    $this->sqlQuery = $sqlQ;
    $this->dbFields = $dbFields;

    // if (se ejecuta por 1º vez) { // inicializar datos
    //   $this->wObjectStatus->setDato('param_fieldPrev', '');
    // }

    $this->parse_event($this->WEvent);
  }
  //--------------------------------------------------------------
  function parse_event($WEvent)
  {
    $datos = $this->wObjectStatus->getDatos();

    switch($WEvent->EVENT) {
      //----------
      case $this->event_fOrder:
        // invertir la ordenación
        if($datos['param_fieldPrev'] == $datos['param_field']) {
           $order_asc = ($datos['order_asc'] == 'DESC')? 'ASC':'DESC';

           $this->wObjectStatus->setDato('order_asc', $order_asc);
        }

        $this->wObjectStatus->setDato('param_fieldPrev', $datos['param_field']);
        $this->wObjectStatus->delDato('id_page'); // reiniciar la paginación
      break;
      //----------
      case $this->event_search:
        //$this->wObjectStatus->delRowId($this->id_object); // quitar la tupla seleccionada
        $this->wObjectStatus->delDato('id_page');  // reiniciar la paginación
      break;
      //----------
    }

  }
  //-------------------------------------------------------
  /* Eventos de otros controles List */
  private function parseEvents()
  {
    switch(Event::$EVENT)
    {
     case $this->event_detalle: // reiniciar la paginación
       $this->wObjectStatus->delDato('id_page');
     break;
    }
  }
  //--------------------------------------------------------------
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
  public function setRowEditor($rowEditor) {
    $this->rowEditor = $rowEditor;
  }
  //-------------------------------------------------------
  public function setCellEditor($cellEditor) {
    $this->cellEditor = $cellEditor;
  }
  //-------------------------------------------------------
  public function setOptionsEditor($optionsEditor) {
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

    global $app;
    if($this->bt_delete_confirm) {
       $this->msgConfirmDel = 'Eliminar';
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
  public function setBtOpc($event, $label='', $onClick=false, $title='') {
    $this->list_Op[$event] = array('event'  => $event,
                                   'oper'   => $event,
                                   'label'  => $label,
                                   'onClick'=> $onClick,
                                   'title'  => $title,
                                   'href'   => '',
                                   'target' => '',
                                   'disabled' => '',
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
  public function getHtmRows()
  {
    $controlID = $this->id_object;

    /** Eventos **/
     $this->parseEvents();

    /** Cuerpo **/
     // Datos
     $listDatos = array();
     $htmPaginacion = '';

     if($this->sqlQuery) {
        $sqlQ = $this->getQuery($this->sqlQuery);
        //print_r2($sqlQ);

        if($this->paging_showOn == 'none') {
           $listDatos = Db_mysql::getListObject($sqlQ);
        } else {
           list($htmPaginacion, $listDatos) = $this->getPaginacion($sqlQ);
        }
        //print_r2($listDatos);
     }

    /** Default selected **/
     if($this->defaultSelected == true && !$this->wObjectStatus->getRowId()) {
        $primero = current($listDatos);
        $this->wObjectStatus->setRowId($primero->id);
     }

    /** HTM: datos **/
     $htmListDatos = '';
     if($listDatos) {
        $id_selected = $this->wObjectStatus->getRowId();
        $htmListDatos = $this->getHtmRowsValues($listDatos, $id_selected);
     }

    /** HTM: Cabecera **/
     $htmColumnas = $this->getHead();

    /** OUT **/
     ob_start(); /* ¡¡Para retornar el contenido del include!! */
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
<form class="FormSearch form-inline well well-sm"
      role="search"
      name="search_form"
      method="get"
      action="./">
 <input type="hidden" name="CONTROL" value="$this->id_object">
 <input type="hidden" name="EVENT" value="$this->event_search">

EOD;
  }
  //--------------------------------------------------------------
  public function formSearch_END() {
    echo ' </form>

';
  }
  //--------------------------------------------------------------
  public function formSearch_complet() {
    $f_search = $this->wObjectStatus->getDato('f_search');

    $this->formSearch();
      ?>
      <div class="form-group">
        <input type="text" class="form-control input-sm" name="f_search" placeholder="Buscar" value="<?=$f_search?>">
      </div>
      <?
      //$this->formSearch_btBuscar();
    $this->formSearch_END();
  }
  //--------------------------------------------------------------
  public function formSearch_btBuscar() {
    echo '&nbsp;<button type="submit" class="btn btn-primary btn-sm">Buscar</button>';
  }
  //--------------------------------------------------------------
  // PRIVATE
  //-------------------------------------------------------
  private function getQuery($sqlQ)
  {
    /** 'ORDER' **/
     $param_field = $this->wObjectStatus->getDato('param_field');
     $order_asc   = $this->wObjectStatus->getDato('order_asc');

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
  private function getPaginacion($sqlQuery) {
    $htmPaginacion = '';
    $rows          = '';

    // Páginas
    $urlFormat = "?CONTROL=".$this->id_object."&EVENT=".$this->event_numPage."&id_page=[id_page]&tpages=[tpages]";

    $id_page = $this->wObjectStatus->getDato('id_page');
    if(!$id_page) $id_page = 1;

    $htmPaginacion = new Paging($sqlQuery, $this->paging_numRows, $id_page);
    if($this->paging_config == 'basic') {
       $htmPaginacion->setNumPages(3);
    } else {
       $htmPaginacion->setNumPages(10);
    }
    $htmPaginacion->setUrlFormat($urlFormat);

    $rows = $htmPaginacion->getListRows();

    // HTML
    $listPaginas = $htmPaginacion->getHtmPaginas();

    $numTotal  = $htmPaginacion->getNumRows();
    $str_desde = $htmPaginacion->getItemDesde();
    $str_hasta = $htmPaginacion->getItemHasta();

    $htmPaginacion = '';
    if($this->paging_config == 'basic') {
       $htmPaginacion = $listPaginas;
    }
    else {
       $htmPaginacion = <<<EOD
         <div class="center-block2 clearfix">
           <div class="pull-left">$listPaginas</div>
           <div class="pull-right resumen">&nbsp; ($str_desde a $str_hasta) de <b>$numTotal</b></div>
         </div>
EOD;
    }

    return array($htmPaginacion, $rows);
  }
  //--------------------------------------------------------------
  // HEAD
  //--------------------------------------------------------------
  private function getHead() {
    global $app;

    /** Títulos de los campos **/
     $orderSimbol = ($this->wObjectStatus->getDato('order_asc') == 'DESC')? 'v ' : '^ ';
     $param_field = $this->wObjectStatus->getDato('param_field');

     $htmTitles = '';
     foreach($this->dbFields as $dbField) {
        if(!$dbField) continue;

        // Campo de ordenación
        if($dbField->order) {
           $simbolo = ($param_field == $dbField->order)? $orderSimbol : '=';
           $link = "?CONTROL=$this->id_object&EVENT=$this->event_fOrder&param_field=$dbField->order";
           $dbField->title = '<a class="btFieldOrder" href="'.$link.'">'.$simbolo.' '.$dbField->title.'</a>';
        }
        // OnClick
        if($dbField->onClick) {
           $link = "?CONTROL=$this->id_object&EVENT=$this->event_fOnClick&onClick_field=$dbField->onClick&id_page=0";
           $dbField->title = '<a class="btFieldOrder" href="'.$link.'">'.$dbField->title.'</a>';
        }

        // width
        $width = ($dbField->size)? ' style="min-width:'.$dbField->size.'px"' : '';
        $width = '';

        // Out
        $htmTitles .= '<th id="fieldTitle_'.$dbField->name.'"'.$width.'>'.$dbField->title.'</th>';
     }

    /** Botón 'Nuevo...' **/
     $strBt_new = '';
     if($this->bt_new) {
        $strBt_new = '<button type="button" class="on_new btn btn-primary btn-xs">New...</button>';
     }

    return $htmTitles.'<th class="optionsBar" id="List_cabButtons_'.$this->id_object.'">'.$strBt_new.'</th>';
  }
  //-------------------------------------------------------
  // Datos
  //-------------------------------------------------------
  private function getHtmRowsValues($rows, $id_selected)
  {
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
        $styleSelected = ($id == $id_selected)? ' class="success"' : '';

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
     if($this->bt_delete) {
        $label = ''; // $label = <span>Delete</span>
        $htmButtons['bt_delete'] = '<button class="on_delete btn btn-xs btn-danger"><i class="fa fa-trash-o"></i>'.$label.'</button>';
        if($this->optionsEditor && $this->optionsEditor->showBtDelete($id, $row) === false) {
           $htmButtons['bt_delete'] = '';
        }
     }

     if($this->bt_update) {
        $label = ''; // $label = <span>Update</span>
        $htmButtons['bt_update'] = '<button class="on_update btn btn-xs btn-default"><i class="fa fa-pencil-square-o"></i>'.$label.'</button>';
        if($this->optionsEditor && $this->optionsEditor->showBtUpdate($id, $row) === false) {
           $htmButtons['bt_update'] = '';
        }
     }

     if($this->bt_detalle) {
        $label = ''; // $label = <span>Detalle</span>
        $htmButtons['bt_detalle'] = '<button class="on_detalle btn btn-xs btn-primary"><i class="fa fa-arrow-right"></i>'.$label.'</button>';
     }

     // Opcional ---
     $hrefOption = "?CONTROL=$this->id_object&ROW_ID=$id";
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
              if(isset($ret_optionsEditor['label'])) {
                $bt_opc['label'] = $ret_optionsEditor['label'];
              }
              if(isset($ret_optionsEditor['disabled'])) {
                $bt_opc['disabled'] = $ret_optionsEditor['disabled'];
              }
              $bt_opc['href']     = $ret_optionsEditor['href'];
              $bt_opc['target']   = $ret_optionsEditor['target'];
           }
        }

        //--------
        $bt_href = "$hrefOption&EVENT=$bt_opc[event]&OPER=$bt_opc[oper]";

        if($bt_opc['href']) {
           $bt_href = $bt_opc['href'];
        }
        if(!$bt_opc['title']) {
           $bt_opc['title'] = $bt_opc['label'];
        }

        $bt_target = '';
        if($bt_opc['target']) {
           $bt_target = $bt_opc['target'];
        }
        if($bt_opc['disabled'] === true) {
           $htmButtons['op_'.$key] = '<span class="disabled_'.$key.'">'.$bt_opc['label'].'</span>';
        } else {
           $htmButtons['op_'.$key] = "<a class='$key btn btn-default btn-xs' role='button' href='$bt_href' target='$bt_target' title='$bt_opc[title]'>$bt_opc[label]</a>";
        }
     }
     // END Opcional ---

    /** Out **/
     return '<td class="optionsBar">'.implode('', $htmButtons).'</td>';
  }
  //--------------------------------------------------------------
}
