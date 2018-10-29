<?php
use angelrove\membrillo2\WPage\WPage;
use angelrove\membrillo2\WObjects\WForm\WForm;
use angelrove\membrillo2\WObjectsStatus\Event;
use App\Models\[Sample];

$controlName = 'simple';

// form_data -------
switch(Event::$EVENT) {
  case CRUD_EDIT_NEW:
    $form_data = [Sample]::findEmpty();

    // Default values
    $form_data['name'] = 'My name';
  break;

  case CRUD_EDIT_UPDATE:
    $form_data = [Sample]::findById(Event::$ROW_ID);
  break;
}

$form = new WForm($controlName, $form_data);
$form->setButtons(true, true, true);
?>

<?php WPage::get()?>
  <?php $form->get()?>

    <?=$form->getInput('name', 'Nombre', true)?>
    <?=$form->getInput('profile', 'Profile', true, 'select_array', [1=>'A',1=>'B',1=>'C',])?>

  <?php $form->get_end()?>
<?php WPage::get_end()?>
