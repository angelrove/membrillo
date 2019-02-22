<?php
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 */

namespace angelrove\membrillo\WObjects\WDatatable;

use angelrove\membrillo\CrudUrl;
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
    private $renderOptions = false;

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
    public function rowOption($event, $title = '')
    {
        $this->rowOptions[] = array(
            'event'    => $event,
            'oper'     => $event,
            'title'    => $title,
        );
    }
    //-------------------------------------------------------
    public function renderOptions()
    {
        $this->renderOptions = true;
    }
    //--------------------------------------------------------------
    // Searcher
    //--------------------------------------------------------------
    public function searcher()
    {
        $action = CrudUrl::get(CRUD_LIST_SEARCH, $this->id_control);

        return <<<EOD
<form class="FormSearch form-inline well well-sm"
      role="search"
      name="search_form"
      method="get"
      action="$action">
EOD;
    }
    //-------------------------------------------------------
    public function searcher_bt()
    {
        echo '&nbsp;<button type="submit" class="btn btn-primary btn-sm">Search</button>';
    }
    //-------------------------------------------------------
    public function searcher_END()
    {
        return '</form>';
    }
    //-------------------------------------------------------
    // GETS
    //-------------------------------------------------------
    private function getJsColumns()
    {
        // Columns ---
        $columns = '';
        foreach ($this->columns as $column) {
            $paramsType = "";
            switch ($column->type) {
                case 'datetime':
                    $paramsType = ",className:'datetime', width:108";
                break;
                case 'price':
                    $paramsType = ",className:'price', width:66";
                break;
                case 'boolean':
                    $paramsType = ",className:'text-center'";
                break;
                case 'render':
                    $paramsType = ",className:'render'";
                break;
            }

            $columns .= " { data: '".$column->name."' $paramsType },\n";
        }

        // Options column ---
        $columns .= " { data: null, orderable:false, searchable:false, targets: -1, className:'options', width:108, defaultContent: btOptions }, \n";

        // OUT ----
        return "[\n$columns]";
    }
    //-------------------------------------------------------
    private function getOptionsBt()
    {
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
        foreach ($this->rowOptions as $data) {
            $btOptions[] = "<button param_event='$data[event]' class='btn btn-xs btn-primary'>$data[title]</button>";
        }

        $listButtons = '';
        if ($btOptions) {
            $listButtons = implode('&nbsp;&nbsp;', $btOptions);
        }

        return $listButtons;
    }
    //-------------------------------------------------------
    public function get()
    {
        $id_component = 'datatable_'.$this->id_control;

        // Js ------
        $dtColumns = $this->getJsColumns();

        $btOptions = $this->getOptionsBt();

        $href_new = '';
        if ($this->bt_new) {
            $href_new = CrudUrl::get(CRUD_EDIT_NEW, $this->id_control);
        }

        // Render types ---
        $colsRender_datetime = array();
        $colsRender_bool     = array();
        $colsRender_relation = array();
        $colsRender_render   = array();
        $colsRender_render_options = ($this->renderOptions)? '-1' : '';

        foreach ($this->columns as $key => $column) {
            if ($column->type == 'datetime') {
                $colsRender_datetime[] = $key;
            }
            elseif ($column->type == 'boolean') {
                $colsRender_bool[] = $key;
            }
            elseif ($column->type == 'relation') {
                $colsRender_relation[] = $key;
            }
            elseif ($column->type == 'render') {
                $colsRender_render[] = $key;
            }
        }

        $colsRender_datetime = implode(',', $colsRender_datetime);
        $colsRender_bool     = implode(',', $colsRender_bool);
        $colsRender_relation = implode(',', $colsRender_relation);
        $colsRender_render   = implode(',', $colsRender_render);

        // Datatables js ------------
        CssJsLoad::set_script(
<<<EOD
var id_component = '$id_component';

var btOptions = "$btOptions";
var dt_cols = $dtColumns;

var colsRender_datetime = [$colsRender_datetime];
var colsRender_bool     = [$colsRender_bool];
var colsRender_relation = [$colsRender_relation];
var colsRender_render   = [$colsRender_render];
var colsRender_render_options = [$colsRender_render_options];

var href_new = '$href_new';
EOD
);

        // Columns head ----
        $strColumnsHead = '';
        foreach ($this->columns as $column) {
            $strColumnsHead .= '<th>'.$column->title.'</th>';
        }

        // Action ----------
        $action = CrudUrl::get('', $this->id_control);

        return <<<EOD
<style>
.btn-group, .btn-group-vertical { display: block; }
.dt-buttons a.btn { margin-left: 10px !important; }
#$id_component td.options,
#$id_component td.datetime {
    white-space: nowrap;
}

table.dataTable td.datetime,
table.dataTable td.price {
    text-align: right;
    font-family: monospace;
}
</style>

<table id="$id_component"
       class="table table-striped table-bordered table-hover"
       data-order='[[ 0, "desc" ]]'
       data-page-length="20"
       style="width:100%"
       param_action="$action">
    <thead><tr>
      $strColumnsHead
      <th></th>
    </tr></thead>
</table>
EOD;

    }
    //--------------------------------------------------------------
}
