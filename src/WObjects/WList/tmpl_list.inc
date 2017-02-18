<?
use angelrove\membrillo2\WPage\Frame;
use angelrove\membrillo2\CrudUrl;

$class_scroll = '';
$styleScroll  = '';
if($this->showScroll == true) {
   $class_scroll = 'setScroll';
   $styleScroll  = 'height:'.$this->height.'px;';
}

$action = CrudUrl::get('', $this->id_object);
?>

<!-- Wlist -->
<script>
var List_msgConfirmDel = '<?=$this->msgConfirmDel?>';
</script>

<div class="List" id="List_<?=$this->id_object?>">
  <? if($this->paging_showOn == 'top'): ?>
    <!-- pagination -->
    <div class="List_paginacion top well well-sm">
       <?=$htmPaginacion?>
    </div>
    <!-- /pagination -->
  <? endif; ?>

  <div class="<?=$class_scroll?>" style="<?=$styleScroll?>">
    <table class="List_tuplas
                  table
                  table-striped
                  table-bordered
                  table-hover"
           param_action="<?=$action?>"

           param_event-onRow  ="<?=$this->onClickRow?>"
           param_event-new    ="<?=$this->event_new?>"
           param_event-update ="<?=$this->event_update?>"
           param_event-delete ="<?=$this->event_delete?>"
           param_event-detalle="<?=CRUD_LIST_DETAIL?>">
       <thead>
         <?if($this->title) {?>
           <tr><th class="text-center" colspan="<?=count($this->dbFields)+1?>"><?=$this->title?></th></tr>
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
