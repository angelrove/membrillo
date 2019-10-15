<?php
use App\Models\[Sample];

$controlName = 'simple';

// form_data -------
$form_data = [];
$form_data['name'] = 'My name';
if(Event::$EVENT == CRUD_EDIT_UPDATE) {
    $form_data = [Sample]::findById(Event::$row_id);
}

$form = new WForm($controlName, $form_data);
$form->setButtons(true, true, true);
?>

<?php WPage::get()?>
  <?php $form->get()?>

    <?=$form->getInput('name', 'Nombre', true)?>
    <?=$form->getInput('profile', 'Profile', false, 'select', [['1'=>'A','2'=>'B','3'=>'C']])?>

  <?php $form->get_end()?>
<?php WPage::get_end()?>
