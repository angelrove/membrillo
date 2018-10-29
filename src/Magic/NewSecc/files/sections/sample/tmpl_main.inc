<?php
use angelrove\membrillo2\WPage\WPage;
use angelrove\membrillo2\WObjects\WList\WList;
use angelrove\membrillo2\WObjects\WList\WListColumn;
use App\Models\User;


$controlName = 'simple';

//-----------------------------
$campos = array(new WListColumn('created_at', 'Created at', '90', 'center'),
                new WListColumn('name',  'Name',  '220'),
                );

$htmList = new WList($controlName, User::read(), $campos);

$htmList->setDefaultOrder('profile');
// $htmList->showPagination(false);
$htmList->showNew();
$htmList->showUpdate();
$htmList->showDelete();
?>

<?php WPage::get()?>
  <?=$htmList->get()?>
<?php WPage::get_end()?>
