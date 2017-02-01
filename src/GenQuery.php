<?
/**
 * Generadores de consultas SQL
 *
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 */

namespace angelrove\membrillo2;

use angelrove\membrillo2\WInputs\WInputFile\WInputFile_upload;
use angelrove\membrillo2\SysTrazas\SysTrazas;
use angelrove\membrillo2\WMessages\WMessages;

use angelrove\utils\FileUploaded;
use angelrove\utils\Db_mysql;


class GenQuery
{
  //------------------------------------------------------------------
  // Parse form
  //------------------------------------------------------------------
  static public function parseForm($DB_TABLE, $id='', $uniques=array(), $notNull=array())
  {
    global $seccCtrl, $LOCAL;

    if(!$id) $id = $seccCtrl->ROW_ID;
    $listFields = self::getTableProperties($DB_TABLE);

    /** user config **/
    foreach($notNull as $fieldName) {
       $listFields[$fieldName]->obligatorio = 'true';
    }
    foreach($uniques as $fieldName) {
       $listFields[$fieldName]->unique = 'true';
    }

    /** Parse Errors **/
    $listErrors = '';

    // Obligatorios ---
    foreach($listFields as $fieldName => $fieldProp) {
       if(!$fieldProp->obligatorio) {
          continue;
       }
       if(!isset($_POST[$fieldName]) && !isset($_FILES[$fieldName]['name'])) {
          continue;
       }

       $value = '';

       // Type File
       if($fieldProp->type == 'file') {
          if($_POST[$fieldName.'_isDelete'] == 0) {
             $value = $_POST[$fieldName.'_prev'];
          }
          $value = $value.$_FILES[$fieldName]['name'];
       }
       // Otros
       else {
          $value = $_POST[$fieldName];
       }

       // Error
       if($value == '' || $value == '00/00/0000') {
          $title = ($fieldProp->title)? $fieldProp->title : $fieldName;
          $listErrors[$fieldName] = $title.': '.$LOCAL['GenQuery_error_obliga'];
       }
    }

    // Unique ---------
    foreach($listFields as $fieldName => $fieldProp)
    {
       $postValue = (isset($_POST[$fieldName]))? $_POST[$fieldName] : '';

       if(!$fieldProp->unique) {
          continue;
       }
       if(!$postValue && !$fieldProp->obligatorio) {
          continue;
       }

       $sqlQ = "SELECT id FROM $DB_TABLE WHERE `$fieldName`='$postValue' AND id <> '$id'";
       if(Db_mysql::getValue($sqlQ)) {
          $title = ($fieldProp->title)? $fieldProp->title : $fieldName;
          $listErrors[$fieldName] .= $title.': '.$LOCAL['GenQuery_error_unique'];
       }
    }

    /** Out errors **/
    if($listErrors) {
       $seccCtrl->reverseEvent(); // mantenerse en la misma pantalla
       self::parseForm_resalta($listErrors);
       return $listErrors;
    }
  }
  //-------------------------------
  static public function parseForm_resalta($errors)
  {
    //WMessages::set(print_r($errors, true));
    WMessages::set('- Hay errores:');

    $Js = '';
    foreach($errors as $name=>$err) {
      $Js .= "$('[name=\"$name\"]').css('border', '2px solid red');"; // resaltar campos
      WMessages::set($err);
    }

    $Js .= "$('[name=\"".key($errors)."\"]').focus()"; // foco en el primer input erroneo
    WMessages::set('<script>'.$Js.'</script>');
  }
  //------------------------------------------------------------------
  // SELECT
  //------------------------------------------------------------------
  static public function selectFiltros($DB_TABLE, $sqlFiltros)
  {
    $strFiltros = '';
    if($sqlFiltros) { $strFiltros = " WHERE $sqlFiltros"; }

    $sqlQ = self::select($DB_TABLE);
    return $sqlQ . $strFiltros;
  }
  //------------------------------------------------------------------
  static public function select($DB_TABLE) {
    // Formatear salida
    $strDates = '';
    $listFields = self::getTableProperties($DB_TABLE);
    foreach($listFields as $fieldName => $fieldProp) {
       switch($fieldProp->type) {
         case 'date':
           $strDates .= ",\n DATE_FORMAT($fieldName, '%d/%m/%Y') AS ".$fieldName."_format";
         break;

         case 'timestamp':
         case 'datetime':
           $strDates .= ",\n DATE_FORMAT($fieldName, '%d/%m/%Y %H:%i') AS ".$fieldName."_format";
         break;

         case 'file':
           $strDates .= ",\n SUBSTRING_INDEX($fieldName, '#', 1) AS ".$fieldName."_format";
         break;
       }
    }

    // Query
    $sqlQ = "SELECT * $strDates \nFROM $DB_TABLE";
    return $sqlQ;
  }
  //------------------------------------------------------------------
  static public function selectRow($DB_TABLE, $id)
  {
    // Formatear salida
    $strDates = '';
    $listFields = self::getTableProperties($DB_TABLE);
    foreach($listFields as $fieldName => $fieldProp) {
       switch($fieldProp->type) {
         case 'date':
           $strDates .= ",\n DATE_FORMAT($fieldName, '%d/%m/%Y') AS ".$fieldName;
         break;

         case 'timestamp':
         case 'datetime':
           $strDates .= ",\n DATE_FORMAT($fieldName, '%d/%m/%Y %H:%i:%s') AS ".$fieldName;
         break;
       }
    }

    // Query
    $sqlQ = "SELECT * $strDates \nFROM $DB_TABLE \nWHERE id='$id' LIMIT 1";
    return $sqlQ;
  }
  //------------------------------------------------------------------
  // INSERT
  //------------------------------------------------------------------
  static public function insert($DB_TABLE, $listValuesPers=array())
  {
    global $seccCtrl;

    /** Query **/
    $sqlQ = self::getQueryInsert($DB_TABLE, $listValuesPers);

    /** Errores **/
    if($sqlQ->errors) {
       $seccCtrl->reverseEvent(); // mantenerse en la misma pantalla
       self::parseForm_resalta($sqlQ->errors);
       return $sqlQ->errors;
    }

    /** Ejecutar Query **/
    Db_mysql::query($sqlQ);
    //self::log($sqlQ); // Log

    /** La nueva tupla activa **/
    $seccCtrl->setRowId($seccCtrl->CONTROL, Db_mysql::insert_id());

    SysTrazas::out('GenQuery::insert()', $sqlQ);
    return;
  }
  //---------
  static public function getQueryInsert($DB_TABLE, $listValuesPers=array())
  {
    /** Recorrer los campos **/
    $strFields = '';
    $strValues = '';
    $listFields = self::getTableProperties($DB_TABLE);

    foreach($listFields as $fieldName => $fieldProp) {
       // Valor
       $value = '';
       if(isset($listValuesPers[$fieldName])) {
          $value = $listValuesPers[$fieldName];
       } else {
          $value = self::getValueToInsert($DB_TABLE, $fieldName, $fieldProp->type);
          if($value->errors) return $value;
          if($value === false) continue;
       }

       // Query
       $strFields .= ",`$fieldName`";
       $strValues .= ",$value";
    }

    $strFields{0} = ' ';
    $strValues{0} = ' ';

    /** Query **/
    $sqlQ = "INSERT INTO $DB_TABLE ($strFields) VALUES ($strValues)";
    return $sqlQ;
  }
  //------------------------------------------------------------------
  // UPDATE
  //------------------------------------------------------------------
  static public function update($DB_TABLE, $listValuesPers=array(), $id='')
  {
    global $seccCtrl;
    if(!$id) $id = $seccCtrl->ROW_ID;

    /** Query **/
    $sqlQ = self::getQueryUpdate($DB_TABLE, $id, $listValuesPers);
    // print_r2("$sqlQ");exit();

    /** Errores **/
    if($sqlQ->errors) {
       $seccCtrl->reverseEvent(); // mantenerse en la misma pantalla
       self::parseForm_resalta($sqlQ->errors);
       return $sqlQ->errors;
    }

    /** Ejecutar Query **/
    if($sqlQ) {
       Db_mysql::query($sqlQ);
       self::log($sqlQ); // Log
    }

    SysTrazas::out('GenQuery::update()', $sqlQ);
    return;
  }
  //---------
  static public function getQueryUpdate($DB_TABLE, $id, $listValuesPers=array())
  {
    /** Recorrer los campos **/
    $strValues = '';
    $listFields = self::getTableProperties($DB_TABLE);

    foreach($listFields as $fieldName => $fieldProp) {
       // Valor
       $value = '';
       if(isset($listValuesPers[$fieldName])) {
          $value = $listValuesPers[$fieldName];
       }
       else {
          $value = self::getValueToInsert($DB_TABLE, $fieldName, $fieldProp->type);
          if(isset($value->errors)) {
             return $value;
          }
          if($value === false) continue;
       }

       // Query
       $strValues .= ",\n `$fieldName`=$value";
    }

    /** Query **/
    $sqlQ = '';
    if($strValues) {
       $strValues{0} = ' ';
       $sqlQ = "UPDATE $DB_TABLE \nSET $strValues \nWHERE id='$id'";
    }

    return $sqlQ;
  }
  //------------------------------------------------------------------
  // DELETE
  //------------------------------------------------------------------
  /* Delete row and uploaded files */
  static public function delete($DB_TABLE)
  {
    global $seccCtrl;

    $sqlQ = self::getQueryDelete($DB_TABLE, $seccCtrl->ROW_ID);
    Db_mysql::query($sqlQ);

    self::log($sqlQ); // Log

    // Delete in session
    $seccCtrl->delRowId($seccCtrl->CONTROL);

    SysTrazas::out('GenQuery::delete()', $sqlQ);
    return;
  }
  //-----------
  static public function getQueryDelete($DB_TABLE, $id)
  {
    global $CONFIG_APP, $seccCtrl;

    $listFields = self::getTableProperties($DB_TABLE);

    /** Delete files **/
     foreach($listFields as $fieldName => $fieldProp) {
        switch($fieldProp->type) {
          case 'file':
            $bbdd_file = Db_mysql::getValue("SELECT $fieldName FROM $DB_TABLE WHERE id='$id'");
            $paramsFile = FileUploaded::getInfo($bbdd_file, $seccCtrl->UPLOADS_DIR);

            if($paramsFile['name']) {
               SysTrazas::out('getQueryDelete(): unlink 1:', "'$paramsFile[path_completo]'");
               SysTrazas::out('getQueryDelete(): unlink 2:', "'$paramsFile[path_completo_th]'");

                unlink($paramsFile['path_completo']);    // archivo
               @unlink($paramsFile['path_completo_th']); // if thumbnail
            }
          break;
        }
     }

    /** Query **/
     $sqlQ = "DELETE FROM $DB_TABLE WHERE id='$id'";
     return $sqlQ;
  }
  //------------------------------------------------------------------
  // PRIVATE
  //------------------------------------------------------------------
  static public function log($sqlQ)
  {
    global $CONFIG_APP, $appLogin;
    if(!$CONFIG_APP['debug']['LOG_UPDATES']) return;

    $usuario = $appLogin->login;
    $strSql  = addslashes($sqlQ);

    $ip         = $_SERVER['REMOTE_ADDR'];
    //$origen   = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
    $origen     = $_SERVER['REQUEST_URI'];
    $USER_AGENT = addslashes($_SERVER['HTTP_USER_AGENT']);

    $sqlQ = "INSERT INTO log_updates(usuario, ip, user_agent, origen, sqlQ)
             VALUES('$usuario', '$ip', '$USER_AGENT', '$origen', '$strSql')";
    Db_mysql::query($sqlQ);
  }
  //------------------------------------------------------------------
  static private function getTableProperties($table)
  {
    $listFields = Db_mysql::getListNoId("SHOW FULL COLUMNS FROM $table");
    if(!$listFields) {
       user_error("DBProperties(): la tabla [$table] no existe", E_USER_WARNING);
       return false;
    }

    $tableProp = array();
    foreach($listFields as $field) {
       $nombreCampo = $field['Field'];
       if($nombreCampo == 'id') continue;

       $tableProp[$nombreCampo] = new \stdClass();

       // Propiedades a través de MySql
       $tableProp[$nombreCampo]->type        = ($field['Type'] == 'timestamp' || $field['Type'] == 'date' || $field['Type'] == 'datetime')? $field['Type'] : '';
       $tableProp[$nombreCampo]->obligatorio = ($field['Null'] == 'NO') ? 'true' : '';
       $tableProp[$nombreCampo]->unique      = ($field['Key']  == 'UNI')? 'true' : '';

       // Propiedades a través del comentario
       if($field['Comment']) {
          $field['Comment'] = str_replace(";", "&", $field['Comment']);
          parse_str($field['Comment'], $output);

          if(isset($output['title'])) {
             $tableProp[$nombreCampo]->title = $output['title'];
          }

          if(isset($output['type'])) {
             $tableProp[$nombreCampo]->type = $output['type'];
          }
       }
    }

    //SysTrazas::out('TableProperties()', $tableProp);
    return $tableProp;
  }
  //------------------------------------------------------------------
  static private function getValueToInsert($DB_TABLE, $fieldName, $fieldType)
  {
    if(!isset($_POST[$fieldName]) && !isset($_FILES[$fieldName])) {
       return false;
    }

    // Formatear entrada según el tipo
    $value = false;
    $fieldValue = $_POST[$fieldName];

    //echo "'$fieldName' >> '$fieldType' = '$fieldValue'<br>";
    switch($fieldType) {
      case 'date':
        $value = "STR_TO_DATE('$fieldValue', '%d/%m/%Y')";
      break;

      case 'timestamp':
        $value = "$fieldValue";
      break;

      case 'datetime':
        $value = "'$fieldValue'";
      break;

      case 'file':
        if(count($_FILES) == 0) {
           print_r2("ERROR [upload], asegurate de que el formulario contiene 'enctype=\"multipart/form-data\"'");
        }
        $datosFile = WInputFile_upload::getFile($DB_TABLE, $fieldName);

        if($datosFile->errors) {
           return $datosFile;
        }
        $value = "'$datosFile'";
      break;

      default:
        $value = "'$fieldValue'";
      break;
    }

    return $value;
  }
  //------------------------------------------------------------------
}

