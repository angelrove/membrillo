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

    //-------------------------------------------------------
    // PUBLIC
    //-------------------------------------------------------
    public function __construct($id_control, $jsonData, array $columns)
    {
        Vendor::usef('datatables');
        CssJsLoad::set(__DIR__ . '/styles.css');
        // CssJsLoad::set(__DIR__ . '/lib.js');

        $this->id_control = $id_control;
        $this->jsonData   = $jsonData;
        $this->columns    = $columns;
    }
    //-------------------------------------------------------
    public function showBtNew()
    {
        $this->bt_new = true;
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
        // $strColumns .= "{data: 'action', name: 'action', orderable: false, searchable: false}";

        CssJsLoad::set_script(
<<<EOD
$(document).ready(function() {
    var dataTable = $('#$id_component').DataTable( {
        ajax: '/index_ajax.php?service=read',
        columns: [
            $strColumns
            {
                "data": null,
                "className": 'options',
                "render": function(data, type, full, meta) {
                   if (full.activated) {
                       return '<button class="btn btn-xs btn-primary pull-right"> Enabled</button>';
                   } else {
                       return '<button class="btn btn-xs btn-danger pull-right"> Disabled</button>';
                   }
                }
            }
        ],
        dom: 'Bfrtip',
        buttons: [
            'print',
            'csvHtml5',
            // { text: 'TSV', extend: 'csvHtml5', fieldSeparator: '\t', extension: '.csv' }
        ]
    });

    // Delete button ---
    $(".dt-buttons").append(
        '<a href="$href_delete" class="btn btn-warning"><i class="fa fa-trash"></i> Delete</a>'
    );

    // New button ---
    if('$this->bt_new') {
        $(".dt-buttons").append(
            ' <a href="$href_new" class="btn btn-success"><i class="fa fa-plus"></i> New</a>'
        );
    }

    // Other buttons ---

// $('#dataTable').on( 'click', 'tbody tr', function () {
//     dataTable.row( this ).delete( {
//         buttons: [
//             { label: 'Cancel', fn: function () { this.close(); } },
//             'Delete'
//         ]
//     } );
// } );

});
EOD
);

        // Html ---
        $strColumns = '';
        foreach ($this->columns as $column) {
            $strColumns .= '<th>'.$column->title.'</th>';
        }

        return '
<table id="'.$id_component.'" class="table table-striped table-bordered table-hover" style="width:100%">
    <thead><tr>
      '.$strColumns.'
      <th></th>
    </tr></thead>
</table>
        ';
    }
    //--------------------------------------------------------------
}
