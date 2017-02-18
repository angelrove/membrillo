
function wtree2_onUpdate(id, nivel) {
  location.href = '/'+main_secc+'/'+WTree2_CONTROL+'/'+CRUD_EDIT_UPDATE+'/?ROW_ID='+id+'&nivel='+nivel;
}
function wtree2_onNewSub(id, nivel) {
  location.href = '/'+main_secc+'/'+WTree2_CONTROL+'/'+CRUD_EDIT_NEW+'/?ROW_PADRE_ID='+id+'&nivel='+(nivel+1);
}
function wtree2_onDel(id, nivel) {
  if(confirm('¿Está seguro...?')) {
     location.href = '/'+main_secc+'/'+WTree2_CONTROL+'/list_delete/?OPER='+CRUD_DELETE+'&nivel='+nivel+'&ROW_ID='+id;
  }
}
function wtree2_onDetalle(id, nivel) {
  location.href = '/'+main_secc+'/'+WTree2_CONTROL+'/list_rowSelected/?nivel='+nivel+'&ROW_ID='+id;
}
