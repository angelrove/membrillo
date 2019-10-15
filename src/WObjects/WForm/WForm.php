<?php
/**
 * WForm
 *
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 */

namespace angelrove\membrillo\WObjects\WForm;

use angelrove\membrillo\Messages;
use angelrove\membrillo\WObjectsStatus\Event;
use angelrove\membrillo\WObjectsStatus\EventComponent;
use angelrove\membrillo\WPage\WPage;
use angelrove\membrillo\WApp\Local;

use angelrove\utils\CssJsLoad;
use angelrove\utils\UtilsBasic;
use angelrove\membrillo\Login\Login;

use angelrove\membrillo\WObjects\WForm\FormInputs;

class WForm extends EventComponent
{
    // use WFormInputs; // Deprecated

    private $title;

    private $datos = array();

    private $onSubmit;
    private $readOnly = false;
    private $eventDefault = false;

    // Buttons
    private $bt_ok       = true;
    private $bt_ok_label = '';
    private $bt_cancel   = true;
    private $bt_cancel_label = '';
    private $bt_upd      = false;
    private $bt_del      = false;
    private $bt_saveNext = false;

    private $setButtons_top = false;

    public static $errors = false;

    //------------------------------------------------------------------
    public function __construct($id_object, $data, string $title = '')
    {
        $this->title = $title;

        // Data array or Eloquent object
        if (is_array($data)) {
            $this->datos = $data;
        } else {
            $this->datos = $data->toArray();
        }

        //----------
        CssJsLoad::set(__DIR__ . '/libs.js');
        parent::__construct($id_object);
        WPage::add_pagekey('WForm');
        $this->parse_event($this->WEvent);
    }
    //--------------------------------------------------------------
    public function setData(array $data)
    {
        return $this->datos = $data;
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
                $this->title = UtilsBasic::implode(' - ', [$this->title, 'Update']);
                break;
            //----------
            case CRUD_EDIT_NEW:
                $this->title = UtilsBasic::implode(' - ', [$this->title, 'New']);
                break;
            //----------
        }

