<?php
/**
 * WForm
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 */

namespace angelrove\membrillo\WObjects\WForm;

use angelrove\membrillo\Messages;
use angelrove\membrillo\WObjectsStatus\Event;
use angelrove\membrillo\WObjectsStatus\EventComponent;
use angelrove\membrillo\WPage\WPage;
use angelrove\membrillo\WApp\Local;

use angelrove\membrillo\WInputs\WInputSelect;
use angelrove\membrillo\WInputs\WInputCheck;
use angelrove\membrillo\WInputs\WInputTextarea;

use angelrove\utils\CssJsLoad;
use angelrove\utils\UtilsBasic;
use angelrove\membrillo\Login\Login;

class WForm extends EventComponent
{
    private $title;

    private $datos = array();

    private $onSubmit;
    private $readOnly = false;
    private $eventDefault = false;

    // Buttons
    private $bt_ok       = true;
    private $bt_cancel   = true;
    private $bt_upd      = false;
    private $bt_del      = false;
    private $bt_saveNext = false;

    private $setButtons_top = false;

    public static $errors = false;

    //------------------------------------------------------------------
    public function __construct($id_object, array $data, $title='')
    {
        $this->title = $title;

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
    public function set_bt_cancel($flag)
    {
        $this->bt_cancel = $flag;
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
        $bt_aceptar  = '<button type="submit" class="WForm_bfAccept btn btn-primary">' . Local::$t['save'] . '</button> ' . "\n";
        $bt_guardar  = '<button type="submit" class="WForm_btUpdate btn btn-primary">' . Local::$t['save_continue']   . '</button> ' . "\n";
        $bt_cancelar = '<button type="button" class="WForm_btClose  btn btn-default">' . Local::$t['close'] . '</button>' . "\n";

        $datosEv = $this->getFormEvent();

        if (!$this->bt_ok) {
            $bt_aceptar = '';
        }

        if (!$this->bt_upd) {
            $bt_guardar = '';
        }

        if (!$this->bt_cancel || $flag == 'TOP') {
            $bt_cancelar = '';
        }

        if ($flag == 'TOP' || !$this->WEvent->ROW_ID) {
            $this->bt_del = '';
        }

        // $strButtons
        $strButtons = $this->bt_del . $bt_aceptar . $bt_guardar . $this->bt_saveNext . $bt_cancelar;

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
    public function getField($title, $htmInput, $name='')
    {
        return '
        <div class="form-group" id="obj_'.$name.'">
           <label class="col-sm-3 control-label">'.$title.'</label>
           <div class="col-sm-9">'.$htmInput.'</div>
        </div>
        ';
    }
    //------------------------------------------------------------------
    /*
     * $type: select, select_query, select_array, select_object,
     *        checkbox, textarea, text_read, hidden, number, url
     */
    public function getInput($name, $title='', $required=false, $type='text', array $params=array())
    {
        if ($title === false) {

        } elseif ($title == '') {
            $title = $name;
        }

        if (!$type) {
            $type = 'text';
        }

        switch ($type) {
            case 'select':
                $dbTable = $params[0];

                $emptyOption = '';
                if (isset($params[1]) && $params[1]) {
                    $emptyOption = ($params[1] === true)? '-' : $params[1];
                }

                $htmInput = WInputSelect::get2($dbTable, $this->datos[$name], $name, $required, $emptyOption);
                break;

            case 'select_query':
                $sqlQ = $params[0];

                $emptyOption = '';
                if (isset($params[1]) && $params[1]) {
                    $emptyOption = ($params[1] === true)? '-' : $params[1];
                }

                $htmInput = WInputSelect::get($sqlQ, $this->datos[$name], $name, $required, $emptyOption);
                break;

            case 'select_array':
            case 'select_object':
                $values = $params[0];

                $emptyOption = '';
                if (isset($params[1]) && $params[1]) {
                    $emptyOption = ($params[1] === true)? '-' : $params[1];
                }

                $htmInput = WInputSelect::getFromArray($values, $this->datos[$name], $name, $required, '', $emptyOption);
                break;

            case 'checkbox':
                $htmInput = WInputCheck::get($name, '&nbsp;', $this->datos[$name], $required);
                break;

            case 'textarea':
                $maxlength  = (isset($params[0]))? $params[0] : '';
                $attributes = (isset($params[1]))? $params[1] : '';
                $htmInput = WInputTextarea::get($name, $this->datos[$name], $required, '', $maxlength, $attributes);
                break;

            case 'text_read':
                $htmInput = '<input disabled class="form-control" value="'.$this->datos[$name].'">';
                break;

            case 'hidden':
                return '<input type="hidden" name="'.$name.'" value="'.$this->datos[$name].'">';
                break;

            case 'number':
                $extraHtml = $params[0]?? '';
                $extraHtml .= 'style="width:initial"';
                $htmInput = $this->getInput1($title, $name, $this->datos[$name], $type, $required, false, $extraHtml);
                break;

            case 'price':
                $extraHtml = $params[0]?? '';
                $extraHtml .= ' min="0" step=".01" style="width:initial"';
                $htmInput = $this->getInput1($title, $name, $this->datos[$name], 'number', $required, false, $extraHtml);
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
                $extraHtml = $params[0]?? '';
                // $type_text = (isset($params[0])? $params[0] : 'text';
                $htmInput = $this->getInput1($title, $name, $this->datos[$name], $type, $required, false, $extraHtml);
                break;
        }

        if ($title === false) {
            return $htmInput;
        }
        return $this->getField($title, $htmInput, $name);
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
            // See: UtilsBasic::strDateChromeToTimestamp()
            case 'datetime-local':
            case 'datetime':
                $type = 'datetime-local';
                if (is_integer($value)) {
                    $value = self::timestampToDate($value, 'Y-m-d\TH:i', Login::$timezone);
                } else {
                    $value = str_replace(" ", "T", $value);
                }
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
    //---------------------------------------------------
    // Input datetime helpers
    static private function timestampToDate($timestamp, $toFormat='Y-m-d\TH:i', $toTimezone=NULL)
    {
        $datetime = new \DateTime();
        $datetime->setTimestamp($timestamp);

        if ($toTimezone) {
            $datetime->setTimeZone(new \DateTimeZone($toTimezone));
        }

        return $datetime->format($toFormat);
    }
    //---------------------------------------------------
    static public function dateTimeToTimestamp($dateTime)
    {
       $time = false;

       // 2018-01-01T22:02 -------
       if ($date = \DateTime::createFromFormat('Y-m-d\TH:i', $dateTime)) {
       }
       // 2018-01-01T22:02:00 -------
       elseif ($date = \DateTime::createFromFormat('Y-m-d\TH:i:s', $dateTime)) {
       }

       if ($date) {
           return $date->getTimestamp();
       } else {
           throw new \Exception("WForm::dateTimeToTimestamp(): Error processing date!! [$dateTime]");
       }

       return $time;
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
