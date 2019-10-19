<?php
use angelrove\FormInputs\FormInputs;
use angelrove\membrillo\WApp\Local;
?>

<!-- Search -->
<?=$listDetail->searcher()?>
    <?=$htmList->inputSearch()?>
    <?=FormInputs::check('f_deleted', $filtros['f_deleted'])->label(Local::$t['Deleted'])->get()?>
<?=$listDetail->searcher_END()?>
<!-- /Search -->

<?=$listDetail->get()?>
