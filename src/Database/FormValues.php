<?php
/**
 * SearchFilters
 *
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 */

namespace angelrove\membrillo\Database;

use angelrove\membrillo\WInputs\WInputFile\WInputFile_upload;
use angelrove\membrillo\WApp\Local;

trait FormValues
{
    //------------------------------------------------------------------
    public static function getFormValues(string $DB_TABLE, array $listValuesPers = [], $id = false)
    {
        $listFields = self::getTableProperties($DB_TABLE);

        // Check Values -----------
        $errors = [
            'errors' => []
        ];

        foreach ($listFields as $fieldName => $fieldProp) {
            // Required ---
            if ($error = self::checkRequired($fieldName, $fieldProp)) {
                $errors['errors'] = [$fieldName => $error];
                return $errors;
            }

            // Unique ---
            $value = ($_POST[$fieldName])?? '';
            $error = self::checkUnique($fieldName, $fieldProp, $DB_TABLE, $value, $id);
            if ($error) {
                $errors['errors'] = [$fieldName => $error];
                return $errors;
            }
        }

        // Get Values -----------
        $values = [];

        foreach ($listFields as $fieldName => $fieldProp) {
            // Value ---
            $value = '';
            if (isset($listValuesPers[$fieldName])) {
                $value = $listValuesPers[$fieldName];
                $value = self::parseValue($value, $fieldName, $fieldProp->type);
            } else {
                $value = self::getValueFromRequest($DB_TABLE, $fieldName, $fieldProp->type);
                if ($value === false) {
                    continue;
                }
            }

            // Query ---
            $values[$fieldName] = $value;
        }

        return $values;
    }
    //------------------------------------------------------------------
    // Get values
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

        return self::parseValueToBd($inputValue, $fieldName, $fieldType);
    }
    //------------------------------------------------------------------
    // Convert value to DB format
    private static function parseValueToBd($inputValue, $fieldName, $fieldType): ?string
    {
        // Trim ---
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
    // Check the values according to the type
    //------------------------------------------------------------------
    private static function checkRequired($fieldName, $fieldProp)
    {
        if (!$fieldProp->obligatorio) {
            return;
        }
        if (!isset($_POST[$fieldName]) && !isset($_FILES[$fieldName]['name'])) {
            return;
        }

        // Parse ------
        $ret = [];
        $value = '';

        if ($fieldProp->type == 'file') {
            if ($_POST[$fieldName . '_isDelete'] == 0) {
                $value = $_POST[$fieldName . '_prev'];
            }
            $value = $value . $_FILES[$fieldName]['name'];
        } else {
            $value = $_POST[$fieldName];
        }

        // Error ---
        if ($value == '' || $value == '00/00/0000') {
            $title = ($fieldProp->title) ? $fieldProp->title : $fieldName;
            return $title . ': ' . Local::$t['GenQuery_error_obliga'];
        }
    }
    //------------------------------------------------------------------
    private static function checkUnique($fieldName, $fieldProp, $table, $value, $id = '')
    {
        if (!$fieldProp->unique) {
            return;
        }
        if (!$fieldProp->obligatorio && !$value) {
            return;
        }

        // Value exist? ---
        if ($id) { // on update
            $conditionCurrent = ['id', '<>', $id];
        }

        $exist = \DB::table($table)->where([
            [$fieldName, '=', $value],
            $conditionCurrent
        ])->exists();

        if ($exist) {
            $title = ($fieldProp->title) ? $fieldProp->title : $fieldName;

            return $title . ': ' . Local::$t['GenQuery_error_unique'];
        }
    }
    //------------------------------------------------------------------
}
