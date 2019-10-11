<?php
if (!$this->paging_show) {
    return;
}
?>

<div class="List_paginacion well well-sm clearfix">
     <div class="pull-left">
        <?=$htmPaginacion['resume']?>
     </div>

     <div class="pull-right">
        <ul class="pagination">
            <?=$htmPaginacion['pages']?>
        </ul>
     </div>
</div>
