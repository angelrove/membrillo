<?php
use angelrove\membrillo\WPage\WPage;
use angelrove\membrillo\WObjects\WList\WList;
use angelrove\membrillo\WObjects\WList\WListColumn;
use App\Models\[Sample];

$controlName = 'simple';

//-----------------------------
$campos = array(new WListColumn('created_at', 'Created at', '90', 'center'),
                new WListColumn('name',  'Name',  '220'),
                );

$htmList = new WList($controlName, [Sample]::read(), $campos);
$htmList->setDefaultOrder('name');

$htmList->showNew();
$htmList->showUpdate();
$htmList->showDelete();
?>

<?php WPage::get()?>
  <?=$htmList->get()?>
<?php WPage::get_end()?>
