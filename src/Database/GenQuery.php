<?php
/**
 * Generador de consultas SQL
 *
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 */

namespace angelrove\membrillo\Database;

use angelrove\membrillo\Login\Login;
use angelrove\membrillo\Messages;
use angelrove\membrillo\WInputs\WInputFile\WInputFile_upload;
use angelrove\membrillo\WObjectsStatus\Event;
use angelrove\membrillo\WObjects\WForm\WForm;
use angelrove\membrillo\WApp\Local;
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
    public static function parseFilters(array $filter_conditions, array $filter_data = []): array
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

        return $listWhere;
    }

    public static function getSqlFilters(array $filter_conditions, array $filter_data = []): string
    {
        // Parse filters ---
        $listWhere = self::parseFilters($filter_conditions, $filter_data);

        // Implode list ---
        $sqlFilters = \angelrove\utils\UtilsBasic::implode(' AND ', $listWhere);

        if ($sqlFilters) {
            $sqlFilters = ' WHERE '.$sqlFilters;
        }

        return $sqlFilters;
    }

    public static function getStrWhere(array $filter_conditions, array $filter_data = []): string
    {
        $listWhere = self::parseFilters($filter_conditions, $filter_data);

        return \angelrove\utils\UtilsBasic::implode(' AND ', $listWhere);
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
    public static function helper_insert(string $DB_TABLE, array $listValuesPers = [], $messageAuto = true): ?array
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
    public static function helper_update(string $DB_TABLE, array $listValuesPers = [], $id = ''): ?array
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
    public static function parseForm(string $DB_TABLE, $id = '', array $uniques = [], array $notNull = []): array
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

            // Value exist? ---
            $valueExist = \DB::table($DB_TABLE)->where([
                [$fieldName, '=', $postValue],
                ['id', '<>', $id],
            ])->exists();

            if ($valueExist) {
                $title = ($fieldProp->title) ? $fieldProp->title : $fieldName;
                $listErrors[$fieldName] = $title . ' = ' . $postValue . ' ' . Local::$t['GenQuery_error_unique'];
            }
        }

        /** Out errors **/
        WForm::update_setErrors($listErrors);
        return $listErrors;
    }
    //------------------------------------------------------------------
    // SELECT
    //------------------------------------------------------------------
    public static function selectFiltros(string $DB_TABLE, $sqlFiltros): string
    {
        $strFiltros = '';
        if ($sqlFiltros) {
            $strFiltros = " WHERE $sqlFiltros";
        }

        $sqlQ = self::select($DB_TABLE);
        return $sqlQ . $strFiltros;
    }
    //------------------------------------------------------------------
    public static function select(string $DB_TABLE): string
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
    // INSERT / UPDATE
    //------------------------------------------------------------------
    public static function insert($DB_TABLE, array $listValuesPers = [])
    {
        // Values --------
        $valuesToInsert = self::getValuesToInsert($DB_TABLE, $listValuesPers);

        // Errors ---
        if (isset($valuesToInsert->errors)) {
            WForm::update_setErrors($valuesToInsert->errors);
            return $valuesToInsert->errors;
        }

        // Insert row ---
        $id = \DB::table($DB_TABLE)->insertGetId($valuesToInsert);

        // Update "Event::ROW_ID" ---
        Event::setRowId($id);
    }
    //------------------------------------------------------------------
    public static function update(string $DB_TABLE, array $listValuesPers = [], $id = '')
    {
        if (!$id) {
            $id = Event::$ROW_ID;
        }

        // Values ------
        $valuesToInsert = self::getValuesToInsert($DB_TABLE, $listValuesPers);

        // Errors ----
        if (isset($valuesToInsert->errors) && $valuesToInsert->errors) {
            WForm::update_setErrors($valuesToInsert->errors);
            return $valuesToInsert->errors;
        }

        // Update row ---
        if ($valuesToInsert) {
            \DB::table($DB_TABLE)->where('id', $id)->update($valuesToInsert);

            // Update "Event::ROW_ID" ---
            Event::setRowId($id);
        }
    }
    //------------------------------------------------------------------
    public static function getValuesToInsert(string $DB_TABLE, array $listValuesPers = [])
    {
        $values = [];

        $listFields = self::getTableProperties($DB_TABLE);

        foreach ($listFields as $fieldName => $fieldProp)
        {
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
                if ($value === false) {
                    continue;
                }
            }

            // Query
            $values[$fieldName] = $value;
        }

        return $values;
    }
    //------------------------------------------------------------------
    // DELETE
    //------------------------------------------------------------------
    public static function softDelete(string $DB_TABLE, $id = ''): int
    {
        // Auto get ID ---
        $ROW_ID = ($id)? $id : Event::$ROW_ID;

        // Query ---
        \DB::table($DB_TABLE)
            ->where('id', '=', $ROW_ID)
            ->update(['deleted_at' => \Carbon::now()]);

        return $ROW_ID;
    }
    //-----------
    /* Delete row and uploaded files */
    public static function delete(string $DB_TABLE, $id = ''): int
    {
        // Auto get ID ---
        $ROW_ID = ($id)? $id : Event::$ROW_ID;

        // Query ---
        self::deleteById($DB_TABLE, $ROW_ID);

        // Delete in session ---
        Event::delRowId();

        return $ROW_ID;
    }
    //-----------
    public static function deleteById(string $DB_TABLE, $id)
    {
        // Delete uploads -------
        global $seccCtrl;
        $listFields = self::getTableProperties($DB_TABLE);

        foreach ($listFields as $fieldName => $fieldProp) {
            if ($fieldProp->type != 'file') {
                continue;
            }

            $bbdd_file = \DB::table($DB_TABLE)->where('id', $id)->value($fieldName);
            if (!$bbdd_file) {
                continue;
            }

            $paramsFile = FileUploaded::getInfo($bbdd_file, $seccCtrl->UPLOADS_DIR);
            if ($paramsFile['name']) {
                unlink($paramsFile['path_completo']); // archivo
                @unlink($paramsFile['path_completo_th']); // if thumbnail
            }
        }

        // Delete row ----
        \DB::table($DB_TABLE)->where('id', '=', $id)->delete();
    }
    //------------------------------------------------------------------
    // PRIVATE
    //------------------------------------------------------------------
    private static function getTableProperties(string $table): ?array
    {
        $listFields = \DB::select("SHOW FULL COLUMNS FROM $table");
        if (!$listFields) {
            user_error("DBProperties(): la tabla [$table] no existe", E_USER_WARNING);
            return null;
        }

        $tableProp = [];
        foreach ($listFields as $field) {
            $nombreCampo = $field->Field;
            if ($nombreCampo == 'id') {
                continue;
            }

            $tableProp[$nombreCampo] = new \stdClass();
            $tableProp[$nombreCampo]->title = '';

            // Propiedades a través de MySql
            $tableProp[$nombreCampo]->type = ($field->Type == 'timestamp' || $field->Type == 'datetime') ?
            $field->Type :
            trim(substr($field->Type, 0, 7));

            $tableProp[$nombreCampo]->obligatorio = ($field->Null == 'NO') ? 'true' : '';
            $tableProp[$nombreCampo]->unique      = ($field->Key == 'UNI') ? 'true' : '';

            // Propiedades a través del comentario
            if ($field->Comment) {
                $field->Comment = str_replace(";", "&", $field->Comment);
                parse_str($field->Comment, $output);

                if (isset($output['title'])) {
                    $tableProp[$nombreCampo]->title = $output['title'];
                }
                if (isset($output['type'])) {
                    $tableProp[$nombreCampo]->type = $output['type'];
                }
            }
        }

        return $tableProp;
    }
    //------------------------------------------------------------------
    private static function getValueFromRequest($DB_TABLE, $fieldName, $fieldType)
    {
        $inputValue = '';

        // POST ---
        if (isset($_POST[$fieldName])) {
            $inputValue = $_POST[$fieldName];
        } elseif (isset($_FILES[$fieldName])) {
        } else {
            return false;
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
    private static function parseValue($inputValue, $fieldName, $fieldType): ?string
    {
        $inputValue = trim($inputValue);

        // NULL ---
        if ($inputValue == 'NULL') {
            return null;
        }

        // Password ---
        global $CONFIG_APP;
        if ($fieldName == 'password' && $CONFIG_APP['login']['LOGIN_HASH']) {
            return password_hash($inputValue, PASSWORD_BCRYPT);
        }

        // Date ---
        if ($fieldType == 'date') {
            $value = "STR_TO_DATE('$inputValue', '%d/%m/%Y')";
        }

        return $inputValue;
    }
    //------------------------------------------------------------------
}
