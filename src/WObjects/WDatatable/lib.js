
//----------------------------------------------------------------

function WDatatable_onUpdate(id_component, id)
{
    list_onEvent(id_component, id, CRUD_EDIT_UPDATE, '', '');
}

function list_onEvent(id_component, id, event, oper, txConfirm)
{
  var action = $('#'+id_component).attr('param_action');

  var str_id   = (id)  ? '/'+id : '';
  var str_oper = (oper)? '?OPER='+oper     : '';

  var href_event = action + event + str_id + str_oper;

  // Confirm
  if(txConfirm == '') {
     location.href = href_event;
  }
  else if(confirm(txConfirm)) {
     location.href = href_event;
  }
}
