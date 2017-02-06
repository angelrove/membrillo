<?
/**
 * WInputFile: HTM input file object
 * WInputFile_upload: get file uploaded
 *
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 */

namespace angelrove\membrillo2\WInputs\WInputFile;

use angelrove\utils\CssJsLoad;
use angelrove\utils\Vendor;
use angelrove\utils\FileUploaded;


class WInputFile
{
  private $name  = '';
  private $label = '';

  // Datos del archivo previo
  private $fileDatos     = '';
  private $labelFileInfo = '';

  //---
  private $showDel      = true;
  private $showFileName = false;
  private $isReadonly   = false;

  private $showImg      = false;
  private $showImgWidth = 100;

  private $validFiles    = '';
  private $saveAs        = '';
  private $setMaxSize    = 0;

  private $setMaxWidth    = 0;
  private $setMaxHeight   = 0;
  private $setCropHeight  = 0;
  private $setCropHeightY = '';
  private $setThumbWidth  = 0;
  private $watermark_text = '';

  //---------------------------------------------------------------------
  function __construct($name, $fileDatos, $label='')
  {
    $this->name      = $name;
    $this->label     = $label;
    $this->fileDatos = $fileDatos;

    Vendor::usef('lightbox');
  }
  //---------------------------------------------------------------------
  public function hiddenBtDelete() {
    $this->showDel = false;
  }
  //---------------------------------------------------------------------
  // $flag: false, true, short
  public function showFileName($flag) {
    $this->showFileName = $flag;
  }
  //---------------------------------------------------------------------
  public function showImg($flag, $width, $height='') {
    $this->showImg       = $flag;
    $this->showImgWidth  = $width;
    $this->showImgHeight = $height;
  }
  //---------------------------------------------------------------------
  public function setReadOnly($isReadonly) {
    $this->isReadonly = $isReadonly;
  }
  //---------------------------------------------------------------------
  // Propiedades del archivo
  //---------------------------------------------------------------------
  public function setValidFiles($listMimes)
  {
    $this->validFiles = $listMimes;
  }
  //---------------------------------------------------------------------
  /*
   *  $saveAs: - prefijo que se añade al nombre formateado,
   *           - KEEP_NAME: para mantener el nombre original
   */
  public function setSaveAs($saveAs) {
    $this->saveAs = $saveAs;
  }
  //---------------------------------------------------------------------
  public function setMaxSize($setMax) {
    $this->setMaxSize = $setMax;
  }
  //---------------------------------------------------------------------
  // Imagenes
  //---------------------------------------------------------------------
  public function img_setMaxWidth($setMaxWidth) {
    $this->setMaxWidth = $setMaxWidth;
  }
  //---------------------------------------------------------------------
  public function img_setMaxHeight($setMaxHeight) {
    $this->setMaxHeight = $setMaxHeight;
  }
  //---------------------------------------------------------------------
  public function img_setCropHeight($setCropHeight, $cropY='bottom') {
    $this->setCropHeight  = $setCropHeight;
    $this->setCropHeightY = $cropY;
  }
  //---------------------------------------------------------------------
  public function img_setThumbWidth($setThumbWidth) {
    $this->setThumbWidth = $setThumbWidth;
  }
  //---------------------------------------------------------------------
  public function set_watermark($text) {
    $this->watermark_text = $text;
  }
  //---------------------------------------------------------------------
  // GET
  //---------------------------------------------------------------------
  private function get_btDel()
  {
     CssJsLoad::set_script('
$(document).ready(function() {
  $("#'.$this->name.'_del").click(function()
  {
    event.preventDefault();

    // Marcar borrado -----------
     $("#'.$this->name.'_isDelete").val("1");

    // Elementos visuales -------
     // Ocultar: botón "Ver archivo"
     if( $("#'.$this->name.'_htmFilePrev").length ) {
        $("#'.$this->name.'_htmFilePrev").hide();
     }

     // Ocultar: botón "Borrar"
     if( $("#'.$this->name.'_del").length ) {
        $("#'.$this->name.'_del").hide();
     }

     // Mostrar: input file
     if( $("#'.$this->name.'").length ) {
        $("#'.$this->name.'").show();
     }
  });
});
     ');

     return '<button class="btn btn-default btn-sm" id="'.$this->name.'_del"><i class="fa fa-trash-o fa-2x" style="color:red"></i></button>';
  }
  //---------------------------------------------------------------------
  public function get()
  {
    $htmFilePrev  = '';
    $bt_delete    = '';
    $displayInput = '';

    if($this->fileDatos) {
       /** File prev. **/
        $htmFilePrev = $this->getHtm_fileInfo();

       /** Ocultar "input file" **/
        $displayInput = 'style="display:none"';
    }

    /** Read only **/
     if($this->isReadonly === true) {
        if(!$htmFilePrev) {
           $htmFilePrev = '<input type="text" disabled value="">';
        }
        return <<<EOD
         <!-- WInputFile -->
         <div class="well well-sm display-table strip-margin">
            <div class="WInputFile" id="'.$this->name.'_htmFilePrev" class="prevFile">$htmFilePrev</div>
         </div>
         <!-- /WInputFile -->
EOD;
     }

    /** HTM **/
     // NOTA: Los parámetros que configuran el upload se pasan por POST con hidden.
     //       Este sistema no es seguro. Sería mejor utilizar sesiones ("$objectsStatus->setDato()")
     $htmLabel = '';
     if($htmFilePrev) $htmFilePrev = '<td id="'.$this->name.'_htmFilePrev" class="prevFile">'.$htmFilePrev.'</td>';
     if($this->label) $htmLabel    = '<td>'.$this->label.'</td>';

     return '
<!-- ----- WInputFile ----- -->
<input type="hidden" id="'.$this->name.'_isDelete"       name="'.$this->name.'_isDelete"        value="0">
<input type="hidden" id="'.$this->name.'_prev"           name="'.$this->name.'_prev"            value="'.$this->fileDatos.'">
<input type="hidden" id="'.$this->name.'_saveAs"         name="'.$this->name.'_saveAs"          value="'.$this->saveAs.'">
<input type="hidden" id="'.$this->name.'_validFiles"     name="'.$this->name.'_validFiles"      value="'.$this->validFiles.'">
<input type="hidden" id="'.$this->name.'_setMaxSize"     name="'.$this->name.'_setMaxSize"      value="'.$this->setMaxSize.'">
<input type="hidden" id="'.$this->name.'_setMaxWidth"    name="'.$this->name.'_setMaxWidth"     value="'.$this->setMaxWidth.'">
<input type="hidden" id="'.$this->name.'_setMaxHeight"   name="'.$this->name.'_setMaxHeight"    value="'.$this->setMaxHeight.'">
<input type="hidden" id="'.$this->name.'_setCropHeight"  name="'.$this->name.'_setCropHeight"   value="'.$this->setCropHeight.'">
<input type="hidden" id="'.$this->name.'_setCropHeightY" name="'.$this->name.'_setCropHeightY"  value="'.$this->setCropHeightY.'">
<input type="hidden" id="'.$this->name.'_setThumbWidth"  name="'.$this->name.'_setThumbWidth"   value="'.$this->setThumbWidth.'">
<input type="hidden" id="'.$this->name.'_watermark_text" name="'.$this->name.'_watermark_text"  value="'.$this->watermark_text.'">

<div class="well well-sm display-table strip-margin">
  <table class="WInputFile"><tr>
    '.$htmLabel.$htmFilePrev.'
    <td>
      <input type="file" id="'.$this->name.'" name="'.$this->name.'" class="fileUpload" size="27" '.$displayInput.'>
    </td>
  </tr></table>
</div>
<!-- ----- /WInputFile ----- -->
';
  }
  //---------------------------------------------------------------------
  // PRIVATE
  //---------------------------------------------------------------------
  // A partir de la extensión
  private function get_typeFile($file)
  {
    $ext = substr($file, -4, 4);
    //echo "file='$file'; ext=".$ext."<br />";

    switch(strtoupper($ext)) {
      case '.JPG':
      case 'JPEG':
      case '.GIF':
      case '.PNG':
        return 'IMAGE';
      break;
      default:
        return 'FILE';
      break;
    }

  }
  //---------------------------------------------------------------------
  private function get_fileInfo()
  {
     $labelFileInfo = '';

     if(!$this->showFileName) {
        return '';
     }

     //-------
     if($datosFile['fecha'] && $datosFile['size']) {
        $lb_ruta = $datosFile['ruta_completa'];
        $lb_nameUser = '['.$datosFile['nameUser'].']';
        $lb_fecha = ' ['.$datosFile['fecha'].'] ';
        $lb_size  = round(($datosFile['size'] / 1024), 1).'k';

        $labelFileInfo = $lb_ruta . '<br>' . $lb_nameUser . $lb_fecha . $lb_size;
     }
     else { // Compatibilidad
        $labelFileInfo = $datosFile['nameUser'];
     }

     // short -------
     if($this->showFileName === 'short') {
        $labelFileInfo = $lb_nameUser;
     }

      return '<div class="well-sm" style="background-color: white;">'.$labelFileInfo.'</div>';
  }
  //---------------------------------------------------------------------
  private function getHtm_fileInfo()
  {
    global $CONFIG_APP, $seccCtrl;

   /** Datos file **/
    $listDatos = FileUploaded::getInfo($this->fileDatos, $seccCtrl->UPLOADS_DIR_DEFAULT);

    $dir = ($listDatos['dir'])? '/'.$listDatos['dir'] : '';
    $listDatos['ruta_completa'] = $CONFIG_APP['url_uploads'].$dir.'/'.$listDatos['name'];

    if(!$listDatos['nameUser']) {
       $listDatos['nameUser'] = $listDatos['name'];
    }

   /** Out **/
    // View -------
    $fileProp_TYPE = $this->get_typeFile($listDatos['name']); // IMAGE, FILE
    $fileProp_URL  = $listDatos['ruta_completa'];

    $linkView = '';
    if($fileProp_TYPE == 'IMAGE') {
       $linkView = '<div style="max-width:300px">'.FileUploaded::getHtmlImg($listDatos, 'lightbox', '', '', true).'</div>';
    }
    // Open: "pdf" and "txt" ---
    elseif($listDatos['mime'] == 'application/pdf' || $listDatos['mime'] == 'text/plain') {
       $linkView =  '<a class="img-thumbnail" href="'.$fileProp_URL.'" target="_blank">'.
                       '<i class="fa fa-file-pdf-o fa-4x fa-border" aria-hidden="true"></i>'.
                    '</a>';
    }
    // Open: if not a MIME Type ---
    elseif(!$listDatos['mime']) {
       $linkView =  '<a class="img-thumbnail" href="'.$fileProp_URL.'" target="_blank">'.
                       '<i class="fa fa-file-text-o fa-4x fa-border" aria-hidden="true"></i>'.
                    '</a>';
    }

    // Info --------
    $labelFileInfo = $this->get_fileInfo($datosFile);

    // Download ---
    $linkDownload = '<a class="btn btn-default btn-sm" href="'.$fileProp_URL.'" download><i class="fa fa-download fa-2x" aria-hidden="true"></i></a>';

    // Delete -----
    $bt_delete = '';
    if(!$this->isReadonly && $this->showDel === true) {
       $bt_delete = $this->get_btDel();
    }

    return '
      <!-- File info -->
      '.$str_info.'
      <div class="text-center">'.$linkView.'</div>
      <div class="text-center">'.$linkDownload.' '.$bt_delete.'</div>
      <!-- /File info -->
    ';
  }
  //---------------------------------------------------------------------
}
