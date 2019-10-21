<?php
use App\Models\[Model_parent];
use App\Models\[Model_detail];

$controlName = 'detail';
$controlName_padre = 'parent';

// form_data -------
switch (Event::$EVENT) {
    case CRUD_EDIT_NEW:
        $form_data['[parent_id]'] = $objectsStatus->getRowId($controlName_padre);
        break;

    case CRUD_EDIT_UPDATE:
        $form_data = [Model_detail]::findById(Event::$ROW_ID);
        break;
}

//-------
$form = new WForm($controlName, $form_data);
?>

<?php WPage::get()?>
    <?php $form->get()?>
        <?=$form->fInput('datetime', 'created_at', 'Created at')->readonly()->get()?>
        <?=$form->getInput('[parent_id]', '[Model_parent]', false, 'select', [[Model_parent]::read()])?>
        <?=$form->fInput('text', 'name', Local::$t['Name'])->required()->get()?>
    <?php $form->get_end()?>
<?php WPage::get_end();
