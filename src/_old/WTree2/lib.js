
$(document).ready(function() {
  //--------------------------------
  $(".WTree2 .bt_new").click(function(event) {
     location.href = "?CONTROL="+WTree2_CONTROL+"&EVENT=editNew&nivel=1";
     return true;
  });
  //--------------------------------
  $(".WTree2 .bt_newSub").click(function(event) {
     var row_id = $(this).attr("param_id");
     var nivel  = $(".WTree2 #cat_"+row_id).attr("param_nivel");

     location.href = "?CONTROL="+WTree2_CONTROL+"&EVENT=editNew&nivel="+(nivel + 1)+"&ROW_ID="+row_id;
  });
  //--------------------------------
  $(".WTree2 .bt_update").click(function(event) {
     var row_id = $(this).attr("param_id");
     var nivel  = $(".WTree2 #cat_"+row_id).attr("param_nivel");

     location.href = "?CONTROL="+WTree2_CONTROL+"&EVENT=editUpdate&nivel="+nivel+"&ROW_ID="+row_id;
  });
  //--------------------------------
  $(".WTree2 .bt_detalle").click(function(event) {
     var row_id = $(this).attr("param_id");
     var nivel  = $(".WTree2 #cat_"+row_id).attr("param_nivel");

     location.href = "?CONTROL="+WTree2_CONTROL+"&EVENT=list_rowSelecte&nivel="+nivel+"&ROW_ID="+row_id;
  });
  //--------------------------------
  $(".WTree2 .bt_del").click(function(event) {
     var row_id = $(this).attr("param_id");
     var nivel  = $(".WTree2 #cat_"+row_id).attr("param_nivel");

     if(confirm('¿Está seguro...?')) {
        location.href = "?CONTROL="+WTree2_CONTROL+"&EVENT=list_delete&nivel="+nivel+"&OPER=delete&ROW_ID="+row_id;
        return true;
     }
     return false ;
  });
  //--------------------------------
});
