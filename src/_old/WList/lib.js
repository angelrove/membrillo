
$(document).ready(function() {
  // Focus (buscador) ---
  $('.WFrame input[type="text"]').eq(0).focus();

  //-----------------------------------------------------
  /** Eventos **/
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
    List_onEvent($(this),row_id, 'update', '', '');
  });
  // delete ---------------------------------------------
  $('.List_tuplas .on_delete').click(function(event) {
    event.preventDefault();
    var row_id = $(this).parents("tr").attr('id');
    List_onEvent($(this),row_id, 'delete', 'delete', List_msgConfirmDel);
  });
  // detalle --------------------------------------------
  $('.List_tuplas .on_detalle').click(function(event) {
    event.preventDefault();
    var row_id = $(this).parents("tr").attr('id');
    List_onEvent($(this), row_id, 'detalle', '', '');
  });
  //-----------------------------------------------------
  //-----------------------------------------------------
  function List_onEvent(object, row_id, bt, oper, txConfirm) {
    var List = object.parents(".List_tuplas");
    var control = List.attr('param_control');
    var evento  = List.attr('param_event-'+bt);
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
  /** shortcuts **/
  //-----------------------------------------------------
   var shortcuts_row = $(".List_tuplas tbody tr.selected").index();

   //----------------
   // Down / Up
   shortcut.add("Ctrl+Down",function() {
     if(!$(".List_tuplas tbody tr").eq(shortcuts_row+1).attr("id")) {
        return;
     }
     shortcuts_row++;
     $(".List_tuplas tbody tr").eq(shortcuts_row).addClass("selected");
     $(".List_tuplas tbody tr").eq(shortcuts_row-1).removeClass("selected");
   });
   //-------
   shortcut.add("Ctrl+Up",function() {
     if(shortcuts_row <= 0) {
        return;
     }
     shortcuts_row--;
     $(".List_tuplas tbody tr").eq(shortcuts_row).addClass("selected");
     $(".List_tuplas tbody tr").eq(shortcuts_row+1).removeClass("selected");
   });
   //----------------
   // new, onRow, delete
   shortcut.add("Insert",function() {
     List_onEvent($(".List_tuplas tr"), '', 'new', '', '');
   });
   //----------------
   shortcut.add("Ctrl+Enter",function() {
     var row_id = $(".List_tuplas tr.selected").attr('id');
     List_onEvent($(".List_tuplas tr.selected"), row_id, 'onRow', '', '');
   });
   //----------------
   shortcut.add("Ctrl+Delete",function() {
     var row_id = $(".List_tuplas tr.selected").attr('id');
     List_onEvent($(".List_tuplas tr.selected"), row_id, 'delete', 'delete', List_msgConfirmDel);
   });
   //-----------------------------------
});


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