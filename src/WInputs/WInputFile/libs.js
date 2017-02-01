
//-------------------------------------------------------
function WInputFile_show(url_opener, img_popup)
{
  var w_popup = window.open(url_opener+"?img_popup="+img_popup, "w_popup", "toolbar=0, scrollbars=0, resizable=no");
  w_popup.focus();
  return false;
}
//-------------------------------------------------------
function WInputFile_delInputFile(name)
{
  // Confirm ------------------
  //var agree = confirm("¿Está seguro?");
  //if(!agree) return false;

  // Marcar borrado -----------
   var objIsDelete = document.getElementById(name+'_isDelete');
   objIsDelete.value = '1';

  // Elementos visuales -------
   // Ocultar: botón "Ver archivo"
   var objHtmFilePrev = document.getElementById(name+'_htmFilePrev');
   if(objHtmFilePrev) objHtmFilePrev.style.display = 'none';

   // Ocultar: botón "Borrar"
   var objDel = document.getElementById(name+'_del');
   if(objDel) objDel.style.display = 'none';

   // Mostrar: input file
   var objUpload = document.getElementById(name);
   if(objUpload) objUpload.style.display = '';
}
//-------------------------------------------------------
