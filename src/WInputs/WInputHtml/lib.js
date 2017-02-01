tinymce.init({

  mode : "specific_textareas",
  editor_selector : WInputHtml_selector,
  height: WInputHtml_height,
  code_dialog_width: 900,

  force_br_newlines : false,
  force_p_newlines  : true,
  forced_root_block : "",

  plugins: [
    "hr advlist autolink lists link textcolor colorpicker image charmap print preview anchor",
    "searchreplace visualblocks code fullscreen",
    "insertdatetime media table contextmenu paste"
  ],
  toolbar: "code | insertfile undo redo | forecolor | fontsizeselect | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"

});
