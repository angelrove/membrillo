<?
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 */

namespace angelrove\membrillo2\WObjects\WForm;

use angelrove\utils\Db_mysql;
use angelrove\utils\CssJsLoad;

use angelrove\membrillo2\WObjectsStatus\Event;
use angelrove\membrillo2\WObjectsStatus\EventComponent;
use angelrove\membrillo2\Messages;
use angelrove\membrillo2\GenQuery;

use angelrove\membrillo2\WPage\WPage;


class WForm extends EventComponent
{
  private $title;

  private $sql_row;
  private $db_table;
  private $datos = array();

  private $onSubmit;
  private $readOnly = false;

  // Buttons
  private $bt_ok     = true;
  private $bt_upd    = false;
  private $bt_del    = false;
  private $bt_cancel = true;

  private $bt_saveNext = false;
  private $bt_saveNext_label = '';

  private $setButtons_top = false;

  public static $errors = false;

  //------------------------------------------------------------------
  public function __construct($id_object, $db_table='', $sql_row='')
  {
    CssJsLoad::set(__DIR__.'/libs.js');

    //----------
    parent::__construct($id_object);

    $this->db_table = $db_table;
    $this->sql_row  = $sql_row;

    WPage::add_pagekey('WForm');

    //---------
    $this->parse_event($this->WEvent);
  }
  //--------------------------------------------------------------
  public function getDatos()
  {
    return $this->datos;
  }
  //--------------------------------------------------------------
  public function parse_event($WEvent)
  {
    switch($WEvent->EVENT) {
      //----------
      case 'editUpdate':
        $this->title .= ' Update';

        // Datos ---
        if(!$this->db_table && !$this->sql_row) {
           throw new \Exception('Class WForm (update) need a "db_table" or "sql_row"', 1);
        }

        if(!$this->sql_row) {
           $this->sql_row = GenQuery::selectRow($this->db_table, Event::$ROW_ID);
        }
        $this->datos = Db_mysql::getRow($this->sql_row);

        if(!$this->datos) {
           $strErr = 'El registro solicitado no existe';
           include('404.php');
           exit();
        }
      break;
      //----------
      case 'editNew':
        $this->title .= ' New';

        // Datos ---
        if(!$this->db_table) {
           throw new \Exception('WForm (editNew) need a "db_table"', 1);
        }

        $columns = Db_mysql::getListOneField("SHOW COLUMNS FROM ".$this->db_table);
        foreach($columns as $key => $value) {
           $this->datos[$key] = '';
        }
      break;
      //----------
    }

    // If Errors ----
    $this->datos = array_merge($this->datos, $_POST);
  }
  //------------------------------------------------------------------
  // Static
  //------------------------------------------------------------------
  public static function update_setErrors($listErrors)
  {
     if(!$listErrors) {
        return;
     }

     self::$errors = $listErrors;

     // Continue with edit
     Event::$REDIRECT_AFTER_OPER = false; // para que no se pierdan los datos recibidos por post

     if(Event::$ROW_ID) {
        Event::setEvent('editUpdate');
     } else {
        Event::setEvent('editNew');
     }

     // Highlight errors
     self::update_showErrors($listErrors);
  }
  //------------------------------------------------------------------
  private static function update_showErrors($listErrors)
  {
    $js = '';

    // resaltar campos ---
    foreach($listErrors as $name => $err)
    {
        Messages::set($err, 'danger');
        $js .= '$("[name='.$name.']").css("border", "2px solid red");';
    }

    // foco en el primer input erroneo
    end($listErrors);
    // $js .= '$("[name='.key($listErrors).']").focus();'."\n";

    // Out
    CssJsLoad::set_script('
  $(document).ready(function() {'.$js.'});
    ');
  }
  //------------------------------------------------------------------
  // NO STATIC
  //------------------------------------------------------------------
  public function isUpdate($row_id)
  {
    $this->WEvent->EVENT  = 'editUpdate';
    $this->WEvent->ROW_ID = $row_id;
    $this->parse_event($this->WEvent);
  }
  //------------------------------------------------------------------
  public function isInsert()
  {
    $this->WEvent->EVENT  = 'editNew';
    $this->parse_event($this->WEvent);
  }
  //------------------------------------------------------------------
  public function getFormEvent()
  {
    $event  = 'form_insert';
    $oper   = 'insert';
    $row_id = '';

    if($this->WEvent->EVENT == 'editUpdate') {
       $event  = 'form_update';
       $oper   = 'update';
       $row_id = $this->WEvent->ROW_ID;
    }

/*    echo '<div style="border:1px solid #bbb">'.
          '$this->WEvent->EVENT: '.$this->WEvent->EVENT.
          '<br><br>$event: '.$event.
          '<br>$oper:      '.$oper.
          '<br>$row_id:    '.$row_id.
         '</div>'
         ;
*/
    return array(
        'event' => $event,
        'oper'  => $oper,
        'row_id'=> $row_id,
      );
  }
  //------------------------------------------------------------------
  public function setListenerOnSubmit($onSubmit)
  {
    $this->onSubmit = $onSubmit.'()';
  }
  //------------------------------------------------------------------
  public function set_title($title)
  {
    $this->title = $title;
  }
  //------------------------------------------------------------------
  public function setButtons($bt_ok, $bt_upd, $bt_cancel, $bt_del=false)
  {
    $this->bt_ok     = $bt_ok;
    $this->bt_upd    = $bt_upd;
    $this->bt_del    = $bt_del;
    $this->bt_cancel = $bt_cancel;
  }
  //------------------------------------------------------------------
  public function set_bt_cancel($flag)
  {
    $this->bt_cancel = $flag;
  }
  //------------------------------------------------------------------
  public function show_btSaveNext($label='')
  {
    $this->bt_saveNext       = true;
    $this->bt_saveNext_label = $label;
  }
  //---------------------------------------------------------------------
  public function setButtons_top()
  {
    $this->setButtons_top = true;
  }
  //---------------------------------------------------------------------
  public function setReadOnly($isReadonly)
  {
    $this->readOnly = $isReadonly;
  }
  //------------------------------------------------------------------
  //------------------------------------------------------------------
  public function get()
  {
    // setButtons_top ---
    $htmButtons_top = '';
    if($this->setButtons_top) {
       $htmButtons_top = $this->getButtons('TOP');
    }

    //----
    if($this->readOnly) {
       echo '<form class="form-horizontal">';
       return;
    }

    // Datos evento
    $datosEv = $this->getFormEvent();
    $event  = $datosEv['event'];
    $oper   = $datosEv['oper'];
    $row_id = $datosEv['row_id'];

    // Out ---
    $isUpdate = ($this->bt_ok || $this->bt_upd) ? 'true' : 'false';

    include 'tmpl_start.inc';
  }
  //------------------------------------------------------------------
  public function get_end()
  {
    include 'tmpl_end.inc';
  }
  //------------------------------------------------------------------
  // $flag: '', 'top'
  public function getButtons($flag='')
  {
   global $app;

   $js = <<<EOD
//-----------------------------------
// WForm EVENTS
$(document).ready(function()
{
  $("#WForm_btUpdate$flag").click(function() {
     $("#form_edit_"+scut_id_object+" #EVENT").val('editUpdate');
  });
  $("#WForm_btInsert$flag").click(function() {
     $("#form_edit_"+scut_id_object+" #EVENT").val('editNew');
     $(".WForm").submit();
  });
  $("#WForm_btDelete$flag").click(function() {
     WForm_delete();
  });
  $("#WForm_btClose$flag").click(function() {
     WForm_close();
  });
});
//-----------------------------------
EOD;

   CssJsLoad::set_script($js);

   $bt_aceptar  = '<button type="submit" class="btn btn-primary" id="WForm_btAceptar'.$flag.'">'.$app->lang['accept'].'</button> '."\n";
   $bt_guardar  = '<button type="submit" class="btn btn-primary" id="WForm_btUpdate'.$flag.'">'.$app->lang['save'].'</button> '."\n";
   $bt_eliminar = '<button type="button" class="btn btn-danger " id="WForm_btDelete'.$flag.'">'.$app->lang['delete'].'</button> ';
   $bt_saveNext = '<button type="button" class="btn btn-primary" id="WForm_btInsert'.$flag.'">Insertar otro &raquo;</button> '."\n";
   $bt_cancelar = '<button type="button" class="btn btn-default" id="WForm_btClose'.$flag.'">'.$app->lang['close'].'</button>'."\n";

   $datosEv = $this->getFormEvent();

   if(!$this->bt_ok)  $bt_aceptar  = '';
   if(!$this->bt_upd) $bt_guardar  = '';
   if(!$this->bt_saveNext) $bt_saveNext = '';

   if(!$this->bt_cancel || $flag == 'TOP') $bt_cancelar = '';
   if(!$this->bt_del    || $flag == 'TOP' || $datosEv['event'] == 'form_insert') $bt_eliminar = '';

   // $strButtons
   $strButtons = $bt_aceptar.$bt_guardar.$bt_eliminar.$bt_saveNext.$bt_cancelar;

   if($this->readOnly) {
      $strButtons = $bt_cancelar;
   }

   // OUT
   if(!$strButtons) {
      return '';
   }

   return '
<!-- Buttons -->
<div class="form-group oper_buttons text-right">
  <div class="col-lg-10 col-lg-offset-2">
    '.$strButtons.'
  </div>
</div>
<!-- /Buttons -->

   ';
  }
  //------------------------------------------------------------------
  //------------------------------------------------------------------
  public function getFields(array $listFields, array $datos, $required=false)
  {
    foreach($listFields as $name => $field)
    {
       if(is_array($field)) {
          $this->getField($field[0], $field[1]);
       }
       else {
          $htmInput = $this->getInput($field, $name, $datos[$name], 'text', $required);
          $this->getField($field, $htmInput);
       }
    }
  }
  //------------------------------------------------------------------
  public function getInput($title, $name, $value='', $type='text', $required=false, $flag_placeholder=false)
  {
     $required = ($required)? 'required' : '';

     $placeholder = '';
     if($flag_placeholder) {
        $placeholder = 'placeholder="'.$title.'"';
     }

     return '<input '.$placeholder.' '.$required.' type="'.$type.'" class="form-control" name="'.$name.'" value="'.$value.'">';
  }
  //------------------------------------------------------------------
  public function getField($title, $htmInput)
  {
    ?>
    <div class="form-group">
       <label class="col-sm-3 control-label"><?=$title?></label>
       <div class="col-sm-9"><?=$htmInput?></div>
    </div>
    <?
  }
  //------------------------------------------------------------------
}
