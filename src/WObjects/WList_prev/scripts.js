
// Focus (buscador) ---
$('.WFrame input[type="text"]').eq(0).focus();

//-----------------------------------------------------
/** Events **/
//-----------------------------------------------------
$(document).ready(function()
{
  // onRow ----------------------------------------------
  $('.WList_tuplas td:not(.optionsBar, .onClickUser)').click(function(event) {
    var row_id = $(this).parents("tr").attr('id');
    WList_onEvent($(this), row_id, 'onRow', '', '');
  });
  // new ------------------------------------------------
  $('.WList_tuplas .on_new').click(function(event) {
    WList_onEvent($(this), '', 'new', '', '');
  });
  // update ---------------------------------------------
  $('.WList_tuplas a.on_update').click(function(event) {
    event.preventDefault();
    var row_id = $(this).parents("tr").attr('id');
    WList_onEvent($(this),row_id, 'update', '', '');
  });
  // delete ---------------------------------------------
  $('.WList_tuplas a.on_delete').click(function(event)
  {
    event.preventDefault();
    var row_id = $(this).parents("tr").attr('id');
    WList_onEvent($(this),row_id, 'delete', 'delete', WList_msgConfirmDel);
  });
  // detalle --------------------------------------------
  $('.WList_tuplas a.on_detalle').click(function(event) {
    event.preventDefault();
    var row_id = $(this).parents("tr").attr('id');
    WList_onEvent($(this), row_id, 'detalle', '', '');
  });
  //-----------------------------------------------------
});

//-----------------------------------------------------
/** shortcuts **/
//-----------------------------------------------------
$(document).keydown(function(e)
{
    var shortcuts_row = $(".WList_tuplas tbody tr.selected").index();

    //----------------
    // Ctrl+Up
    if(e.keyCode == 38 && e.ctrlKey)
    {
       if(shortcuts_row <= 0) {
          return;
       }
       shortcuts_row--;
       $(".WList_tuplas tbody tr").eq(shortcuts_row).addClass("selected");
       $(".WList_tuplas tbody tr").eq(shortcuts_row+1).removeClass("selected");
    }
    //----------------
    // Ctrl+Down
    else if(e.keyCode == 40 && e.ctrlKey)
    {
       if(!$(".WList_tuplas tbody tr").eq(shortcuts_row+1).attr("id")) {
          return;
       }
       shortcuts_row++;
       $(".WList_tuplas tbody tr").eq(shortcuts_row).addClass("selected");
       $(".WList_tuplas tbody tr").eq(shortcuts_row-1).removeClass("selected");
    }
    //----------------
    // Ctrl+Insert (new, onRow, delete)
    else if(e.keyCode == 45 && e.ctrlKey)
    {
       WList_onEvent($(".WList_tuplas tr"), '', 'new', '', '');
    }
    //----------------
    // Ctrl+Enter
    else if(e.keyCode == 13 && e.ctrlKey)
    {
       var row_id = $(".WList_tuplas tr.selected").attr('id');
       WList_onEvent($(".WList_tuplas tr.selected"), row_id, 'onRow', '', '');
    }
    //----------------
    // Ctrl+Del
    else if(e.keyCode == 46 && e.ctrlKey)
    {
       var row_id = $(".WList_tuplas tr.selected").attr('id');
       WList_onEvent($(".WList_tuplas tr.selected"), row_id, 'delete', 'delete', WList_msgConfirmDel);
    }
    //----------------
});


//-----------------------------------------------------
// Functions
//-----------------------------------------------------
function WList_onEvent(object, row_id, bt, oper, txConfirm)
{
  var WList = object.parents(".WList_tuplas");
  var control = WList.attr('param_control');
  var evento  = WList.attr('param_event-'+bt);
  if(!evento) {
     return false;
  }

  str_row_id = (row_id)? '&ROW_ID='+row_id : '';
  str_oper   = (oper)  ? '&OPER='+oper     : '';
  var href_event = '?CONTROL='+control+'&EVENT='+evento+str_row_id+str_oper;

  // Confirm
  if(txConfirm == '') {
     location.href = href_event;
  } else if(confirm(txConfirm)) {
     location.href = href_event;
  }
}
//-----------------------------------------------------
/*
// Delete en segundo plano
function WListOnDelRowBack(parentId, numRow, hrefBt, delete_confirm) {
  if(delete_confirm == false) {
     WList_delRow(parentId, numRow, hrefBt);
  }
  else if(confirm('Va a eliminar el registro. ¿Está seguro...?')) {
     WList_delRow(parentId, numRow, hrefBt);
  }

  return false;
}
*/
//-------------------------------------------------------
// PRIVATE
//-------------------------------------------------------
/*
function WList_delRow(parentId, numRow, url) {
  var result = util_httpRequest(url, false);
  if(result == 'OK') {
     WList_delNode(parentId, numRow);
  }
  else {
     alert("ERROR: \n"+result);
  }
}
//-------------------------------------------------------
function WList_delNode(parentId, numRow) {
  var elementContenedor = document.getElementById(parentId).getElementsByTagName('tbody')[0];
  var elementDel = elementContenedor.getElementsByTagName('TR')[numRow-1]

  elementContenedor.removeChild(elementDel);
}
//-------------------------------------------------------
*/