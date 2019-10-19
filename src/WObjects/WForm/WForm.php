<?php
/**
 * WForm
 *
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 */

namespace angelrove\membrillo\WObjects\WForm;

use angelrove\membrillo\WObjectsStatus\EventComponent;
use angelrove\membrillo\WPage\WPage;
use angelrove\membrillo\WApp\Local;
use angelrove\utils\CssJsLoad;
use angelrove\utils\UtilsBasic;

class WForm extends EventComponent
{
    use InputsTrait;
    use ErrorsTrait;

    private $title;
    private $readOnly = false;
    private $eventDefault = false;

    // Buttons
    private $setButtons_top = false;
    private $bt_ok       = true;
    private $bt_ok_label = '';
    private $bt_cancel   = true;
    private $bt_cancel_label = '';
    private $bt_upd      = false;
    private $bt_del      = false;
    private $bt_saveNext = false;

    //------------------------------------------------------------------
    public function __construct($id_object, $inputData = [], string $title = '')
    {
        parent::__construct($id_object);
        $this->title = $title;
        $this->parseEvent($this->WEvent);

        // Data ----
        $this->setData($inputData);

        //-----
        WPage::add_pagekey('WForm');
        CssJsLoad::set(__DIR__ . '/libs.js');
    }
    //--------------------------------------------------------------
    // Events
    //--------------------------------------------------------------
    public function parseEvent($WEvent)
    {
        switch ($WEvent->EVENT) {
            case CRUD_EDIT_UPDATE:
                $this->title = UtilsBasic::implode(' - ', [$this->title, 'Update']);
                break;

            case CRUD_EDIT_NEW:
                $this->title = UtilsBasic::implode(' - ', [$this->title, 'New']);
                break;
        }
    }
    //------------------------------------------------------------------
    public function isUpdate($row_id)
    {
        $this->WEvent->EVENT  = CRUD_EDIT_UPDATE;
        $this->WEvent->ROW_ID = $row_id;
        $this->parseEvent($this->WEvent);
    }
    //------------------------------------------------------------------
    public function isInsert()
    {
        $this->WEvent->EVENT = CRUD_EDIT_NEW;
        $this->parseEvent($this->WEvent);
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
    public function title($title)
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
