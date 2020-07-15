<?php
use App\Models\[Sample];

$controlName = 'simple';

// Data -------
$form_data = [
    'name' => 'My name',
];

if (Event::$EVENT == CRUD_EDIT_UPDATE) {
    $form_data = [Sample]::findOrFail(Event::$row_id);
    // $form_data = User::withTrashed()->findOrFail(Event::$row_id);
}

$form = new WForm($controlName, $form_data);
$form->setButtons(true, true, true);
?>

<?php WPage::get()?>
  <?php $form->get()?>

     <fieldset class="fieldset_old">
        <?=$form->getInput('profile', 'Profile', true, 'select', [User::$profiles])?>
    </fieldset>

    <?=$form->getInput('name', 'Nombre', true)?>
    <?=$form->getInput('profile', 'Profile', false, 'select', [['1'=>'A','2'=>'B','3'=>'C']])?>

  <?php $form->get_end()?>
<?php WPage::get_end()?>
