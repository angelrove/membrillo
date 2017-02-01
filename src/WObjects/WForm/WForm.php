<?
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 */

namespace angelrove\membrillo\WObjects\WForm;

use angelrove\utils\Db_mysql;
use angelrove\utils\CssJsLoad;


class WForm
{
  private $control;
  private $multipart;
  private $onSubmit;
  private $readOnly = false;
  private $isUpdate = false;

  // Buttons
  private $bt_ok     = true;
  private $bt_upd    = false;
  private $bt_del    = false;
  private $bt_cancel = true;

  private $bt_saveNext = false;
  private $bt_saveNext_label = '';

  private $setButtons_top = false;

  //------------------------------------------------------------------
  public function __construct($control, $multipart=false)
  {
    $this->control   = $control;
    $this->multipart = $multipart;

    //----------
    CssJsLoad::set(__DIR__.'/styles.css');
    CssJsLoad::set(__DIR__.'/libs.js');
  }
  //------------------------------------------------------------------
  public function auto_init($DB_TABLE, $sqlQ='')
  {
    global $seccCtrl, $errors;

    $datos = array();
    $title = ' - Insertar -';

    if($seccCtrl->EVENT == 'editUpdate') {
       $title = ' - Modificar -';

       // Datos
       if($sqlQ) {
          $datos = Db_mysql::getRow($sqlQ, false);
       } else {
          $id = $seccCtrl->ROW_ID; //$id = $seccCtrl->getRowId($this->control);
          $datos = Db_mysql::getRow(GenQuery::selectRow($DB_TABLE, $id), false);
       }
    }
    WApplication::$title = $seccCtrl->title . $title;

    // Errors
    if($errors) {
       $datos = array_merge($datos, $_POST);
    }

    return $datos;
  }
  //------------------------------------------------------------------
  /* Definir una función onSubmit */
  public function setListenerOnSubmit($onSubmit) {
    $this->onSubmit = $onSubmit.'()';
  }
  //------------------------------------------------------------------
  public function isUpdate($row_id) {
    $this->isUpdate = $row_id;
  }
  //------------------------------------------------------------------
  public function setButtons($bt_ok, $bt_upd, $bt_cancel, $bt_del=false) {
    $this->bt_ok     = $bt_ok;
    $this->bt_upd    = $bt_upd;
    $this->bt_del    = $bt_del;
    $this->bt_cancel = $bt_cancel;
  }
  //------------------------------------------------------------------
  public function show_btSaveNext($label='') {
    $this->bt_saveNext       = true;
    $this->bt_saveNext_label = $label;
  }
  //---------------------------------------------------------------------
  public function setButtons_top() {
    $this->setButtons_top = true;
  }
  //---------------------------------------------------------------------
  public function setReadOnly($isReadonly) {
    $this->readOnly = $isReadonly;
  }
  //------------------------------------------------------------------
  //------------------------------------------------------------------
  public function getWForm() {
    // setButtons_top ---
    $htmButtons = '';
    if($this->setButtons_top) {
       $htmButtons = $this->getButtons('TOP');
       $htmButtons = '<tr><td align="right" colspan="10">'.$htmButtons.'</td></tr>';
    }

    //----
    if($this->readOnly) {
       echo '<table class="WForm" cellspacing="0" cellpadding="0">'; return;
    }

    global $CONFIG_APP, $seccCtrl;

    // EVENT
    $event = 'form_insert';
    $oper  = 'insert';

    /*echo '$seccCtrl->CONTROL: '   .$seccCtrl->CONTROL.
         '<br>$this->control: '   .$this->control.
         '<br>$seccCtrl->EVENT: ' .$seccCtrl->EVENT.
         '<br>$seccCtrl->getEvent($this->control): '.$seccCtrl->getEvent($this->control).
         '<br>$this->isUpdate:'   .$this->isUpdate;*/

    $row_id = '';
    if(($seccCtrl->CONTROL == $this->control && $seccCtrl->getEvent($this->control) == 'editUpdate') ||
       $this->isUpdate)
    {
       $event = 'form_update';
       $oper  = 'update';
       if($this->isUpdate) $row_id = $this->isUpdate;
       else                $row_id = $seccCtrl->getRowId($this->control);
    }

    // Multipart
    $strMultipart = ($this->multipart)? 'enctype="multipart/form-data"' : '';

    // Out
    echo <<<EOD
   <script>
   //-----------------------------------
   // shortcuts
   //-----------------------------------
   $(document).keydown(function(e)
   {
     //----------------
     // Esc
     if(e.keyCode == 27)
     {
        closeWForm_$this->control();
        return false;
     }
     //----------------

EOD;
if($this->bt_ok || $this->bt_upd) {
    echo <<<EOD

     //----------------
     // Ctrl+Enter
     if(e.keyCode == 13 && e.ctrlKey)
     {
        var focused = document.activeElement;
        var focused_type = $(focused).attr('type');
        //var focused_tag = $(focused).get(0).tagName; // INPUT
        if(focused_type == 'text') {
           submitWForm_$this->control();
        }

        return false;
     }
     //----------------

EOD;
}

    echo <<<EOD
   });
   //-----------------------------------

   //-----------------------------------
   // Other
   //-----------------------------------
   function closeWForm_$this->control()
   {
     //var res = confirm("¿Seguro?");
     var res = true;
     if(res == true) {
        window.location = "?CONTROL=$seccCtrl->CONTROL&EVENT=form_close";
     } else {
        return false;
     }
   }
   //-------------------
   function submitWForm_$this->control(event)
   {
     var formEdit = document.getElementById('form_edit_$this->control');

     if(event != '') {
        formEdit.EVENT.value = event;
     }

     // Submit
     //formEdit.action = './?CONTROL=$this->control&EVENT='+formEdit.EVENT.value+'&OPER='+formEdit.OPER.value+'&ROW_ID='+formEdit.ROW_ID.value;
     formEdit.action = './?CONTROL=$this->control&EVENT='+formEdit.EVENT.value+'&ROW_ID='+formEdit.ROW_ID.value;
     formEdit.submit();
   }
   //-------------------
   function deleteWForm_$this->control()
   {
     var formEdit = document.getElementById('form_edit_$this->control');

     formEdit.EVENT.value = 'form_delete';
     formEdit.OPER.value  = 'delete';

     // Submit
     formEdit.action = './?CONTROL=$this->control&EVENT='+formEdit.EVENT.value+'&OPER='+formEdit.OPER.value+'&ROW_ID='+formEdit.ROW_ID.value;
     formEdit.submit();
   }
   //-----------------------------------
   </script>

   <form id="form_edit_$this->control" name="form_edit_$this->control" class="WForm_form" method="POST" action="" $strMultipart>
   <input type="hidden" name="CONTROL" value="$this->control">
   <input type="hidden" name="EVENT"   value="$event">
   <input type="hidden" name="OPER"    value="$oper">
   <input type="hidden" id="ROW_ID" name="ROW_ID"  value="$row_id">

    <table class="WForm" cellspacing="0" cellpadding="0">
      $htmButtons
EOD;
  }
  //------------------------------------------------------------------
  public function getWForm_END()
  {
    $htmButtons = $this->getButtons();

    if($this->readOnly) {
       echo '
          <tr><td align="right" colspan="10">'.$htmButtons.'</td></tr>
        </table>';
       return;
    }

    echo <<<EOD
       <tr><td class="oper_buttons" align="right" colspan="10">$htmButtons</td></tr>
     </table>
    </form>
EOD;
  }
  //------------------------------------------------------------------
  // $flag: '', 'top'
  public function getButtons($flag='')
  {
   global $seccCtrl, $LOCAL;

   $strOnsubmit = ($this->onSubmit)? "if($this->onSubmit)" : '';

   $str_js = <<<EOD
   <script>
   $(document).ready(function() {
     //-----------------------------------
     // EVENTOS
     //-----------------------------------
     $("#WForm_btAceptar$flag").click(function() {
       //$(this).attr('disabled', 'disabled');
       $strOnsubmit submitWForm_$this->control('');
     });
     $("#WForm_btUpdate$flag").click(function() {
       $strOnsubmit submitWForm_$this->control('editUpdate');
     });
     $("#WForm_btDelete$flag").click(function() {
       $strOnsubmit deleteWForm_$this->control();
     });
     $("#WForm_btInsert$flag").click(function() {
       $strOnsubmit submitWForm_$this->control('editNew');
     });
     $("#WForm_btClose$flag").click(function() {
       closeWForm_$this->control();
     });
     //-----------------------------------
   });
   </script>
EOD;

   $bt_aceptar  = '<input type="button" id="WForm_btAceptar'.$flag.'" value="'.$LOCAL['WForm_aceptar'].'">';
   $bt_guardar  = '<input type="button" id="WForm_btUpdate' .$flag.'" value="'.$LOCAL['WForm_update'].'">';
   $bt_eliminar = '<input type="button" id="WForm_btDelete' .$flag.'" value="Eliminar">';
   $bt_saveNext = '<input type="button" id="WForm_btInsert' .$flag.'" value="Insertar otro &raquo;">';
   $bt_cancelar = '<input type="button" id="WForm_btClose'  .$flag.'" value="'.$LOCAL['WForm_close'].'">';

   if(!$this->bt_ok)  $bt_aceptar  = '';
   if(!$this->bt_upd) $bt_guardar  = '';
   if(!$this->bt_saveNext) $bt_saveNext = '';

   if(!$this->bt_del      || $flag == 'TOP') $bt_eliminar = '';
   if(!$this->bt_cancel   || $flag == 'TOP') $bt_cancelar = '';
   $strButtons = $bt_aceptar.$bt_guardar.$bt_eliminar.$bt_saveNext.$bt_cancelar;

   // ReadOnly
   if($this->readOnly) $strButtons = $bt_cancelar;

   // OUT
   if(!$strButtons) {
      return '';
   }

   return "
     <!-- Botones -->
     $str_js
     $strButtons
     <!-- /Botones -->
   ";
  }
  //------------------------------------------------------------------
}
?>
