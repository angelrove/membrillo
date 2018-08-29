
$(document).ready(function() {

  $(".wtree_onNewSub").click(function(event) {
    var id    = $(this).data("id");
    var nivel = $(this).data("level");

    location.href = '/'+main_secc+'/crd/'+WTree2_CONTROL+'/'+CRUD_EDIT_NEW+'/?ROW_PARENT_ID='+id+'&level='+(nivel+1);
  });

  $(".wtree_onUpdate").click(function(event) {
    var id    = $(this).data("id");
    var nivel = $(this).data("level");

    location.href = '/'+main_secc+'/crd/'+WTree2_CONTROL+'/'+CRUD_EDIT_UPDATE+'/'+id+'/?level='+nivel;
  });

  $(".wtree_onDel").click(function(event) {
    var id    = $(this).data("id");
    var nivel = $(this).data("level");

    if(confirm('¿Está seguro...?')) {
       location.href = '/'+main_secc+'/crd/'+WTree2_CONTROL+'/'+CRUD_DEFAULT+'/'+id+'/?OPER='+CRUD_OPER_DELETE+'&level='+nivel;
    }
  });

  $(".wtree_onDetalle").click(function(event) {
    var id    = $(this).data("id");
    var nivel = $(this).data("level");

    location.href = '/'+main_secc+'/crd/'+WTree2_CONTROL+'/'+CRUD_LIST_DETAIL+'/'+id+'/?level='+nivel;
  });

});
