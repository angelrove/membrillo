
//-- Scroll to previous position ----------------------
// set scroll position in session
$(window).scroll(function () {
    sessionStorage.scrollPos = $(window).scrollTop();
    console.log(sessionStorage.scrollPos+"\n");
});

// get scroll position
var init = function () {
    $(window).scrollTop(sessionStorage.scrollPos || 0)
};
window.onload = init;
//-----------------------------------------------------


$(document).ready(function()
{
  // Focus (buscador) ---
  // $('.WFrame input[type="text"]').eq(0).focus();

  //-----------------------------------------------------
  /** Events **/
  //-----------------------------------------------------
  // onRow ----------------------------------------------
  $('.List_tuplas td:not(.optionsBar, .onClickUser)').click(function(event) {
    var row_id = $(this).parents("tr").attr('id');
    List_onEvent($(this), row_id, 'onRow', '', '');
  });
  // new ------------------------------------------------
  $('.List_tuplas .on_new').click(function(event) {
    List_onEvent($(this), '', 'new', '', '');
  });
  // update ---------------------------------------------
  $('.List_tuplas .on_update').click(function(event) {
    event.preventDefault();
    var row_id = $(this).parents("tr").attr('id');
    List_onEvent($(this), row_id, 'update', '', '');
  });
  // delete ---------------------------------------------
  $('.List_tuplas .on_delete').click(function(event) {
    event.preventDefault();
    var row_id = $(this).parents("tr").attr('id');
    List_onEvent($(this), row_id, 'delete', CRUD_OPER_DELETE, List_msgConfirmDel);
  });
  // detalle --------------------------------------------
  $('.List_tuplas .on_detalle').click(function(event) {
    event.preventDefault();
    var row_id = $(this).parents("tr").attr('id');
    List_onEvent($(this), row_id, 'detalle', '', '');
  });
  //-----------------------------------------------------
});

//-----------------------------------------------------
/** shortcuts **/
//-----------------------------------------------------
$(document).keydown(function(e)
{
    var shortcuts_row = $(".List_tuplas tbody tr.selected").index();

    //----------------
    // Ctrl+Up
    if(e.keyCode == 38 && e.ctrlKey)
    {
      if(shortcuts_row <= 0) {
        return;
      }
      shortcuts_row--;
      $(".List_tuplas tbody tr").eq(shortcuts_row).addClass("selected");
      $(".List_tuplas tbody tr").eq(shortcuts_row+1).removeClass("selected");
    }
    //----------------
    // Ctrl+Down
    else if(e.keyCode == 40 && e.ctrlKey)
    {
      if(!$(".List_tuplas tbody tr").eq(shortcuts_row+1).attr("id")) {
        return;
      }
      shortcuts_row++;
      $(".List_tuplas tbody tr").eq(shortcuts_row).addClass("selected");
      $(".List_tuplas tbody tr").eq(shortcuts_row-1).removeClass("selected");
    }
    //----------------
    // Ctrl+Insert (new, onRow, delete)
    else if(e.keyCode == 45 && e.ctrlKey)
    {
      List_onEvent($(".List_tuplas tr"), '', 'new', '', '');
    }
    //----------------
    // Ctrl+Enter
    else if(e.keyCode == 13 && e.ctrlKey)
    {
      var row_id = $(".List_tuplas tr.selected").attr('id');
      List_onEvent($(".List_tuplas tr.selected"), row_id, 'onRow', '', '');
    }
    //----------------
    // Ctrl+Supr
    else if(e.keyCode == 46 && e.ctrlKey)
    {
      var row_id = $(".List_tuplas tr.selected").attr('id');
      List_onEvent($(".List_tuplas tr.selected"), row_id, 'delete', CRUD_OPER_DELETE, List_msgConfirmDel);
    }
    //-----------------------------------
});


//-----------------------------------------------------
// Functions
//-----------------------------------------------------
function List_onEvent(object, row_id, bt, oper, txConfirm)
{
  var List = object.parents(".List_tuplas");

  var action = List.attr('param_action');
  var event  = List.attr('param_event-'+bt);
  if(!event) {
     return false;
  }

  var str_row_id = (row_id)? '/'+row_id : '';
  var str_oper   = (oper)  ? '?OPER='+oper     : '';
  var href_event = action+event+str_row_id+str_oper;

  // Confirm
  if(txConfirm == '') {
     location.href = href_event;
  } else if(confirm(txConfirm)) {
     location.href = href_event;
  }
}
//-------------------------------------------------------
/*
// Delete en segundo plano
function ListOnDelRowBack(parentId, numRow, hrefBt, delete_confirm) {
  if(delete_confirm == false) {
     List_delRow(parentId, numRow, hrefBt);
  }
  else if(confirm('Va a eliminar el registro. ¿Está seguro...?')) {
     List_delRow(parentId, numRow, hrefBt);
  }

  return false;
}
*/
//-------------------------------------------------------
// PRIVATE
//-------------------------------------------------------
/*
function List_delRow(parentId, numRow, url) {
  var result = util_httpRequest(url, false);
  if(result == 'OK') {
     List_delNode(parentId, numRow);
  }
  else {
     alert("ERROR: \n"+result);
  }
}
//-------------------------------------------------------
function List_delNode(parentId, numRow) {
  var elementContenedor = document.getElementById(parentId).getElementsByTagName('tbody')[0];
  var elementDel = elementContenedor.getElementsByTagName('TR')[numRow-1]

  elementContenedor.removeChild(elementDel);
}
//-------------------------------------------------------
*/