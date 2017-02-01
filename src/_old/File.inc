<?
/**
 * WInputFile: HTM input file object
 * WInputFile_upload: get file uploaded
 *
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 */

namespace membrillo\ComponentsInputs;

use membrillo\utils\CssJs_load;
use membrillo\utils\FileUpload;
use membrillo\utils\UtilsBasic;


class File
{
  private $name  = '';
  private $label = '';

  // Datos del archivo previo
  private $fileDatos     = '';
  private $labelFileInfo = '';

  //---
  private $labelDownloadFile = '';
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
  }
  //---------------------------------------------------------------------
  public function setLabelDownloadFile($labelDownload) {
    $this->labelDownloadFile = $labelDownload;
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
  public function setValidFiles($listMimes) {
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
    CssJs_load::set_script('
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

    return '<button class="btn btn-danger btn-sm" id="'.$this->name.'_del"><i class="fa fa-trash-o fa-lg"></i></button>';
  }
  //-----------------------------
  public function getHtm()
  {
    $htmFilePrev  = '';
    $bt_delete    = '';
    $displayInput = '';

    if($this->fileDatos) {
       /** File prev. **/
        $htmFilePrev = $this->getHtm_fileInfo();

       /** Button "Delete" **/
        if($this->showDel === true) {
           $bt_delete = $this->get_btDel();
        }

       /** Ocultar "input file" **/
        $displayInput = 'style="display:none"';
    }

    /** HTM: read only **/
     if($this->isReadonly === true) {
        if(!$htmFilePrev) {
           $htmFilePrev = '<input type="text" disabled value="">';
        }
        return <<<EOD
         <!-- WInputFile -->
         <div class="well well-sm">
           <table class="WInputFile"><tr>
              <td>$this->label</td>
              <td id="'.$this->name.'_htmFilePrev">$htmFilePrev</td>
           </tr></table>
         </div>
         <!-- /WInputFile -->
EOD;
     }

    /** HTM **/
     // NOTA: Los parámetros que configuran el upload se pasan por POST con hidden.
     //       Este sistema no es seguro. Sería mejor utilizar sesiones ("$this->objectStatus->setDato()")
     $htmLabel = '';
     if($htmFilePrev) $htmFilePrev = '<td id="'.$this->name.'_htmFilePrev">'.$htmFilePrev.'</td>';
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
      '.$bt_delete.'
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
  private function getHtm_fileInfo()
  {
    global $app, $CONFIG_APP, $seccCtrl;

   /** Datos del archivo **/
    $listDatos = FileUploaded::getInfo($this->fileDatos, $seccCtrl->UPLOADS_DIR_DEFAULT);
    $dir = ($listDatos['dir'])? '/'.$listDatos['dir'] : '';
    $listDatos['ruta_completa'] = $CONFIG_APP['rutas']['URL_UPLOADS'].$dir.'/'.$listDatos['name'];

    // Compatibilidad
    if(!$listDatos['nameUser']) {
       $listDatos['nameUser'] = $listDatos['name'];
    }

    // labelFileInfo
    if($listDatos['fecha'] && $listDatos['size']) {
       $lb_ruta = $listDatos['ruta_completa'];
       $lb_nameUser = '['.$listDatos['nameUser'].']';
       $lb_fecha = ' ['.$listDatos['fecha'].'] ';
       $lb_size  = round(($listDatos['size'] / 1024), 1).'k';

       $this->labelFileInfo = $lb_ruta . '<br>' . $lb_nameUser . $lb_fecha . $lb_size;
    }
    else { // Compatibilidad
       $this->labelFileInfo = $listDatos['nameUser'];
    }

    $fileProp_URL  = $listDatos['ruta_completa'];
    $fileProp_TYPE = $this->get_typeFile($listDatos['name']); // IMAGE, FILE

   /** Show file info **/
    // Label: $bt_varLabel ----
    $bt_varLabel = $this->labelDownloadFile;
    if($this->showFileName) {
       if($this->showFileName === 'short') {
          $bt_varLabel = $lb_nameUser;
       } else {
          $bt_varLabel = $this->labelFileInfo;
       }
    }

    if(!$bt_varLabel) {
       if($fileProp_TYPE == 'FILE') {
          if(!$this->showFileName) $bt_varLabel = '<i class="fa fa-download fa-2x" aria-hidden="true"></i>';
       }
       elseif($fileProp_TYPE == 'IMAGE') {
          if(!$this->showImg) {
             $bt_varLabel = '<i class="fa fa-eye fa-2x" aria-hidden="true"></i>';
          }
       }
    }

    if($bt_varLabel) {
       $bt_varLabel .= '<br>';
    }

    // linkFile ------------
    $linkFile = '';

    // Show image: $htmShowImage ----
    $htmShowImage = '';
    if($this->showImg) {
       if($fileProp_TYPE == 'IMAGE') {
          $showImgHeight = ($this->showImgHeight)? ' height="'.$this->showImgHeight.'"' : '';
          $htmShowImage = '<img class="img-thumbnail" src="'.$fileProp_URL.'" width="'.$this->showImgWidth.'"'.$showImgHeight.' border="1">';
       }
    }

    // Show image
    if($fileProp_TYPE == 'IMAGE') {
       return '
        <!-- File info -->
        <a href="#" data-toggle="modal" data-target=".WInputFile-modal-'.$this->name.'">
         '.$bt_varLabel.'
         '.$htmShowImage.'
        </a>
        '.$this->getModal($fileProp_URL).'
        <!-- /File info -->
      ';
    }
    // Link to files
    else {
       // $linkFile = $this->getFileLink();
       $linkFile = '';

       // Show (if documents "pdf" or "txt", if no MIME Type)
       if(!$listDatos['mime'] ||
           $listDatos['mime'] == 'application/pdf' ||
           $listDatos['mime'] == 'text/plain') {
           $onclick = "window.open('$fileProp_URL', 'w_popup').focus();";
           $linkFile = '<a href="#" onclick="'.$onclick.'">';
       }
       // Download
       else {
          $onclick = "location.href = '/?APP_EVENT=download&f=$fileProp_URL&fu=$listDatos[nameUser]&mime=".$listDatos['mime']."';";
          $linkFile = '<a href="#" onclick="'.$onclick.'">';
       }

       return '
        <!-- File info -->
        '.$linkFile.'
        '.$bt_varLabel.'
        </a>
        <!-- /File info -->
      ';
    }
  }
  //---------------------------------------------------------------------
