
// Focus in the first ------------
$(document).ready(function()
{
    $('input[type="text"]').eq(0).focus();

    //-------------------------------------------
    $(".WForm").submit(function( event ) {

      // ROW_ID, action ---
      var formEdit = document.getElementById('form_edit_'+scut_id_object);

      var param_row_id = '';
      if(formEdit.ROW_ID.value) {
         param_row_id = '&ROW_ID='+formEdit.ROW_ID.value;
      }
      formEdit.action = './?CONTROL='+scut_id_object+'&EVENT='+formEdit.EVENT.value+param_row_id;

      // alert( "Handler for .submit() called." );
    });
    //-------------------------------------------
});

// Shortcuts ---------------------
$(document).keydown(function(e)
{
   //------------------------
   // Esc
   if(e.keyCode == 27)
   {
      if(scut_close) {
        WForm_close();
      };
   }
   //------------------------
   // Ctrl+Enter
   if(e.keyCode == 13 && e.ctrlKey)
   {
      $("#form_edit_"+scut_id_object+" #EVENT").val('editUpdate');
      $(".WForm").submit();
  }
   //------------------------
});


//-------------------------------------------
function WForm_delete()
{
  $("#form_edit_"+scut_id_object+" #EVENT").val('form_delete');
  $("#form_edit_"+scut_id_object+" #OPER").val('delete');

  // action
  var formEdit = document.getElementById('form_edit_'+scut_id_object);
  formEdit.action = './?CONTROL='+scut_id_object+'&EVENT='+formEdit.EVENT.value+'&OPER='+formEdit.OPER.value+'&ROW_ID='+formEdit.ROW_ID.value;

  var res = confirm("¿Estás seguro?");
  if(res == true) {
     formEdit.submit();
  } else {
     return false;
  }
}
//-------------------------------------------
function WForm_close()
{
  //var res = confirm("¿Seguro?");
  var res = true;
  if(res == true) {
     window.location = '?CONTROL='+scut_id_object+'&EVENT=form_close';
  }
  else {
     return false;
  }
}
//-------------------------------------------
