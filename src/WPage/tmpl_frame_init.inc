
<!-- WFrame -->
<div class="WFrame panel panel-default">
    <div class="panel-heading">
      <? if($params['showClose']) { ?>
        <button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <? } ?>
      <? if($params['title']) { ?>
        <div class="panel-title"><?=$params['title']?> &nbsp;</div>
      <? } ?>
    </div>

  <!-- body -->
  <div class="panel-body">
