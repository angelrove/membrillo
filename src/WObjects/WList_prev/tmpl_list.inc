<?
if(isset($this->showScroll) && $this->showScroll == true) {
   $styleScroll = 'overflow:scroll; height:'.$this->height.'px;';
}
?>

<!-- Wlist -->
<script>
var WList_msgConfirmDel = '<?=$this->msgConfirmDel?>';
</script>

<div class="WList" id="WList_<?=$this->control?>">
  <? if($this->paging_showOn == 'top'): ?>
  <!-- paginación -->
  <div class="WList_paginacion top">
    <?=$htmPaginacion?>
  </div>
  <!-- /paginación -->
  <? endif; ?>

  <div style="<?=$styleScroll?>">
  <table class="WList_tuplas" cellpadding="0" cellspacing="0"
         param_control="<?=$this->control?>"

         param_event-onRow  ="<?=$this->onClickRow?>"

         param_event-new    ="<?=$this->event_new?>"
         param_event-update ="<?=$this->event_update?>"
         param_event-delete ="<?=$this->event_delete?>"
         param_event-detalle="<?=$this->event_detalle?>">
    <thead>
      <?if($this->title) {?>
      <tr><th class="title" colspan="20"><?=$this->title?></th></tr>
      <?}?>
      <tr><?=$htmColumnas?></tr>
    </thead>

    <tbody><?=$htmListDatos?></tbody>
  </table>
  </div>

  <? if($this->paging_showOn == 'bottom'): ?>
  <!-- paginación -->
  <div class="WList_paginacion bottom">
    <?=$htmPaginacion?>
  </div>
  <!-- /paginación -->
  <? endif; ?>
</div>
<!-- /Wlist -->
