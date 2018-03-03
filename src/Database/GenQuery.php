<?php
/**
 * Generador de consultas SQL
 *
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 */

namespace angelrove\membrillo2\Database;

use angelrove\membrillo2\AppCms;
use angelrove\membrillo2\Login\Login;
use angelrove\membrillo2\WInputs\WInputFile\WInputFile_upload;
use angelrove\membrillo2\WObjectsStatus\Event;
use angelrove\membrillo2\WObjects\WForm\WForm;
use angelrove\membrillo2\DebugTrace;
use angelrove\membrillo2\Messages;
use angelrove\utils\Db_mysql;
use angelrove\utils\FileUploaded;

class GenQuery
{
    public static $executed_queries = array();

    //------------------------------------------------------------
    // Helpers
    //------------------------------------------------------------
    /*
     * Buscador: Concatenar filtros
     *  $listSql['campo1'] = "campo1 LIKE '%$_REQUEST[campo1]%'";
     *  ...
     *  Ejem.: GenQuery::getSqlFiltros($listSql, $_REQUEST);
     */
    public static function getSqlFiltros(array $listSql,
                                         array $listFiltros,
                                         $sep = 'AND',
                                         $pref = '')
    {
        $sqlFiltros = '';
        $sep        = ' ' . $sep . ' ';

        $c = 0;
        foreach ($listSql as $field => $strSql) {
            if ($listFiltros[$field] == '' || !$strSql) {
                continue;
            }

            if ($c > 0) {
                $sqlFiltros .= $sep;
            }

            $sqlFiltros .= "\n   " . $strSql;

            $c = 1;
        }

        // Pref ---
        if ($pref && $sqlFiltros) {
            $sqlFiltros = $pref . ' ' . $sqlFiltros;
        }

        return $sqlFiltros;
    }
    //------------------------------------------------------------------
    public static function helper_insert($DB_TABLE, array $listValuesPers = array())
    {
        // Parse from ---
        if ($errors = self::parseForm($DB_TABLE)) {
            return $errors;
        }

        // Insert ---
        if ($errors = self::insert($DB_TABLE, $listValuesPers)) {
            return $errors;
        }

        Messages::set("Insertado correctamente.");
    }
    //------------------------------------------------------------------
    public static function helper_update($DB_TABLE,
                                         array $listValuesPers = array(), $id = '')
    {
        // Parse from ---
        if ($errors = self::parseForm($DB_TABLE)) {
            return $errors;
        }

        // Update ---
        if ($errors = self::update($DB_TABLE, $listValuesPers, $id)) {
            return $errors;
        }

        // Messages::set("Guardado correctamente.");
    }
    //------------------------------------------------------------------
    // Parse form
    //------------------------------------------------------------------
    public static function parseForm($DB_TABLE,
                                     $id = '',
                                     array $uniques = array(),
                                     array $notNull = array())
    {
        global $app;

        $listErrors = array();

        if (!$id) {
            $id = Event::$ROW_ID;
        }

        $listFields = self::getTableProperties($DB_TABLE);

        /** user config **/
        foreach ($notNull as $fieldName) {
            $listFields[$fieldName]->obligatorio = 'true';
        }
        foreach ($uniques as $fieldName) {
            $listFields[$fieldName]->unique = 'true';
        }

        /** Parse Errors **/

        // Obligatorios ---
        foreach ($listFields as $fieldName => $fieldProp) {
            if (!$fieldProp->obligatorio) {
                continue;
            }
            if (!isset($_POST[$fieldName]) && !isset($_FILES[$fieldName]['name'])) {
                continue;
            }

            $value = '';

            // Type: File ---
            if ($fieldProp->type == 'file') {
                if ($_POST[$fieldName . '_isDelete'] == 0) {
                    $value = $_POST[$fieldName . '_prev'];
                }
                $value = $value . $_FILES[$fieldName]['name'];
            }
            // Type: Other ---
            else {
                $value = $_POST[$fieldName];
            }

            // Error
            if ($value == '' || $value == '00/00/0000') {
                $title                  = ($fieldProp->title) ? $fieldProp->title : $fieldName;
                $listErrors[$fieldName] = $title . ': ' . AppCms::$lang['GenQuery_error_obliga'];
            }
        }

        // Unique ---------
        foreach ($listFields as $fieldName => $fieldProp) {
            $postValue = (isset($_POST[$fieldName])) ? $_POST[$fieldName] : '';

            if (!$fieldProp->unique) {
                continue;
            }
            if (!$postValue && !$fieldProp->obligatorio) {
                continue;
            }

            $sqlQ = "SELECT id FROM $DB_TABLE WHERE `$fieldName`='$postValue' AND id <> '$id'";
            if (Db_mysql::getValue($sqlQ)) {
                $title = ($fieldProp->title) ? $fieldProp->title : $fieldName;
                $listErrors[$fieldName] .= $title . ': ' . AppCms::$lang['GenQuery_error_unique'];
            }
        }

        // Values --------
        // foreach($listFields as $fieldName => $fieldProp)
        // {
        //    if($fieldProp->type == 'tinyint') {
        //       if($_POST[$fieldName] > 255) {
        //          $title = ($fieldProp->title)? $fieldProp->title : $fieldName;
        //          $listErrors[$fieldName] .= $title.': Out of range value';
        //       }
        //    }
        // }

        /** Out errors **/
        WForm::update_setErrors($listErrors);
        return $listErrors;
    }
    //------------------------------------------------------------------
    // SELECT
    //------------------------------------------------------------------
    public static function selectFiltros($DB_TABLE, $sqlFiltros)
    {
        $strFiltros = '';
        if ($sqlFiltros) {
            $strFiltros = " WHERE $sqlFiltros";
        }

        $sqlQ = self::select($DB_TABLE);
        return $sqlQ . $strFiltros;
    }
    //------------------------------------------------------------------
    public static function select($DB_TABLE)
    {
        // Formatear salida
        $strDates   = '';
        $listFields = self::getTableProperties($DB_TABLE);

        foreach ($listFields as $fieldName => $fieldProp) {
            switch ($fieldProp->type) {
                case 'date':
                    $strDates .= ",\n DATE_FORMAT($fieldName, '%d/%m/%Y') AS " . $fieldName . "_format";
                    break;

                case 'timestamp':
                case 'datetime':
                    $strDates .= ",\n DATE_FORMAT($fieldName, '%d/%m/%Y %H:%i') AS " . $fieldName . "_format";
                    break;

                case 'file':
                    $strDates .= ",\n SUBSTRING_INDEX($fieldName, '#', 1) AS " . $fieldName . "_format";
                    break;
            }
        }

        // Query
        return "SELECT * $strDates \nFROM $DB_TABLE";
    }
    //------------------------------------------------------------------
    public static function selectRow($DB_TABLE, $id)
    {
        // Formatear salida
        $strDates   = '';
        $listFields = self::getTableProperties($DB_TABLE);
        foreach ($listFields as $fieldName => $fieldProp) {
            switch ($fieldProp->type) {
                case 'date':
                    $strDates .= ",\n DATE_FORMAT($fieldName, '%d/%m/%Y') AS " . $fieldName.'_format';
                    break;

                case 'timestamp':
                case 'datetime':
                    $strDates .= ",\n DATE_FORMAT($fieldName, '%d/%m/%Y %H:%i:%s') AS " . $fieldName.'_format';
                    break;
            }
        }

        // Query
        return "SELECT * $strDates \nFROM $DB_TABLE \nWHERE id='$id' LIMIT 1";
    }
    //------------------------------------------------------------------
    // INSERT
    //------------------------------------------------------------------
    public static function insert($DB_TABLE, array $listValuesPers = array())
    {
        // Query --------
        $sqlQ = self::getQueryInsert($DB_TABLE, $listValuesPers);

        // Errors
        if (isset($sqlQ->errors)) {
            WForm::update_setErrors($sqlQ->errors);
            return $sqlQ->errors;
        }

        // Exec query
        try {
            Db_mysql::query($sqlQ);
        } catch (\Exception $e) {
            // WForm::update_setErrors(array('SQL', $e->getMessage()));
            throw $e;
        }

        self::$executed_queries[] = $sqlQ;
        //self::log_updates($sqlQ); // Log

        // Envío el nuevo ROW_ID al evento en curso
        Event::setRowId(Db_mysql::insert_id());

        DebugTrace::out('GenQuery::insert()', $sqlQ);

        return;
    }
    //------------------------------------------------------------------
    public static function getQueryInsert($DB_TABLE, array $listValuesPers = array())
    {
        /** Recorrer los campos **/
        $strFields  = '';
        $strValues  = '';
        $listFields = self::getTableProperties($DB_TABLE);

        foreach ($listFields as $fieldName => $fieldProp) {
            // Valor
            $value = '';
            if (isset($listValuesPers[$fieldName])) {
                $value = $listValuesPers[$fieldName];
            } else {
                $value = self::getValueToInsert($DB_TABLE, $fieldName, $fieldProp->type);

                if (isset($value->errors)) {
                    return $value;
                }
                if ($value === false) {
                    continue;
                }
            }

            // Query
            $strFields .= ",`$fieldName`";
            $strValues .= ",$value";
        }

        $strFields{0} = ' ';
        $strValues{0} = ' ';

        /** Query **/
        return "INSERT INTO $DB_TABLE ($strFields) VALUES ($strValues)";
    }
    //------------------------------------------------------------------
    // UPDATE
    //------------------------------------------------------------------
    public static function update($DB_TABLE, array $listValuesPers = array(), $id = '')
    {
        //------------
        if (!$id) {
            $id = Event::$ROW_ID;
        }

        /** Query **/
        $sqlQ = self::getQueryUpdate($DB_TABLE, $id, $listValuesPers);

        /** Errors **/
        if (isset($sqlQ->errors) && $sqlQ->errors) {
            WForm::update_setErrors($sqlQ->errors);
            return $sqlQ->errors;
        }

        /** Exec Query **/
        if ($sqlQ) {
            try {
                Db_mysql::query($sqlQ);
            } catch (\Exception $e) {
                // WForm::update_setErrors(array('SQL', $e->getMessage()));
                throw $e;
            }

            self::$executed_queries[] = $sqlQ;
            self::log_updates($sqlQ); // Log
        }

        DebugTrace::out('GenQuery::update()', $sqlQ);
        return;
    }
    //------------------------------------------------------------------
    public static function getQueryUpdate($DB_TABLE, $id, array $listValuesPers = array())
    {
        /** Recorrer los campos **/
        $strValues  = '';
        $listFields = self::getTableProperties($DB_TABLE);

        foreach ($listFields as $fieldName => $fieldProp) {
            // Valor
            $value = '';
            if (isset($listValuesPers[$fieldName])) {
                $value = $listValuesPers[$fieldName];
            } else {
                $value = self::getValueToInsert($DB_TABLE, $fieldName, $fieldProp->type);
                if (isset($value->errors)) {
                    return $value;
                }
                if ($value === false) {
                    continue;
                }

            }

            // Query
            $strValues .= ",\n `$fieldName`=$value";
        }

        /** Query **/
        $sqlQ = '';
        if ($strValues) {
            $strValues{0} = ' ';
            $sqlQ         = "UPDATE $DB_TABLE \nSET $strValues \nWHERE id='$id'";
        }

        return $sqlQ;
    }
    //------------------------------------------------------------------
    // DELETE
    //------------------------------------------------------------------
    /* Delete row and uploaded files */
    public static function delete($DB_TABLE)
    {
        $sqlQ = self::getQueryDelete($DB_TABLE, Event::$ROW_ID);
        Db_mysql::query($sqlQ);
        self::$executed_queries[] = $sqlQ;

        self::log_updates($sqlQ); // Log

        // Delete in session
        Event::delRowId();

        DebugTrace::out('GenQuery::delete()', $sqlQ);

        return;
    }
    //-----------
    public static function getQueryDelete($DB_TABLE, $id)
    {
        global $seccCtrl;

        $listFields = self::getTableProperties($DB_TABLE);

        /** Delete files **/
        foreach ($listFields as $fieldName => $fieldProp) {
            switch ($fieldProp->type) {
                case 'file':
                    $bbdd_file  = Db_mysql::getValue("SELECT $fieldName FROM $DB_TABLE WHERE id='$id'");
                    if (!$bbdd_file) {
                        break;
                    }

                    $paramsFile = FileUploaded::getInfo($bbdd_file, $seccCtrl->UPLOADS_DIR);
                    if ($paramsFile['name']) {
                        DebugTrace::out('getQueryDelete(): unlink 1:', "'$paramsFile[path_completo]'");
                        DebugTrace::out('getQueryDelete(): unlink 2:', "'$paramsFile[path_completo_th]'");

                        unlink($paramsFile['path_completo']); // archivo
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
    public static function log_updates($sqlQ)
    {
        global $CONFIG_APP;

        if (!$CONFIG_APP['debug']['LOG_UPDATES']) {
            return;
        }

        $user_login = Login::$login;
        $strSql     = addslashes($sqlQ);

        $ip = $_SERVER['REMOTE_ADDR'];
        //$origen   = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
        $origen     = $_SERVER['REQUEST_URI'];
        $USER_AGENT = addslashes($_SERVER['HTTP_USER_AGENT']);

        $sqlQ = "INSERT INTO sys_log_updates(user_login, ip, user_agent, origen, sqlQ)
             VALUES('$user_login', '$ip', '$USER_AGENT', '$origen', '$strSql')";
        Db_mysql::query($sqlQ);
    }
    //------------------------------------------------------------------
    private static function getTableProperties($table)
    {
        $listFields = Db_mysql::getListNoId("SHOW FULL COLUMNS FROM $table");
        if (!$listFields) {
            user_error("DBProperties(): la tabla [$table] no existe", E_USER_WARNING);
            return false;
        }

        $tableProp = array();
        foreach ($listFields as $field) {
            $nombreCampo = $field['Field'];
            if ($nombreCampo == 'id') {
                continue;
            }

            $tableProp[$nombreCampo]        = new \stdClass();
            $tableProp[$nombreCampo]->title = '';

            // Propiedades a través de MySql
            $tableProp[$nombreCampo]->type = ($field['Type'] == 'timestamp' || $field['Type'] == 'datetime') ?
            $field['Type'] :
            trim(substr($field['Type'], 0, 7));

            $tableProp[$nombreCampo]->obligatorio = ($field['Null'] == 'NO') ? 'true' : '';
            $tableProp[$nombreCampo]->unique      = ($field['Key'] == 'UNI') ? 'true' : '';

            // Propiedades a través del comentario
            if ($field['Comment']) {
                $field['Comment'] = str_replace(";", "&", $field['Comment']);
                parse_str($field['Comment'], $output);

                if (isset($output['title'])) {
                    $tableProp[$nombreCampo]->title = $output['title'];
                }
                if (isset($output['type'])) {
                    $tableProp[$nombreCampo]->type = $output['type'];
                }
            }
        }

        //DebugTrace::out('TableProperties()', $tableProp);
        return $tableProp;
    }
    //------------------------------------------------------------------
    private static function getValueToInsert($DB_TABLE, $fieldName, $fieldType)
    {
        $inputValue = '';

        if (isset($_POST[$fieldName])) {
            $inputValue = $_POST[$fieldName];
        } elseif (isset($_FILES[$fieldName])) {
        } else {
            return false;
        }

        // Formatear entrada según el tipo ---
        //echo "'$fieldName' >> '$fieldType' = '$inputValue'<br>";
        switch ($fieldType) {
            //-------
            case 'date':
                $value = "STR_TO_DATE('$inputValue', '%d/%m/%Y')";
                break;
            //-------
            case 'timestamp':
                $value = "$inputValue";
                break;
            //-------
            case 'datetime':
                $value = "'$inputValue'";
                break;
            //-------
            case 'file':
                if (count($_FILES) == 0) {
                    throw new \Exception("ERROR [upload], Make sure the form have 'enctype=\"multipart/form-data\"'", E_USER_ERROR);
                }

                $datosFile = WInputFile_upload::getFile($DB_TABLE, $fieldName);
                if (isset($datosFile->errors)) {
                    $value = $datosFile;
                } else {
                    $value = "'$datosFile'";
                }
                break;
            //-------
            default:
                $value = "'$inputValue'";
                break;
                //-------
        }

        return $value;
    }
    //------------------------------------------------------------------
}
