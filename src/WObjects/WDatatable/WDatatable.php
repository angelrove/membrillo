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
    private $jsonData;

    private $bt_new = false;
    private $bt_update = false;
    private $bt_delete = false;

    // Events
    private $event_new    = '';
    private $event_update = '';

    private $defaultOrder = '';

    //-------------------------------------------------------
    // PUBLIC
    //-------------------------------------------------------
    public function __construct($id_control, $jsonData, array $columns)
    {
        Vendor::usef('datatables');
        CssJsLoad::set(__DIR__ . '/styles.css');
        CssJsLoad::set(__DIR__ . '/lib.js');

        $this->id_control = $id_control;
        $this->jsonData   = $jsonData;
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
    public function get()
    {
        $id_component = 'datatable_'.$this->id_control;

        // Options links ---
        $href_new    = CrudUrl::get(CRUD_EDIT_NEW, $this->id_control);
        $href_delete = CrudUrl::get(CRUD_OPER_DELETE, $this->id_control);

        // Js ----
        $strColumns = '';
        foreach ($this->columns as $column) {
            $strColumns .= "{ data: '".$column->name."', name: '".$column->title."' },";
        }

        CssJsLoad::set_script(
<<<EOD
var id_component  = '$id_component';
var href_new      = '$href_new';
var show_btNew    = '$this->bt_new';
var show_btUpdate = '$this->bt_update';
var show_btDelete = '$this->bt_delete';

$(document).ready(function() {

    var dataTable = $('#$id_component').DataTable( {
        ajax: '/index_ajax.php?service=read',
        select: false,
        dom: 'Bfrtip',

        buttons: [
            'print',
            'csvHtml5',
            // { text: 'TSV', extend: 'csvHtml5', fieldSeparator: '\t', extension: '.csv' }
        ],

        columns: [
            $strColumns
            {
                className: 'options', data: null, orderable: false, searchable: false,
                render: function(data, type, full, meta) {
                   // if (full.activated) {]

                   var strButtons = '';
                   if(show_btUpdate) {
                       strButtons = strButtons+'<button onclick="WDatatable_onUpdate(\''+id_component+'\', '+data.id+')" class="on_update btn btn-xs btn-primary">Edit</button> ';
                   }
                   if(show_btDelete) {
                       strButtons = strButtons+'<button onclick="WDatatable_onDelete(\''+id_component+'\', '+data.id+')" class="on_delete btn btn-xs btn-danger"><i class="far fa-trash-alt"></i></button> ';
                   }
                   return strButtons;
                }
            },
        ]
    });

    // New button ---
    if(show_btNew) {
        $(".dt-buttons").append(
            '<a href="" onclick="location.href=href_new;return false;" class="btn btn-success"><i class="fa fa-plus"></i> New</a>'
        );
    }

    // Other buttons ---


});
EOD
);

        // Html ---
        $strColumns = '';
        foreach ($this->columns as $column) {
            $strColumns .= '<th>'.$column->title.'</th>';
        }

        // Action ---
        $action = CrudUrl::get('', $this->id_control);

        return <<<EOD
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
