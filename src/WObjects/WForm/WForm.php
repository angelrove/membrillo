<?php
/**
 * WForm
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 */

namespace angelrove\membrillo2\WObjects\WForm;

use angelrove\membrillo2\AppCms;
use angelrove\membrillo2\Messages;
use angelrove\membrillo2\WObjectsStatus\Event;
use angelrove\membrillo2\WObjectsStatus\EventComponent;
use angelrove\membrillo2\WPage\WPage;
use angelrove\utils\CssJsLoad;

use angelrove\membrillo2\WInputs\WInputSelect;
use angelrove\membrillo2\WInputs\WInputCheck;
use angelrove\membrillo2\WInputs\WInputTextarea;


class WForm extends EventComponent
{
    private $title;

    private $datos = array();

    private $onSubmit;
    private $readOnly = false;

    // Buttons
    private $bt_ok     = true;
    private $bt_upd    = false;
    private $bt_del    = false;
    private $bt_cancel = true;

    private $bt_saveNext       = false;
    private $bt_saveNext_label = '';

    private $setButtons_top = false;

    public static $errors = false;

    //------------------------------------------------------------------
    public function __construct($id_object, $data)
    {
        CssJsLoad::set(__DIR__ . '/libs.js');

        //----------
        parent::__construct($id_object);

        $this->datos = $data;
        if (!$this->datos) {
            $strErr = 'ERROR: WForm: el registro solicitado no existe';
            include '404.php';
            exit();
        }

        WPage::add_pagekey('WForm');

        //---------
        $this->parse_event($this->WEvent);
    }
    //--------------------------------------------------------------
    public function getData()
    {
        return $this->datos;
    }
    //--------------------------------------------------------------
    public function parse_event($WEvent)
    {
        switch ($WEvent->EVENT) {
            //----------
            case CRUD_EDIT_UPDATE:
                $this->title .= ' Update';
                break;
            //----------
            case CRUD_EDIT_NEW:
                $this->title .= ' New';
                break;
                //----------
        }

        // If Errors ----
        $this->datos = array_merge($this->datos, $_POST);
    }
    //------------------------------------------------------------------
    // Static
    //------------------------------------------------------------------
    public static function update_setErrors(array $listErrors)
    {
        if (!$listErrors) {
            return;
        }

        self::$errors = $listErrors;

        // Continue with edit
        Event::$REDIRECT_AFTER_OPER = false; // para que no se pierdan los datos recibidos por post

        if (Event::$ROW_ID) {
            Event::setEvent(CRUD_EDIT_UPDATE);
        } else {
            Event::setEvent(CRUD_EDIT_NEW);
        }

        // Highlight errors
        self::update_showErrors($listErrors);
    }
    //------------------------------------------------------------------
    private static function update_showErrors(array $listErrors)
    {
        $js = '';

        // resaltar campos ---
        foreach ($listErrors as $name => $err) {
            Messages::set($err, 'danger');
            $js .= '$("[name=' . $name . ']").css("border", "2px solid red");';
        }

        // foco en el primer input erroneo
        end($listErrors);
        // $js .= '$("[name='.key($listErrors).']").focus();'."\n";

        // Out
        CssJsLoad::set_script('
  $(document).ready(function() {' . $js . '});
    ');
    }
    //------------------------------------------------------------------
    // NO STATIC
    //------------------------------------------------------------------
    public function isUpdate($row_id)
    {
        $this->WEvent->EVENT  = CRUD_EDIT_UPDATE;
        $this->WEvent->ROW_ID = $row_id;
        $this->parse_event($this->WEvent);
    }
    //------------------------------------------------------------------
    public function isInsert()
    {
        $this->WEvent->EVENT = CRUD_EDIT_NEW;
        $this->parse_event($this->WEvent);
    }
    //------------------------------------------------------------------
    public function getFormEvent()
    {
        $event  = CRUD_DEFAULT;
        $oper   = CRUD_OPER_INSERT;
        $row_id = '';

        if ($this->WEvent->EVENT == CRUD_EDIT_UPDATE) {
            $oper   = CRUD_OPER_UPDATE;
            $row_id = $this->WEvent->ROW_ID;
        }

        return array(
            'event'  => $event,
            'oper'   => $oper,
            'row_id' => $row_id,
        );
    }
    //------------------------------------------------------------------
    public function setListenerOnSubmit($onSubmit)
    {
        $this->onSubmit = $onSubmit . '()';
    }
    //------------------------------------------------------------------
    public function set_title($title)
    {
        $this->title = $title;
    }
    //------------------------------------------------------------------
    public function setButtons($bt_ok, $bt_upd, $bt_cancel, $bt_del = false)
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
    public function show_btSaveNext($label = '')
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
        if ($this->setButtons_top) {
            $htmButtons_top = $this->getButtons('TOP');
        }

        //----
        if ($this->readOnly) {
            echo '<form class="form-horizontal">';
            return;
        }

        // Datos evento
        $datosEv = $this->getFormEvent();
        $event   = $datosEv['event'];
        $oper    = $datosEv['oper'];
        $row_id  = $datosEv['row_id'];

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
    public function getButtons($flag = '')
    {
        $bt_aceptar  = '<button type="submit" class="WForm_bfAccept btn btn-primary">' . AppCms::$lang['accept'] . '</button> ' . "\n";
        $bt_guardar  = '<button type="submit" class="WForm_btUpdate btn btn-primary">' . AppCms::$lang['save']   . '</button> ' . "\n";
        $bt_saveNext = '<button type="submit" class="WForm_btInsert btn btn-primary">' . AppCms::$lang['save_and_new'] . '</button> ' . "\n";

        $bt_eliminar = '<button type="button" class="WForm_btDelete btn btn-danger">'  . AppCms::$lang['delete'] . '</button> ';
        $bt_cancelar = '<button type="button" class="WForm_btClose  btn btn-default">' . AppCms::$lang['close'] . '</button>' . "\n";

        $datosEv = $this->getFormEvent();

        if (!$this->bt_ok) {
            $bt_aceptar = '';
        }

        if (!$this->bt_upd) {
            $bt_guardar = '';
        }

        if (!$this->bt_saveNext) {
            $bt_saveNext = '';
        }

        if (!$this->bt_cancel || $flag == 'TOP') {
            $bt_cancelar = '';
        }
        if (!$this->bt_del || $flag == 'TOP' || !$this->WEvent->ROW_ID) {
            $bt_eliminar = '';
        }

        // $strButtons
        $strButtons = $bt_aceptar . $bt_guardar . $bt_eliminar . $bt_saveNext . $bt_cancelar;

        if ($this->readOnly) {
            $strButtons = $bt_cancelar;
        }

        // OUT
        if (!$strButtons) {
            return '';
        }

        return '
<!-- Buttons -->
<div class="form-group oper_buttons text-right">
  <div class="col-lg-10 col-lg-offset-2">
    ' . $strButtons . '
  </div>
</div>
<!-- /Buttons -->

   ';
    }
    //------------------------------------------------------------------
    // Inputs
    //------------------------------------------------------------------
    public function getField($title, $htmInput)
    {
        return '
        <div class="form-group">
           <label class="col-sm-3 control-label">'.$title.'</label>
           <div class="col-sm-9">'.$htmInput.'</div>
        </div>
        ';
    }
    //------------------------------------------------------------------
    public function getInput($name, $title='', $required=false, $type='text', array $params=array())
    {
        if (!$title) {
            $title = $name;
        }
        if (!$type) {
            $type = 'text';
        }

        switch ($type) {
            case 'select':
                $dbTable = $params[0];
                $emptyOption = (isset($params[1]) && $params[1])? '-':'';
                $htmInput = WInputSelect::get2($dbTable, $this->datos[$name], $name, $required, $emptyOption);
                break;

            case 'select_query':
                $sqlQ = $params[0];
                $emptyOption = (isset($params[1]) && $params[1])? '-':'';
                $htmInput = WInputSelect::get($sqlQ, $this->datos[$name], $name, $required, $emptyOption);
                break;

            case 'select_array':
                $values = $params[0];
                $emptyOption = (isset($params[1]) && $params[1])? '-':'';
                $htmInput = WInputSelect::getFromArray($values, $this->datos[$name], $name, $required, '', $emptyOption);
                break;

            case 'checkbox':
                $htmInput = WInputCheck::get($name, '&nbsp;', $this->datos[$name], $required);
                break;

            case 'textarea':
                $maxlength = (isset($params[0]))? $params[0] : '';
                $htmInput = WInputTextarea::get($name, $this->datos[$name], $required, '', $maxlength);
                break;

            case 'text_read':
                $htmInput = '<input disabled class="form-control" value="'.$this->datos[$name].'">';
                break;

            case 'number':
                $extraHtml = $params[0]?? '';
                $htmInput = $this->getInput1($title, $name, $this->datos[$name], $type, $required, false, $extraHtml);
                break;

            case 'url':
                $extra = 'style="display:initial;width:95%"';

                if ($this->datos[$name]) {
                    $title = '<a target="_blank" href="'.$this->datos[$name].'">'.$title.'</a>';
                }

                $htmInput = $this->getInput1($title, $name, $this->datos[$name], $type, $required,
                                             false,
                                             $extra);

                // if ($this->datos[$name]) {
                //     $htmInput .= ' <a target="_blank" href="'.$this->datos[$name].'">'.
                //                     '<i class="fas fa-link fa-lg"></i>'.
                //                  ' </a>';
                // }

                break;

            default:
                // $type_text = (isset($params[0])? $params[0] : 'text';
                $htmInput = $this->getInput1($title, $name, $this->datos[$name], $type, $required);
                break;
        }

        return $this->getField($title, $htmInput);
    }
    //------------------------------------------------------------------
    public function getInput1($title,
                              $name,
                              $value = '',
                              $type = 'text',
                              $required = false,
                              $flag_placeholder = false,
                              $extraHtml='')
    {
        $required = ($required) ? 'required' : '';

        $placeholder = '';
        if ($flag_placeholder) {
            $placeholder = 'placeholder="' . $title . '"';
        }

        switch ($type) {
            case 'datetime-local':
                $value = str_replace(" ", "T", $value);
                break;
        }

        return '<input class="form-control type_'.$type.'"'.
                   ' ' . $placeholder .
                   ' ' . $required .
                   ' ' . $extraHtml .
                   ' type="'  . $type  . '"'.
                   ' name="'  . $name  . '"'.
                   ' value="' . $value . '"'.
               '>';
    }
    //------------------------------------------------------------------
    // OLD
    //------------------------------------------------------------------
    public function getFields(array $listFields, array $data, $required = false)
    {
        foreach ($listFields as $name => $field) {
            if (is_array($field)) {
                echo $this->getField($field[0], $field[1]);
            } else {
                $htmInput = $this->getInput1($field, $name, $data[$name], 'text', $required);
                echo $this->getField($field, $htmInput);
            }
        }
    }
    //------------------------------------------------------------------
}
