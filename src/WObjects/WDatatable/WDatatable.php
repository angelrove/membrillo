<?php
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 */

namespace angelrove\membrillo\WObjects\WDatatable;

use angelrove\membrillo\CrudUrl;
// use angelrove\membrillo\WApp\Local;
use angelrove\utils\CssJsLoad;
use angelrove\utils\Vendor;

class WDatatable
{
    private $id_control;
    private $columns;

    private $bt_new = false;
    private $bt_update = false;
    private $bt_delete = false;

    private $rowOptions = array();

    // Events
    private $event_new    = '';
    private $event_update = '';

    private $defaultOrder = '';

    //-------------------------------------------------------
    // PUBLIC
    //-------------------------------------------------------
    public function __construct($id_control, array $columns)
    {
        Vendor::usef('datatables');
        CssJsLoad::set(__DIR__ . '/styles.css');
        CssJsLoad::set(__DIR__ . '/lib.js');

        $this->id_control = $id_control;
        $this->columns    = $columns;
    }
    //-------------------------------------------------------
    public function showNew()
    {
        $this->event_new = CRUD_EDIT_NEW;
        $this->bt_new = true;
    }
    //-------------------------------------------------------
    public function showUpdate()
    {
        $this->event_update = CRUD_EDIT_UPDATE;
        $this->bt_update = true;
    }
    //-------------------------------------------------------
    public function showDelete()
    {
        $this->bt_delete = true;
    }
    //-------------------------------------------------------
    public function setDefaultOrder($defaultOrder)
    {
        $this->defaultOrder = $defaultOrder;
    }
    //-------------------------------------------------------
    public function setRowOption($event, $title = '')
    {
        $this->rowOptions[$event] = array(
            'event'    => $event,
            'oper'     => $event,
            'title'    => $title,
        );
    }
    //-------------------------------------------------------
    // GETS
    //-------------------------------------------------------
    private function getJsColumns()
    {
        // Columns ---
        $columns = '';
        foreach ($this->columns as $column) {
            $columns .= "{ data: '".$column->name."', name: '".$column->title."' },\n";
        }

        // Column options ---
        $strOptions = '';
        $btOptions = array();

        // edit
        if ($this->bt_update) {
            $btOptions[] = "<button param_event='$this->event_update' class='btn btn-xs btn-default'><i class='far fa-edit fa-lg'></i></button>";
        }
        // delete
        if ($this->bt_delete) {
            $btOptions[] = "<button param_event='delete' class='btn btn-xs btn-danger'><i class='far fa-trash-alt fa-lg'></i></button>";
        }
        // User options
        foreach ($this->rowOptions as $event => $data) {
            $btOptions[] = "<button param_event='$event' class='btn btn-xs btn-primary'>$data[title]</button>";
        }

        if ($btOptions) {
            $listButtons = implode('&nbsp;&nbsp;', $btOptions);

            $strOptions = "{
                data: null,
                orderable:  false,
                searchable: false,
                targets: -1,
                className: 'options',
                defaultContent: \"$listButtons\",
            } \n";
        }

        // OUT ----
        return "[\n$columns $strOptions]";
    }
    //-------------------------------------------------------
    public function get()
    {
        $id_component = 'datatable_'.$this->id_control;

        // Js ------
        $strColumns = $this->getJsColumns();

        $href_new = '';
        if ($this->bt_new) {
            $href_new = CrudUrl::get(CRUD_EDIT_NEW, $this->id_control);
        }

        // Render types
        $colsRender_datetime = array();
        $colsRender_bool = array();
        foreach ($this->columns as $key => $column) {
            if ($column->type == 'datetime') {
                $colsRender_datetime[] = $key;
            }
            elseif ($column->type == 'boolean') {
                $colsRender_bool[] = $key;
            }
        }
        $colsRender_datetime = implode(',', $colsRender_datetime);
        $colsRender_bool = implode(',', $colsRender_bool);

        CssJsLoad::set_script("
 var id_component = '$id_component';
 var dt_cols = $strColumns;
 var colsRender_datetime = [$colsRender_datetime];
 var colsRender_bool = [$colsRender_bool];
 var href_new = '$href_new';
");

        // Html ----
        $strColumns = '';
        foreach ($this->columns as $column) {
            $strColumns .= '<th>'.$column->title.'</th>';
        }

        $action = CrudUrl::get('', $this->id_control);

        return <<<EOD
<style>
#$id_component td.options {
    white-space: nowrap;
}
</style>

<table id="$id_component"
       data-order='[[ 0, "desc" ]]'
       data-page-length="20"
       class="table table-striped table-bordered table-hover"
       style="width:100%"
       param_action="$action">
    <thead><tr>
      $strColumns
      <th></th>
    </tr></thead>
</table>
EOD;

    }
    //--------------------------------------------------------------
}
