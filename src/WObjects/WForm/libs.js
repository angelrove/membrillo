
// Shortcuts ---------------------
$(document).keydown(function(e)
{
   //------------------------
   // Esc (Close)
   if(e.keyCode == 27) {
      if(scut_close) {
        WForm_close();
      };
   }
   //------------------------
   // Ctrl+Enter (Save)
   if(e.keyCode == 13 && e.ctrlKey) {
      WForm_save();
   }
   //------------------------
});

$(document).ready(function()
{
    // Focus in the first -----------------------
    $('input[type="text"]').eq(0).focus();

    // Submit -----------------------------------
    $(".WForm").submit(function( event ) {

      // action: ROW_ID ---
      var formEdit = document.getElementById('form_edit_'+scut_id_object);

      var param_row_id = '';
      if(formEdit.ROW_ID.value) {
         param_row_id = '?ROW_ID='+formEdit.ROW_ID.value;
      }

      formEdit.action = formEdit.action+formEdit.EVENT.value+'/'+param_row_id;
      // alert("action: "+formEdit.action);
    });
    //-------------------------------------------
});

//-------------------------------------------
function WForm_save()
{
  $("#form_edit_"+scut_id_object+" #EVENT").val('editUpdate');
  $(".WForm").submit();
}
//-------------------------------------------
function WForm_insert()
{
  $("#form_edit_"+scut_id_object+" #EVENT").val('editNew');
  $(".WForm").submit();
}
//-------------------------------------------
function WForm_delete()
{
  $("#form_edit_"+scut_id_object+" #EVENT").val('form_delete');
  $("#form_edit_"+scut_id_object+" #OPER").val('delete');

  // action
  var formEdit = document.getElementById('form_edit_'+scut_id_object);
  formEdit.action = '/'+main_secc+'/crd/'+scut_id_object+'/'+formEdit.EVENT.value+'/?OPER='+formEdit.OPER.value+'&ROW_ID='+formEdit.ROW_ID.value;

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
     window.location = '/'+main_secc+'/';
  }
  else {
     return false;
  }
}
//-------------------------------------------
