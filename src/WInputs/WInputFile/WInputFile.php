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

    Vendor::usef('lightbox');

    CssJsLoad::set(__DIR__.'/styles.css');
    CssJsLoad::set(__DIR__.'/libs.js');
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
     return '<input type="button" '.
                   'id="'.$this->name.'_del" '.
                   'class="WInputButton bt_del" value="X Delete" '.
                   'onclick="WInputFile_delInputFile('."'$this->name'".')">';
  }
  //---------------------------------------------------------------------
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
        echo <<<EOD
         <!-- WInputFile -->
         <div class="well well-sm">
           <table class="WInputFile"><tr>
              <td>$this->label</td>
              <td id="'.$this->name.'_htmFilePrev" class="prevFile">$htmFilePrev</td>
           </tr></table>
         </div>
         <!-- /WInputFile -->
EOD;
        return;
     }

    /** HTM **/
     // NOTA: Los parámetros que configuran el upload se pasan por POST con hidden.
     //       Este sistema no es seguro. Sería mejor utilizar sesiones ("$seccCtrl->setDato()")
     $htmLabel = '';
     if($htmFilePrev) $htmFilePrev = '<td id="'.$this->name.'_htmFilePrev" class="prevFile">'.$htmFilePrev.'</td>';
     if($this->label) $htmLabel    = '<td>'.$this->label.'</td>';

     echo '
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

<table class="WInputFile" cellspacing="0"><tr>
  '.$htmLabel.$htmFilePrev.'
  <td>
    '.$bt_delete.'
    <input type="file" id="'.$this->name.'" name="'.$this->name.'" class="fileUpload" size="27" '.$displayInput.'>
  </td>
</tr></table>
<!-- ----- /WInputFile ----- -->
';
  }
  //---------------------------------------------------------------------
  // PRIVATE
  //---------------------------------------------------------------------
  // A partir de la extensión
  private function get_typeFile($file) {
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
    global $CONFIG_APP, $seccCtrl;

   /** Datos del archivo **/
    $listDatos = FileUploaded::getInfo($this->fileDatos, $seccCtrl->UPLOADS_DIR_DEFAULT);
    $dir = ($listDatos['dir'])? '/'.$listDatos['dir'] : '';
    $listDatos['ruta_completa'] = $CONFIG_APP['url_uploads'].$dir.'/'.$listDatos['name'];

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

   /** Mostrar: información del archivo **/
    // Etiqueta: $bt_varLabel ----
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
    if($bt_varLabel) $bt_varLabel .= '<br>';

    // linkFile ------------
    $linkFile = '';

    // Show: image ---
    if($fileProp_TYPE == 'IMAGE')
    {
       $linkFile = FileUploaded::getHtmlImg($listDatos, 'lightbox', '', '', true);
    }
    // Open: "pdf" and "txt" or if not a MIME Type ---
    elseif(!$listDatos['mime'] || $listDatos['mime'] == 'application/pdf' || $listDatos['mime'] == 'text/plain')
    {
       $linkFile =  '<a href="'.$fileProp_URL.'" target="_blank">'.$bt_varLabel.'</a>';
    }
    // Download
    else
    {
       $linkFile = '<a href="'.$fileProp_URL.'" download>'.$bt_varLabel.'</a>';
    }

    return '
      <!-- File info -->
      '.$linkFile.'
      <!-- /File info -->
    ';
  }
  //---------------------------------------------------------------------
}
