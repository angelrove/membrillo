
function wtree2_onUpdate(id, nivel) {
  location.href = '?CONTROL='+WTree2_CONTROL+'&EVENT=editUpdate&nivel='+nivel+'&ROW_ID='+id;
}
function wtree2_onNewSub(id, nivel) {
  location.href = '?CONTROL='+WTree2_CONTROL+'&EVENT=editNew&ROW_PADRE_ID='+id+'&nivel='+(nivel+1);
}
function wtree2_onDel(id, nivel) {
  if(confirm('¿Está seguro...?')) {
     location.href = '?CONTROL='+WTree2_CONTROL+'&EVENT=list_delete&OPER=delete&nivel='+nivel+'&ROW_ID='+id;
  }
}
function wtree2_onDetalle(id, nivel) {
  location.href = '?CONTROL='+WTree2_CONTROL+'&EVENT=list_rowSelected&nivel='+nivel+'&ROW_ID='+id;
}
