
var idPrev;

$(document).ready(function() {
  //--------------------------------
  $(".WTree .op_row").click(function(event) {

     WTree_onSelectRow($(this).attr("param_id"));

     // var control = $(this).attr("param_ctrl");
     // var row_id  = $(this).attr("param_id");
     // var nivel   = $(this).attr("param_nivel");
     // var id_top  = $(this).attr("param_id_top");
     // location.href = "/"+main_secc+"/crd/"+control+"/list/?OPER="+CRUD_DELETE+"&nivel="+nivel+"&ROW_ID="+row_id;

  });
  //--------------------------------
  $(".WTree .op_delete").click(function(event) {
     event.preventDefault();

     if(confirm('¿Está seguro...?')) {
       var control= $(this).attr("param_ctrl");
       var row_id = $(this).attr("param_id");
       var nivel  = $(this).attr("param_nivel");
       location.href = "/"+main_secc+"/crd/"+control+"/list/?OPER="+CRUD_DELETE+"&nivel="+nivel+"&ROW_ID="+row_id;
       return true;
     }
     return false ;

  });
  //--------------------------------
});

//-------------------------------------------------------
function WTree_onSelectRow_reload(url)
{
   location.href = url;
}
//-------------------------------------------------------
function WTree_onSelectRow(id)
{
  console.log("WTree_onSelectRow("+id+")");

  // Seleccionada (mostrar)
  if(document.getElementById('cat_'+id)) {
     WTree_cambiarVisibilidad(id);
  }

  // Previa (ocultar)
  if(idPrev && idPrev != id) {
     WTree_ocultar(idPrev);
  }

  idPrev = id;
}
//-------------------------------------------------------
// Private
//-------------------------------------------------------
function WTree_cambiarVisibilidad(id)
{
  console.log("WTree_cambiarVisibilidad("+id+")");

  if($(".WTree #cat_"+id).is(':visible')) {
     WTree_ocultar(id);
  }
  else {
     WTree_show(id);
  }
}
//---------------------------
function WTree_show(id)
{
  console.log("WTree_show("+id+")");

  var image = ".WTree .row_"+id+" .title i";

  $(".WTree #cat_"+id).show();

  $(image).addClass("fa-minus");
  $(image).removeClass("fa-plus");
}
//---------------------------
function WTree_ocultar(id)
{
  console.log("WTree_ocultar("+id+")");

  $(".WTree #cat_"+id).hide();

  var image = ".WTree .row_"+id+" .title i";
  $(image).addClass("fa-plus");
  $(image).removeClass("fa-minus");
}
//-------------------------------------------------------
