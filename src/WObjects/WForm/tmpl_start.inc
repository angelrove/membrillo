<?
use angelrove\membrillo2\WPage\Frame;
?>

 <?=Frame::get($this->title, $this->bt_cancel)?>
   <script>
   var scut_id_object = '<?=$this->id_object?>';
   var scut_close     = '<?=$this->bt_cancel?>';
   </script>

   <form class="WForm form-horizontal"
         enctype="multipart/form-data"
         id      ="form_edit_<?=$this->id_object?>"
         name    ="form_edit_<?=$this->id_object?>"
         onsubmit ="WForm_submit('<?=$this->id_object?>', '')"
         method  = "POST"
         action  = "">
   <input type="hidden" name="CONTROL" value="<?=$this->id_object?>">
   <input type="hidden" name="EVENT"   value="<?=$event?>">
   <input type="hidden" name="OPER"    value="<?=$oper?>">
   <input type="hidden" name="ROW_ID"  value="<?=$row_id?>">

   <?=$htmButtons_top?>

