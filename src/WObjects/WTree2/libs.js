
var idPrev;

//-------------------------------------------------------
function WTree_Del() {
  if(confirm('¿Está seguro...?')) return true;
  return false ;
}
//-------------------------------------------------------
function WTree_change_display(id) {

  // Seleccionada (mostrar)
  var capa   = document.getElementById('cat_'+id);
  var objImg = document.getElementById('img_'+id);
  if(capa) WTree_cambiarVisibilidad(capa, objImg);

  // Previa (ocultar)
  if(idPrev && idPrev != id) {
     var capaPrev   = document.getElementById('cat_'+idPrev);
     var objImgPrev = document.getElementById('img_'+idPrev);
     WTree_ocultar(capaPrev, objImgPrev);
  }
  idPrev = id;

}
//-------------------------------------------------------
function WTree_cambiarVisibilidad(capa, objImg)
{
  if(capa.style.display == "") {
     WTree_ocultar(capa, objImg)
  }
  else {
     WTree_show(capa, objImg);
  }

  return capa.style.display;
}
//-------------------------------------------------------
function WTree_show(capa, objImg)
{
  capa.style.display = "";

  if(objImg.src.substr((objImg.src.length - 8), 8) == "cruz.gif") {
     // objImg.src = "/_libUtils/WObjects/WTree/images/cruzDesplegada.gif";
     objImg.src = '<i class="fas fa-angle-down" aria-hidden="true"></i>';
  } else {
     // objImg.src = "/_libUtils/WObjects/WTree/images/cruzDesplegadaFin.gif";
     objImg.src = '<i class="fas fa-angle-right" aria-hidden="true"></i>';
  }
}
//-------------------------------------------------------
function WTree_ocultar(capa, objImg) {
  capa.style.display = "none";

  if(objImg.src.substr((objImg.src.length - 21), 21) == "cruzDesplegadaFin.gif") {
     // objImg.src = "/_libUtils/WObjects/WTree/images/cruzFin.gif";
     objImg.src = '<i class="fas fa-angle-right" aria-hidden="true"></i>';
  }
  else {
     // objImg.src = "/_libUtils/WObjects/WTree/images/cruz.gif";
     objImg.src = '<i class="fas fa-plus" aria-hidden="true"></i>';
  }
}
//-------------------------------------------------------
