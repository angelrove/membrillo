<!-- WInputHtml -->
<script>
var WInputHtml_selector = "<?=$params['selector']?>";
var WInputHtml_height   = <?=$params['height']?>;
</script>

<style>
textarea.mce-textbox {
  font-family: "Courier New";
  background-color: #333;
  color: #eee;
}
</style>

<!-- <form method="post" action="somepage"> -->
<textarea name="<?=$params['name']?>" class="<?=$params['selector']?>"><?=html_entity_decode($params['value'])?></textarea>
<!-- </form> -->
