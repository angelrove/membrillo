<?php
/**
 * SearchFilters
 *
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 */

namespace angelrove\membrillo\Database;

use angelrove\membrillo\WInputs\WInputFile\WInputFile_upload;
use angelrove\membrillo\WApp\Local;
use angelrove\membrillo\WObjects\WForm\WForm;

trait FormValues
{
    //------------------------------------------------------------------
    public static function getFormValuesX(string $DB_TABLE, $listValues, $id = false): ?array
    {
        $errors = GenQuery::parseFormValues($DB_TABLE, $id);
        if ($errors) {
            WForm::update_setErrors($errors, $id);
            return null;
        }

        $formValues = GenQuery::getFormValues($DB_TABLE, $listValues);

        return $formValues;
    }
    //------------------------------------------------------------------
    private static function parseFormValues(string $DB_TABLE, $id = false): array
    {
        $listErrors = [];

        $listFields = self::getTableProperties($DB_TABLE);

        // Obligatorios ---
        foreach ($listFields as $fieldName => $fieldProp)
        {
            $ret = self::parseRequired();
            if ($ret['error']) {
                $listErrors[$fieldName] = $ret['error'];
            }
            // $listValues[$fieldName] = $ret['value']
        }

        // Unique ---------
        foreach ($listFields as $fieldName => $fieldProp)
        {
            $ret = self::parseUnique($id);
            if ($ret['error']) {
                $listErrors[$fieldName] = $ret['error'];
            }
            // $listValues[$fieldName] = $ret['value']
        }

        /** Out errors **/
        return $listErrors;
    }
    //------------------------------------------------------------------
    private static function getFormValues(string $DB_TABLE, array $listValuesPers = [])
    {
        $values = [];

        $listFields = self::getTableProperties($DB_TABLE);

        // Get Values -----------
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
    // PRIVATE
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

function parseRequired($fieldName, $fieldProp)
{
    if (!$fieldProp->obligatorio) {
        return false;
    }
    if (!isset($_POST[$fieldName]) && !isset($_FILES[$fieldName]['name'])) {
        return false;
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
        return [
            'error' => $title . ': ' . Local::$t['GenQuery_error_obliga'],
            'value' => $value,
        ];
    } else {
        return [
            'value' => $value,
        ];
    }
}
function parseUnique($fieldName, $fieldProp, $id = false)
{
    if (!$fieldProp->unique) {
        return false;
    }

    $value = ($_POST[$fieldName])?? '';

    if (!$fieldProp->obligatorio && !$value) {
        return false;
    }

    // Value exist? ---
    if ($id) {
        $conditionCurrent = ['id', '<>', $id];
    }

    $exist = \DB::table($DB_TABLE)->where([
        [$fieldName, '=', $value],
        $conditionCurrent
    ])->exists();

    if ($exist) {
        $title = ($fieldProp->title) ? $fieldProp->title : $fieldName;

        return [
            'error' => $title . ': ' . Local::$t['GenQuery_error_obliga'],
            'value' => $value,
        ];
    }
}
}
