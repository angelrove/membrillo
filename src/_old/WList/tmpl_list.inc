<?
$class_scroll = '';
$styleScroll  = '';
if($this->showScroll == true) {
   $class_scroll = 'setScroll';
   $styleScroll  = 'height:'.$this->height.'px;';
}
?>

<!-- Wlist -->
<script>
var List_msgConfirmDel = '<?=$this->msgConfirmDel?>';
</script>

<div class="List" id="List_<?=$this->id_object?>">
  <? if($this->paging_showOn == 'top'): ?>
    <!-- paginación -->
    <div class="List_paginacion top well well-sm">
       <?=$htmPaginacion?>
    </div>
    <!-- /paginación -->
  <? endif; ?>

  <div class="<?=$class_scroll?>" style="<?=$styleScroll?>">
    <table class="List_tuplas
                  table
                  table-striped
                  table-bordered
                  table-hover"
           param_control="<?=$this->id_object?>"

           param_event-onRow  ="<?=$this->onClickRow?>"

           param_event-new    ="<?=$this->event_new?>"
           param_event-update ="<?=$this->event_update?>"
           param_event-delete ="<?=$this->event_delete?>"
           param_event-detalle="<?=$this->event_detalle?>">
      <thead>
        <?if($this->title) {?>
        <tr><th colspan="<?=$this->num_cols+1?>"><?=$this->title?></th></tr>
        <?}?>
        <tr><?=$htmColumnas?></tr>
      </thead>

      <tbody><?=$htmListDatos?></tbody>
    </table>
  </div>

  <? if($this->paging_showOn == 'bottom'): ?>
    <!-- paginación -->
    <div class="List_paginacion bottom">
      <?=$htmPaginacion?>
    </div>
    <!-- /paginación -->
  <? endif; ?>
</div>
<!-- /Wlist -->
