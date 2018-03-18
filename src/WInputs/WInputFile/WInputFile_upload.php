<?php
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 * class WInputFile_upload: upload/delete a file
 *
 */

namespace angelrove\membrillo2\WInputs\WInputFile;

use angelrove\membrillo2\DebugTrace;
use angelrove\membrillo2\WObjectsStatus\Event;
use angelrove\utils\Db_mysql;
use angelrove\utils\FileUpload;
use angelrove\utils\Images\ImageTransform;
use angelrove\utils\Images\ImageWatermark;

class WInputFile_upload
{
    //-----------------------------------------------------------
    /**
     *  @param $dbTable:   tabla de la bbdd
     *  @param $fieldName: campo del formulario que se corresponde también con el campo de la bbdd
     *  @return: el nombre con el que se ha copiado el archivo subido
     */
    public static function getFile($dbTable, $fieldName)
    {
        global $CONFIG_APP, $seccCtrl;
        $trazas = "WInputFile_upload('$dbTable', '$fieldName')";

        if (count($_FILES) == 0) {
            Messages::set("ERROR [upload], asegurate de que el formulario contiene 'enctype=\"multipart/form-data\"'");
        }

        /** Propiedades **/
        $fileNew  = $_FILES[$fieldName]['name'];
        $fileSize = $_FILES[$fieldName]['size'];
        $fileMIME = $_FILES[$fieldName]['type'];

        $param_isDelete       = $_REQUEST[$fieldName . '_isDelete'];
        $param_filePrev       = $_REQUEST[$fieldName . '_prev'];
        $param_saveAs         = $_REQUEST[$fieldName . '_saveAs'];
        $validFiles           = $_REQUEST[$fieldName . '_validFiles'];
        $param_maxSize        = $_REQUEST[$fieldName . '_setMaxSize'];
        $param_setMaxWidth    = $_REQUEST[$fieldName . '_setMaxWidth'];
        $param_setMaxHeight   = $_REQUEST[$fieldName . '_setMaxHeight'];
        $param_setCropHeight  = $_REQUEST[$fieldName . '_setCropHeight'];
        $param_setCropHeightY = $_REQUEST[$fieldName . '_setCropHeightY'];
        $param_thumbWidth     = $_REQUEST[$fieldName . '_setThumbWidth'];
        $param_watermark_text = $_REQUEST[$fieldName . '_watermark_text'];

        /** >> DELETE **/
        if ($param_isDelete) {
            $listDatos    = explode('#', $param_filePrev);
            $fileToDelete = $listDatos[0];

            $file_path = '';
            if (isset($listDatos[5]) && $listDatos[5]) {
                $file_path = $CONFIG_APP['path_uploads'] . '/' . $listDatos[5];
            } elseif ($seccCtrl->UPLOADS_DIR_DEFAULT) {
                $file_path = $CONFIG_APP['path_uploads'] . '/' . $seccCtrl->UPLOADS_DIR_DEFAULT;
            } else {
                $file_path = $CONFIG_APP['path_uploads'];
            }

            $traza = $trazas . ' >> delete: "' . $file_path . '/' . $fileToDelete . '"';
            DebugTrace::out('WInputFile', $traza);

            unlink($file_path . '/' . $fileToDelete);
            @unlink($file_path . '/' . 'th_' . $fileToDelete); // intenta eliminar un posible thumbnail

            if (!$fileNew) {
                return ''; // SQL
            }
        }

        /** >> NADA QUE HACER **/
        if (!$fileNew) {
            return $param_filePrev; // SQL
        }

        /** >> UPLOAD **/
        // $saveAs
        $saveAs = self::getNewFileName($dbTable, $fieldName);
        if ($param_saveAs) {
            if ($param_saveAs == 'KEEP_NAME') {
                list($saveAs, $ext) = explode('.', $fileNew);
            } else {
                $saveAs = $param_saveAs . $saveAs;
            }
        }

        // Upload
        $uploads_path = $CONFIG_APP['path_uploads'] . '/' . $seccCtrl->UPLOADS_DIR;

        $resUpload = FileUpload::getFile($fieldName, $saveAs, $uploads_path, $validFiles, $param_maxSize);
        $traza     = "$trazas >> saveAs: '$saveAs', uploads_path: '$uploads_path'";
        DebugTrace::out('WInputFile', $traza);
        DebugTrace::out('WInputFile >> resUpload', $resUpload);

        if ($resUpload !== true) {
            switch ($resUpload['COD']) {
                case 'InputIsEmptyError':
                    break;
                case 'FileTypeError':
                case 'FileSizeError':
                case 'CopyFileError':
                    //WFrameAlert('ERROR: '.$resUpload['MSG']);
                    $ret->errors[$fieldName] = '<div>' . $resUpload['MSG'] . '</div>';
                    return $ret;
            }
        }

        /** 'nameWidthExt' **/
        $fileExt      = substr(strrchr($fileNew, '.'), 1);
        $nameWidthExt = $saveAs . '.' . $fileExt;
        //echo "nameWidthExt=$nameWidthExt";

        /** Imágenes **/
        if (self::isImage($fieldName)) {
            // Redimensionar ---
            //         if($param_setMaxWidth > 0 && $param_setMaxHeight > 0) { //echo "resize() - 0";
            //            ImageTransform::resize($uploads_path, $nameWidthExt, $param_setMaxWidth, $param_setMaxHeight);
            //         }
            if ($param_setMaxWidth > 0) {
                $datosImg = ImageTransform::getDatosImg($uploads_path, $nameWidthExt);
                if ($datosImg['width'] > $param_setMaxWidth) {
                    // echo "resize() - 1";
                    ImageTransform::resize($uploads_path, $nameWidthExt, $param_setMaxWidth, '');
                }
            }
            if ($param_setMaxHeight > 0) {
                $datosImg = ImageTransform::getDatosImg($uploads_path, $nameWidthExt);
                if ($datosImg['height'] > $param_setMaxHeight) {
                    // echo "resize() - 2";
                    ImageTransform::resize($uploads_path, $nameWidthExt, '', $param_setMaxHeight);
                }
            }

            // Recortar altura ---
            if ($param_setCropHeight > 0) {
                ImageTransform::crop($uploads_path, $nameWidthExt, 0, $param_setCropHeight, false, 'right', $param_setCropHeightY);
            }

            // Thumbnail ---
            if ($param_thumbWidth > 0) {
                $traza = $trazas . " >> Thumb: param_thumbWidth=$param_thumbWidth; uploads_path='$uploads_path'";
                DebugTrace::out('WInputFile', $traza);
                ImageTransform::resize($uploads_path, $nameWidthExt, $param_thumbWidth, '', 'th_');
            }

            // Watermark ---
            if ($param_watermark_text) {
                ImageWatermark::updateImg($uploads_path . '/' . $nameWidthExt, $param_watermark_text);
            }
        }

        /** Return **/
        // limitar nombre del archivo
        $limitName = 40;
        if (strlen($fileNew) > $limitName) {
            $fileNew = substr($fileNew, 0, $limitName) . '.' . $fileExt;
        }

        // result
        $bbdd_value = $nameWidthExt . '#' .
        $fileNew . '#' .
        date('d/m/y H:i') . '#' .
        $fileSize . '#' .
        $fileMIME . '#' .
        $seccCtrl->UPLOADS_DIR;

        return $bbdd_value;
    }
    //-----------------------------------------------------------
    // PRIVATE
    //-----------------------------------------------------------
    private static function isImage($fieldName)
    {
        $f_params = $_FILES[$fieldName];

        switch ($f_params['type']) {
            case 'image/gif':
            case 'image/pjpeg':
            case 'image/jpeg':
            case 'image/png':
                return true;
                break;
        }

        return false;
    }
    //-----------------------------------------------------------
    /**
     * Params:
     *   dbTable: tabla de la BD
     *   dbField: campo de la BD. (que se corresponde con el campo del formulario)
     */
    private static function getNewFileName($dbTable, $dbField)
    {
        // comprobar tabla
        $statusTabla = Db_mysql::getRowObject("SHOW TABLE STATUS LIKE '$dbTable'");
        if (!$statusTabla) {
            user_error("getName(): la tabla '$dbTable' no existe.", E_USER_WARNING);
            return false;
        }

        // ID
        if (Event::$ROW_ID) {
            $rowId = Event::$ROW_ID;
        } else {
            $rowId = $statusTabla->Auto_increment;
        }

        // New file name ---
        $fileName = $dbTable . '_' . $dbField . '_' . $rowId;

        /*
        Clave para que cambie el nombre cuando se actualiza(para resolver temas de caché)
        - ¡¡ Da problemas porque siempre se mantiene el nombre !!
         */
        $fileName .= '_' . time();

        return $fileName;
    }
    //-----------------------------------------------------------
}