        // If Errors ----
        $this->datos = array_merge($this->datos, $_POST);
    }
    //------------------------------------------------------------------
    // Static
    //------------------------------------------------------------------
    public static function update_setErrors(array $listErrors, $id = '')
    {
        if (!$listErrors) {
            return;
        }

        self::$errors = $listErrors;

        // Continue with edit
        Event::$REDIRECT_AFTER_OPER = false; // para que no se pierdan los datos recibidos por post

        if ($id) {
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

        if ($this->eventDefault) {
            $event  = $this->eventDefault;
            $oper   = $this->eventDefault;
        }

        return array(
            'event'  => $event,
            'oper'   => $oper,
            'row_id' => $row_id,
        );
    }
    //------------------------------------------------------------------
    public function set_title($title)
    {
        $this->title = $title;
    }
    //------------------------------------------------------------------
    public function set_eventDefault($event)
    {
        $this->eventDefault = $event;
    }
    //------------------------------------------------------------------
    public function setButtons($bt_ok, $bt_upd, $bt_cancel)
    {
        $this->bt_ok     = $bt_ok;
        $this->bt_upd    = $bt_upd;
        $this->bt_cancel = $bt_cancel;
    }
    //------------------------------------------------------------------
    public function set_bt_ok_label($label = '')
    {
        $this->bt_ok_label = $label;
    }
    //------------------------------------------------------------------
    public function set_bt_cancel($flag, $label = '')
    {
        $this->bt_cancel = $flag;
        $this->bt_cancel_label = $label;
    }
    //------------------------------------------------------------------
    public function set_bt_delete($label = '')
    {
        $label = ($label)? $label : '<i class="far fa-trash-alt"></i> '.Local::$t['delete'];
        $this->bt_del = '<button type="button" class="WForm_btDelete btn btn-danger"> '.$label.'</button> ';
    }
    //------------------------------------------------------------------
    public function show_bt_saveNext($label = '')
    {
        if (!$label) {
            $label = Local::$t['save_and_new'];
        }
        $this->bt_saveNext = '<button type="submit" class="WForm_btInsert btn btn-primary">' . $label . '</button> ';
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
    // Form inputs
    //------------------------------------------------------------------
    public function inputContainer(string $title, string $htmInput, string $name = '')
    {
        return FormInputs::inputContainer($title, $htmInput, $name);
    }
    //------------------------------------------------------------------
    public function inputContainer_start(string $title, string $name = '')
    {
        return FormInputs::inputContainer_start($title, $name);
    }

    public function inputContainer_end()
    {
        return FormInputs::inputContainer_end();
    }
    //------------------------------------------------------------------
    /**
     * @param $type string View valid types in 'FormInputs' class:
     *      const HIDDEN    = 'hidden';
     *      const TEXT      = 'text';
     *      const TEXTAREA  = 'textarea';
     *      const SELECT    = 'select';
     *      const CHECKBOX  = 'checkbox';
     *      const RADIOS    = 'radios';
     *      const FILE      = 'file';
     *      const NUMBER    = 'number';
     *      const PRICE     = 'price';
     *      const DATETIME  = 'datetime';
     *      const MONTH     = 'month';
     *      const PERCENTAGE = 'percentage';
     *      const URL       = 'url';
     *      ...
     */
    public function fInput(string $type, string $name = '', string $title = '')
    {
        return new FormInputs($type, $name, $title, ($this->datos[$name])?? '');
    }
    //------------------------------------------------------------------
    // DEPRECATED !!
    public function getField($title, $htmInput, $name = '')
    {
        return FormInputs::inputContainer($title, $htmInput, $name);
    }

    public function input($name, $type = 'text', $title = '', $required = false, array $params = [])
    {
        return $this->getInput($name, $title, $required, $type, $params);
    }

    public function getInput($name, $title = '', $required = false, $type = 'text', array $params = [])
    {
        $value = ($this->datos[$name])?? '';
        $formInput = new FormInputs($type, $name, $title, $value);

        // Input "select" ---
        if ($type == 'select') {
            $formInput->listData($params[0]);

            if (isset($params[1]) && $params[1]) {
                $formInput->placeholder($params[1]);
            }
        } else if ($type == 'text_read') {
            $type == 'text';
            $formInput->readOnly();
        }

        return $formInput->required()->get();
    }
    //------------------------------------------------------------------
    //------------------------------------------------------------------
    // $flag: '', 'top'
    public function getButtons($flag = '')
    {
        $label = ($this->bt_ok_label)? $this->bt_ok_label : Local::$t['save'];
        $bt_enter  = '<button type="submit" class="WForm_bfAccept btn btn-primary" scut_id_object="'.$this->id_object.'">' .
                         $label .
                     '</button> ' . "\n";

        $bt_save   = '<button type="submit" class="WForm_btUpdate btn btn-primary" scut_id_object="'.$this->id_object.'">' .
                         Local::$t['save_continue'] .
                     '</button> ' . "\n";

        $label = ($this->bt_cancel_label)? $this->bt_cancel_label : Local::$t['close'];
        $bt_cancel = '<button type="button" class="WForm_btClose btn btn-default" scut_id_object="'.$this->id_object.'">' .
                         $label .
                     '</button>' . "\n";

        $datosEv = $this->getFormEvent();

        if (!$this->bt_ok) {
            $bt_enter = '';
        }

        if (!$this->bt_upd) {
            $bt_save = '';
        }

        if (!$this->bt_cancel || $flag == 'TOP') {
            $bt_cancel = '';
        }

        if ($flag == 'TOP' || !$this->WEvent->ROW_ID) {
            $this->bt_del = '';
        }

        // $strButtons
        $strButtons = $this->bt_del . $bt_enter . $bt_save . $this->bt_saveNext . $bt_cancel;

        if ($this->readOnly) {
            $strButtons = $bt_cancel;
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
}
