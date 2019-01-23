<?php
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 */

namespace angelrove\membrillo\WObjects\WDatatable;

// use angelrove\membrillo\CrudUrl;
// use angelrove\membrillo\WApp\Local;
use angelrove\utils\CssJsLoad;
use angelrove\utils\Vendor;

class WDatatable
{
    private $id_control;
    private $columns;
    private $jsonData;

    //-------------------------------------------------------
    // PUBLIC
    //-------------------------------------------------------
    public function __construct($id_control, $jsonData, array $columns)
    {
        // CssJsLoad::set(__DIR__ . '/style.css');
        // CssJsLoad::set(__DIR__ . '/lib.js');
        Vendor::usef('datatables');

        $this->id_control = $id_control;
        $this->jsonData   = $jsonData;
        $this->columns    = $columns;
    }
    //-------------------------------------------------------
    public function get()
    {
        $id_component = 'datatable_'.$this->id_control;

        // Js ----
        $strColumns = '';
        foreach ($this->columns as $column) {
            $strColumns .= "{ data: '".$column->name."', name: '".$column->title."' },";
        }
        // $strColumns .= "{data: 'action', name: 'action', orderable: false, searchable: false}";

        CssJsLoad::set_script("
$(document).ready(function() {
    $('#$id_component').DataTable( {
        'ajax': '/index_ajax.php?service=read',
        'columns': [
            $strColumns
        ]
    } );
} );
        ");

        // Html ---
        $strColumns = '';
        foreach ($this->columns as $column) {
            $strColumns .= '<th>'.$column->title.'</th>';
        }

        return '
<table id="'.$id_component.'" class="table table-bordered">
    <thead><tr>
      '.$strColumns.'<th></th>
    </tr></thead>
</table>
        ';
    }
    //--------------------------------------------------------------
}
