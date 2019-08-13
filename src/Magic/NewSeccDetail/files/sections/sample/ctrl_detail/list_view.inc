<?php
use angelrove\membrillo\WApp\Local;
use angelrove\membrillo\WInputs\WInputCheck;
?>

<!-- Search -->
<?=$listDetail->searcher($controlName)?>
    <?php $deltext = ($filtros['f_text'])? '<a href="#" class="clear_search"><i class="fas fa-times fa-lg"></i></a>' : ''; ?>

    <input type="text"
         class="form-control input-sm"
         name="f_text"
         placeholder="Search"
         autocomplete="off"
         value="<?=$filtros['f_text']?>"> <?=$deltext?> &nbsp;

    <?=WInputCheck::get('f_deleted', Local::$t['Deleted'], $filtros['f_deleted'])?>
<?=$listDetail->searcher_END()?>
<!-- /Search -->

<?=$listDetail->get()?>
