
// Shortcuts ---------------------
$(document).keydown(function(e)
{
   // Esc (Close) ----------
   if(e.keyCode == 27) {
      if(scut_close) {
         WForm_close();
      };
   }

   // Ctrl+Intro (Save) ----
   // if(e.keyCode == 13) {
   //    if(e.ctrlKey) {
   //       WForm_save();
   //       $(".WForm").submit();
   //    }
   // }
});
//--------------------------------


$(document).ready(function()
{
    // Focus in the first
    // $('input').eq(0).focus();

    // Submit -----------------------------------
    $(".WForm").submit(function( event )
    {
       var scut_id_object = $(this).attr("scut_id_object");

       // action: ROW_ID ---
       var formEdit = document.getElementById('form_edit_'+scut_id_object);

       var param_row_id = '';
       if(formEdit.ROW_ID.value) {
          param_row_id = '/'+formEdit.ROW_ID.value;
       }

       formEdit.action = formEdit.ACTION.value+formEdit.EVENT.value+param_row_id+'/';
       // alert("scut_id_object: "+scut_id_object+"; action: "+formEdit.action);
    });

    // Enter ------------------------------------
    // $(".WForm_bfAccept").click(function()
    // {
    //    WForm_enter();
    // });
    // Save -------------------------------------
    $(".WForm_btUpdate").click(function()
    {
       var scut_id_object = $(this).attr("scut_id_object");
       WForm_save(scut_id_object);
    });
    // Save and new -----------------------------
    $(".WForm_btInsert").click(function()
    {
       var scut_id_object = $(this).attr("scut_id_object");
       WForm_insert(scut_id_object);
    });
    // Delete -----------------------------------
    $(".WForm_btDelete").click(function()
    {
       var scut_id_object = $(this).attr("scut_id_object");

       $("#form_edit_"+scut_id_object+" #OPER").val(CRUD_OPER_DELETE);

       // action
       var formEdit = document.getElementById('form_edit_'+scut_id_object);
       formEdit.action = formEdit.action+formEdit.EVENT.value+'/'+formEdit.ROW_ID.value+'/?OPER='+formEdit.OPER.value;

       var res = confirm("¿Estás seguro?");
       if(res == true) {
          formEdit.submit();
       } else {
          return false;
       }
    });
    // Close ------------------------------------
    $(".WForm_btClose").click(function()
    {
       WForm_close();
    });
    //-------------------------------------------
});

//-------------------------------------------
// function WForm_enter()
// {
//   $(".WForm").submit();
// }
//-------------------------------------------
function WForm_insert(scut_id_object)
{
   $("#form_edit_"+scut_id_object+" #EVENT").val(CRUD_EDIT_NEW);
   // $(".WForm").submit();
}
//-------------------------------------------
function WForm_save(scut_id_object)
{
   $("#form_edit_"+scut_id_object+" #EVENT").val(CRUD_EDIT_UPDATE);
   // $(".WForm").submit();
}
//-------------------------------------------
function WForm_close()
{
    window.location.href = '/'+main_secc+'/';

    // var res = confirm("¿Seguro?");
    // if(res == true) {
    //    window.location.href = '/'+main_secc+'/';
    // } else {
    //    return false;
    // }
}
//-------------------------------------------
