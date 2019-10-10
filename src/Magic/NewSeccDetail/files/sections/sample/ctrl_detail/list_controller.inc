<?php
use angelrove\membrillo\WObjects\WList\WList;
use angelrove\membrillo\WObjects\WList\WListColumn;
use angelrove\membrillo\WApp\Local;
use App\Models\[Model_detail];

global $objectsStatus;

$controlName = 'detail';
$controlName_parent = 'parent';

$id_padre = $objectsStatus->getRowId($controlName_parent);

// Filtros --------------------
$filtros = $objectsStatus->getDatos($controlName, [
    'f_parent' => 'NULL',
    'f_text' => '',
    'f_deleted' => 0,
]);

if ($id_padre) {
    $filtros['f_parent'] = $id_padre;
}

// Wlist ----------------------
$columns = array(
    new WListColumn('name', Local::$t['Name']),
);

if ($filtros['f_deleted']) {
    $columns[] = new WListColumn('deleted_at_format', 'Deleted at');
}

$listDetail = new WList($controlName, [Model_detail]::list($filtros), $columns);
$listDetail->setDefaultOrder('name');
$listDetail->showPagination(false);
$listDetail->showNew();
$listDetail->showUpdate();

if ($filtros['f_deleted']) {
    $listDetail->setBtOpc('undelete', '<i class="fas fa-trash-restore-alt fa-lg"></i>');
} else {
    $listDetail->showDelete();
}

//---------------------------
CssJsLoad::set_script(
<<<EOD
   $(document).ready(function() {
     // Filtros ----------
     $(".FormSearch select, .FormSearch input:checkbox").change(function() {
        $(".FormSearch").submit();
     });
     //-------------------
   });
EOD
);
