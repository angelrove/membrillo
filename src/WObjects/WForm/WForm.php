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
use angelrove\membrillo2\WPage\WPage;
use angelrove\membrillo2\Messages;


class WForm extends EventComponent
{
  private $sql_row = '';
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
  public function __construct($id_object, $sql_row='')
  {
    CssJsLoad::set(__DIR__.'/libs.js');

    //----------
    parent::__construct($id_object);

    $this->sql_row = $sql_row;

    // Title ---
    global $seccCtrl;
    if(!WPage::$title) {
       WPage::$title = $seccCtrl->title;
    }

    //---------
    $this->parse_event($this->WEvent);
  }
  //--------------------------------------------------------------
  public function parse_event($WEvent)
  {
    switch($WEvent->EVENT) {
      //----------
      case 'editUpdate':
        WPage::$title .= ' - Update';
        $this->datos = Db_mysql::getRow($this->sql_row);
        if(!$this->datos) {
           Messages::set("Error: El registro solicitado no existe", 'danger');
           return false;
        }
      break;
      //----------
      case 'editNew':
        WPage::$title .=  ' - New';
        //Db_mysql::getList("SHOW COLUMNS FROM $db_table");
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
        Messages::set("Guardado correctamente.");
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
    $js .= '$("[name='.key($listErrors).']").focus();'."\n";

    // Out
    CssJsLoad::set_script('$(document).ready(function() {'.$js.'});');
  }
  //------------------------------------------------------------------
  //------------------------------------------------------------------
  public function isUpdate($row_id)
  {
    $this->WEvent->EVENT  = 'editUpdate';
    $this->WEvent->ROW_ID = $row_id;
    $this->parse_event($this->WEvent);
  }
  //--------------------------------------------------------------
  function getDatos()
  {
    return $this->datos;
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
  public function setButtons($bt_ok, $bt_upd, $bt_cancel, $bt_del=false)
  {
    $this->bt_ok     = $bt_ok;
    $this->bt_upd    = $bt_upd;
    $this->bt_del    = $bt_del;
    $this->bt_cancel = $bt_cancel;
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
    $htmButtons = '';
    if($this->setButtons_top) {
       $htmButtons = $this->getButtons('TOP');
    }

    //----
    if($this->readOnly) {
       echo '<form class="form-horizontal">';
       return;
    }

    // Multipart
    // $strMultipart = ($this->multipart)? 'enctype="multipart/form-data"' : '';
    $strMultipart = 'enctype="multipart/form-data"';

    // Datos evento
    $datosEv = $this->getFormEvent();
    $event  = $datosEv['event'];
    $oper   = $datosEv['oper'];
    $row_id = $datosEv['row_id'];

    // Out
    $isUpdate = ($this->bt_ok || $this->bt_upd) ? 'true' : 'false';

    echo <<<EOD
   <script>
   var scut_id_object = '$this->id_object';
   var scut_close     = '$this->bt_cancel';
   </script>

   <form class="WForm form-horizontal"
         id="form_edit_$this->id_object"
         name="form_edit_$this->id_object"
         onsubmit = "WForm_submit('$this->id_object', '')"
         method   = "POST"
         action   = ""
         $strMultipart>
   <input type="hidden" name="CONTROL" value="$this->id_object">
   <input type="hidden" name="EVENT"   value="$event">
   <input type="hidden" name="OPER"    value="$oper">
   <input type="hidden" name="ROW_ID"  value="$row_id">

   $htmButtons

EOD;
  }
  //------------------------------------------------------------------
  public function get_end()
  {
    echo $this->getButtons();

    if(!$this->readOnly) {
       echo '</form>';
    }
  }
  //------------------------------------------------------------------
  // $flag: '', 'top'
  public function getButtons($flag='')
  {
   global $app;

   $strOnsubmit = ($this->onSubmit)? "if($this->onSubmit)" : '';

   $str_js = <<<EOD
//-----------------------------------
// WForm EVENTS
$(document).ready(function()
{
  $("#WForm_btAceptar$flag").click(function() {
    //$(this).attr('disabled', 'disabled');
    $strOnsubmit WForm_submit('$this->id_object', '');
  });
  $("#WForm_btUpdate$flag").click(function() {
    $strOnsubmit WForm_submit('$this->id_object', 'editUpdate');
  });
  $("#WForm_btDelete$flag").click(function() {
    $strOnsubmit WForm_delete('$this->id_object');
  });
  $("#WForm_btInsert$flag").click(function() {
    $strOnsubmit WForm_submit('$this->id_object', 'editNew');
  });
  $("#WForm_btClose$flag").click(function() {
    WForm_close('$this->id_object');
  });
});
//-----------------------------------
EOD;

   CssJsLoad::set_script($str_js);

   $bt_aceptar  = '<button type="submit" class="btn btn-primary" id="WForm_btAceptar'.$flag.'">'.$app->lang['accept'].'</button> '."\n";
   $bt_guardar  = '<button type="button" class="btn btn-primary" id="WForm_btUpdate'.$flag.'">'.$app->lang['save'].'</button> '."\n";
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
<!-- Botones -->
<div class="form-group oper_buttons text-right">
  <div class="col-lg-10 col-lg-offset-2">
    '.$strButtons.'
  </div>
</div>
<!-- /Botones -->

   ';
  }
  //------------------------------------------------------------------
  //------------------------------------------------------------------
  /*
  public function setFieldsText($listFields, $datos)
  {
    foreach($listFields as $name => $title) {
       $htmInput = '<input type="text" class="form-control" name="'.$name.'" value="'.$datos[$name].'">';
       $this->setField($title, $htmInput);
    }
  }
  */
  //------------------------------------------------------------------
  public function setFields(array $listFields, array $datos)
  {
    foreach($listFields as $key => $field) {
       if(is_array($field)) {
          $this->setField($field[0], $field[1]);
       }
       else {
          $htmInput = '<input type="text" class="form-control" name="'.$key.'" value="'.$datos[$key].'">';
          $this->setField($field, $htmInput);
       }
    }
  }
  //------------------------------------------------------------------
  public function setField($title, $htmInput)
  {
    ?>
    <div class="form-group">
       <label class="col-sm-2 control-label"><?=$title?></label>
       <div class="col-sm-10"><?=$htmInput?></div>
    </div>
    <?
  }
  //------------------------------------------------------------------
}
