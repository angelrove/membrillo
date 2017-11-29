
<!-- WFrame -->
<div class="WFrame panel panel-default">
    <div class="panel-heading">
      <?php  if($params['showClose']) { ?>
        <button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <?php  } ?>
      <?php  if($params['title']) { ?>
        <div class="panel-title"><?=$params['title']?> &nbsp;</div>
      <?php  } ?>
    </div>

  <!-- body -->
  <div class="panel-body">
