<?php
/**
 * Generador de consultas SQL
 *
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 */

namespace angelrove\membrillo\Database;

use angelrove\membrillo\Login\Login;
use angelrove\membrillo\WInputs\WInputFile\WInputFile_upload;
use angelrove\membrillo\WObjectsStatus\Event;
use angelrove\membrillo\WObjects\WForm\WForm;
use angelrove\membrillo\WApp\Local;
use angelrove\membrillo\DebugTrace;
use angelrove\membrillo\Messages;
use angelrove\utils\Db_mysql;
use angelrove\utils\FileUploaded;

class GenQuery
{
    public static $executed_queries = [];

    //------------------------------------------------------------
    // Helpers
    //------------------------------------------------------------
    /**
     * Si $conditions contiene "[VALUE]" o es un array, se considera que viene de buscador
     */
    public static function getSqlFilters(array $filter_conditions, array $filter_data = []): string
    {
        $listWhere = [];
        foreach ($filter_conditions as $key => $condition) {
            /*
             * Viene de Buscador (rango de valores):
             *
             * $conditions['f_deleted'] = [
             *    'default' => "A.deleted_at IS NULL",
             *            1 => "A.deleted_at IS NOT NULL",
             * ];
             */
            if (is_array($condition)) {
                if (isset($filter_data[$key]) && isset($condition[$filter_data[$key]])) {
                    $listWhere[] = $condition[$filter_data[$key]];
                }
                // condición por defecto (key 'default')
                elseif (isset($condition['default'])) {
                    $listWhere[] = $condition['default'];
                }
            }
            /* Viene de Buscador ($condition contiene "[VALUE]") */
            elseif (strpos($condition, '[VALUE]') !== false) {
                if (isset($filter_data[$key]) && $filter_data[$key]) {
                    $replaceVal = $filter_data[$key];
                    $listWhere[] = str_replace('[VALUE]', $replaceVal, $condition);
                }
            }
            /* Se incluye siempre */
            else {
                $listWhere[] = $condition;
            }
        }

        $sqlFilters = \angelrove\utils\UtilsBasic::implode(' AND ', $listWhere);

        if ($sqlFilters) {
            $sqlFilters = ' WHERE '.$sqlFilters;
        }

        return $sqlFilters;
    }
    //------------------------------------------------------------
    /*
     * Buscador: Concatenar filtros
     *  $listSql['campo1'] = "campo1 LIKE '%$_REQUEST[campo1]%'";
     *  ...
     *  Ejem.: GenQuery::getSqlFiltros($listSql, $_REQUEST);
     */
    public static function getSqlFiltros(array $listSql, array $filtros, $sep = 'AND', $pref = ''): string
    {
        $sqlFiltros = '';
        $sep = ' ' . $sep . ' ';

        $c = 0;
        foreach ($listSql as $field => $query) {
            if (!isset($filtros[$field]) || !$filtros[$field] || !$query) {
                continue;
            }

            // new $listSql format ---
            if (is_array($query)) {
                $query = $query[$filtros[$field]];
            }

            //----
            if ($c > 0) {
                $sqlFiltros .= $sep;
            }
            $sqlFiltros .= "\n   " . $query;
            $c = 1;
        }

        // Pref ---
        if ($pref && $sqlFiltros) {
            $sqlFiltros = $pref . ' ' . $sqlFiltros;
        }

        return $sqlFiltros;
    }
    //------------------------------------------------------------------
    public static function helper_insert($DB_TABLE, array $listValuesPers = [], $messageAuto = true): ?array
    {
        // Parse from ---
        if ($errors = self::parseForm($DB_TABLE)) {
            return $errors;
        }

        // Insert ---
        if ($errors = self::insert($DB_TABLE, $listValuesPers)) {
            return $errors;
        }

        if ($messageAuto) {
            Messages::set(Local::$t['Saved']);
        }

        return null;
    }
    //------------------------------------------------------------------
    public static function helper_update($DB_TABLE, array $listValuesPers = [], $id = ''): ?array
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
        return null;
    }
    //------------------------------------------------------------------
    // Parse form
    //------------------------------------------------------------------
    public static function parseForm($DB_TABLE, $id = '', array $uniques = [], array $notNull = []): array
    {
        global $app;

        $listErrors = [];

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
            } else {
                $value = $_POST[$fieldName];
            }

            // Error
            if ($value == '' || $value == '00/00/0000') {
                $title                  = ($fieldProp->title) ? $fieldProp->title : $fieldName;
                $listErrors[$fieldName] = $title . ': ' . Local::$t['GenQuery_error_obliga'];
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
                $listErrors[$fieldName] = $title . ' = ' . $postValue . ' ' . Local::$t['GenQuery_error_unique'];
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
    public static function selectFiltros($DB_TABLE, $sqlFiltros): string
    {
        $strFiltros = '';
        if ($sqlFiltros) {
            $strFiltros = " WHERE $sqlFiltros";
        }

        $sqlQ = self::select($DB_TABLE);
        return $sqlQ . $strFiltros;
    }
    //------------------------------------------------------------------
    public static function select($DB_TABLE): string
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
                    $strDates .= ",\n UNIX_TIMESTAMP($fieldName) AS " . $fieldName . "_unix";
                    break;

                case 'file':
                    $strDates .= ",\n SUBSTRING_INDEX($fieldName, '#', 1) AS " . $fieldName . "_format";
                    break;
            }
        }

        // Query
        $sqlQ = "SELECT * $strDates \nFROM $DB_TABLE";
        return $sqlQ;
    }
    //------------------------------------------------------------------
    public static function selectRow($DB_TABLE, $id): string
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
    public static function insert($DB_TABLE, array $listValuesPers = [])
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
    public static function getQueryInsert($DB_TABLE, array $listValuesPers = [])
    {
        /** Recorrer los campos **/
        $strFields  = '';
        $strValues  = '';
        $listFields = self::getTableProperties($DB_TABLE);

        foreach ($listFields as $fieldName => $fieldProp) {
            // Value user ----
            $value = '';
            if (isset($listValuesPers[$fieldName])) {
                $value = $listValuesPers[$fieldName];
                $value = self::parseValue($value, $fieldName, $fieldProp->type);
            }
            // Value _POST ---
            else {
                $value = self::getValueFromRequest($DB_TABLE, $fieldName, $fieldProp->type);

                if (isset($value->errors)) {
                    return $value;
                }
                if ($value === null) {
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
    public static function update($DB_TABLE, array $listValuesPers = [], $id = '')
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

            // Envío el nuevo ROW_ID al evento en curso
            Event::setRowId($id);

            self::$executed_queries[] = $sqlQ;
            self::log_updates($sqlQ); // Log
        }

        DebugTrace::out('GenQuery::update()', $sqlQ);
        return;
    }
    //------------------------------------------------------------------
    public static function getQueryUpdate($DB_TABLE, $id, array $listValuesPers = [])
    {
        /** Recorrer los campos **/
        $strValues  = '';
        $listFields = self::getTableProperties($DB_TABLE);

        foreach ($listFields as $fieldName => $fieldProp) {
            $value = '';

            // Value ---
            if (isset($listValuesPers[$fieldName])) {
                $value = $listValuesPers[$fieldName];
                $value = self::parseValue($value, $fieldName, $fieldProp->type);
            }
            // Value ---
            else {
                $value = self::getValueFromRequest($DB_TABLE, $fieldName, $fieldProp->type);
                if (isset($value->errors)) {
                    return $value;
                }
                if ($value === null) {
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

        // print_r2("DEBUG: ".$sqlQ);
        return $sqlQ;
    }
    //------------------------------------------------------------------
    // DELETE
    //------------------------------------------------------------------
    public static function softDelete($DB_TABLE, $id=''): int
    {
        $ROW_ID = ($id)? $id : Event::$ROW_ID;

        $sqlQ = "UPDATE " . $DB_TABLE . " SET deleted_at=NOW() WHERE id='" . Event::$ROW_ID . "'";
        Db_mysql::query($sqlQ);
        self::$executed_queries[] = $sqlQ;

        self::log_updates($sqlQ); // Log

        DebugTrace::out('GenQuery::softDelete()', $sqlQ);

        return Event::$ROW_ID;
    }
    //-----------
    /* Delete row and uploaded files */
    public static function delete($DB_TABLE, $id=''): int
    {
        $ROW_ID = ($id)? $id : Event::$ROW_ID;

        $sqlQ = self::getQueryDelete($DB_TABLE, $ROW_ID);
        Db_mysql::query($sqlQ);
        self::$executed_queries[] = $sqlQ;

        self::log_updates($sqlQ); // Log

        // Delete in session
        Event::delRowId();

        DebugTrace::out('GenQuery::delete()', $sqlQ);

        return $ROW_ID;
    }
    //-----------
    public static function getQueryDelete($DB_TABLE, $id): string
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
        if (!LOG_SQL) {
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
    private static function getTableProperties(string $table): ?array
    {
        $listFields = Db_mysql::getListNoId("SHOW FULL COLUMNS FROM $table");
        if (!$listFields) {
            user_error("DBProperties(): la tabla [$table] no existe", E_USER_WARNING);
            return null;
        }

        $tableProp = [];
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
    private static function getValueFromRequest($DB_TABLE, $fieldName, $fieldType): ?string
    {
        $inputValue = '';

        // POST ---
        if (isset($_POST[$fieldName])) {
            $inputValue = $_POST[$fieldName];
        } elseif (isset($_FILES[$fieldName])) {
        } else {
            return null;
        }

        // FILES ---
        if ($fieldType == 'file') {
            if (count($_FILES) == 0) {
                throw new \Exception("ERROR [upload], Make sure the form have 'enctype=\"multipart/form-data\"'", E_USER_ERROR);
            }

            $inputValue = WInputFile_upload::getFile($DB_TABLE, $fieldName);
            if (isset($inputValue->errors)) {
                throw new \Exception("ERROR [upload] with column '$fieldName'", E_USER_ERROR);
            }
        }

        return self::parseValue($inputValue, $fieldName, $fieldType);
    }
    //------------------------------------------------------------------
    private static function parseValue($inputValue, $fieldName, $fieldType): string
    {
        // Trim ---
        $inputValue = trim($inputValue);

        // NULL ---
        if ($inputValue == 'NULL') {
            return $inputValue;
        }

        // Password ---
        global $CONFIG_APP;
        if ($fieldName == 'password' && $CONFIG_APP['login']['LOGIN_HASH']) {
            $value = password_hash($inputValue, PASSWORD_BCRYPT);
            return "'$value'";
        }

        // Column type ---
        switch ($fieldType) {
            case 'date':
                $value = "STR_TO_DATE('$inputValue', '%d/%m/%Y')";
                break;

            case 'timestamp':
            case 'datetime':
                if ($inputValue == 'NOW()') {
                    $value = $inputValue;
                } else {
                    $value = "'$inputValue'";
                }
                break;

            default:
                $value = "'$inputValue'";
                break;
        }
        // print_r2("DEBUG: '$fieldName' >> $fieldType: $value");

        return $value;
    }
    //------------------------------------------------------------------
}
