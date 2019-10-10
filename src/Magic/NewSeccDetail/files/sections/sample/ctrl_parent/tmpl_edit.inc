<?php
use angelrove\membrillo\WObjects\WForm\WForm;
use angelrove\membrillo\WObjectsStatus\Event;
use angelrove\membrillo\WApp\Local;
use App\Models\[Sample];

$controlName = 'parent';

// form_data -------
switch (Event::$EVENT) {
    case CRUD_EDIT_NEW:
        $form_data = [Sample]::findEmpty();
        break;

    case CRUD_EDIT_UPDATE:
        $form_data = [Sample]::findById(Event::$ROW_ID);
        break;
}

$form = new WForm($controlName, $form_data);
$form->setButtons(true, false, true);
?>

<?php WPage::get()?>
    <?php $form->get()?>
        <?=$form->getInput('name', Local::$t['Name'], true)?>
    <?php $form->get_end()?>
<?php WPage::get_end();
