<?php
use angelrove\membrillo\WObjects\WList\WListColumn;
use angelrove\membrillo\WApp\Local;
use App\Models\[Sample];

$id_control = 'parent';

//-----------------------------
$columns = array(
    new WListColumn('name', Local::$t['Name'], '220'),
);

$listParent = new WList($id_control, [Sample]::read(), $columns);
// $listParent->setDefaultSelected();
$listParent->setDefaultOrder('name');

$listParent->showPagination(false);
$listParent->showDetail(false);
$listParent->showNew();
$listParent->showUpdate(true);
// $listParent->showDelete();
