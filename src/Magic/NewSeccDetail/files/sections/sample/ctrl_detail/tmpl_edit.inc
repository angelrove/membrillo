<?php
use angelrove\membrillo\WInputs\WInputFile\WInputFile;
use App\Models\[Model_parent];
use App\Models\[Model_detail];

$controlName = 'detail';
$controlName_padre = 'parent';

// form_data -------
switch (Event::$EVENT) {
    case CRUD_EDIT_NEW:
        $form_data = [Model_detail]::findEmpty();
        $form_data['[parent_id]'] = $objectsStatus->getRowId($controlName_padre);
        break;

    case CRUD_EDIT_UPDATE:
        $form_data = [Model_detail]::findById(Event::$ROW_ID);
        $login_type = 'text_read';
        break;
}

//-------
$form = new WForm($controlName, $form_data);
?>

<?php WPage::get()?>
    <?php $form->get()?>
        <?=$form->getInput('created_at', 'Created at', false, 'text_read')?>
        <?=$form->getInput('[parent_id]', '[Model_parent]', false, 'select_query', [[Model_parent]::read()])?>
        <?=$form->getInput('name', Local::$t['Name'], true)?>
    <?php $form->get_end()?>
<?php WPage::get_end();
