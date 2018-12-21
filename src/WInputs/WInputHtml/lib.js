tinymce.init({

    mode : "specific_textareas",
    editor_selector : WInputHtml_selector,
    height: WInputHtml_height,
    code_dialog_width: 900,

    //entity_encoding : "raw",
    verify_html: false,

    force_br_newlines : false,
    force_p_newlines  : true,
    forced_root_block : "",

    visualblocks_default_state: true,

    //plugins: [ "searchreplace insertdatetime" ],

    plugins: [
       "emoticons hr advlist autolink lists link textcolor colorpicker image charmap print preview anchor",
       "visualblocks code codesample fullscreen",
       "media table contextmenu paste"
    ],

    toolbar: "emoticons | code codesample | undo redo | forecolor fontsizeselect styleselect "+
             "bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | image"

});
