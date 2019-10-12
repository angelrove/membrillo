<?php

namespace angelrove\membrillo\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Capsule\Manager as DB;
use angelrove\membrillo\Database\GenQuery;
use angelrove\membrillo\WObjects\WForm\WForm;

class ModelPlus extends Model
{
    //--------------------------------------------------
    public static function emptyRow(): array
    {
        $emptyRow = [];

        $columns = self::getTableColumns();
        foreach ($columns as $column) {
            $emptyRow[$column] = '';
        }

        return $emptyRow;
    }
    //--------------------------------------------------
    public static function createForm(array $listValues = [])
    {
        $DB_TABLE = self::getTableName();

        // Get form values --------
        $errors = GenQuery::parseFormValues($DB_TABLE);
        if ($errors) {
            WForm::update_setErrors($errors, $id);
            return $errors;
        }

        $formValues = GenQuery::getFormValues($DB_TABLE, $listValues);

        // Create row ---
        $row = self::create($formValues);

        // Update "Event::$row_id" ---
        \Event::setRowId($row->id);

        // Message ok ---
        \Messages::set(\Local::$t['Saved']);
    }
    //--------------------------------------------------
    public function updateForm(array $listValues = [])
    {
        $DB_TABLE = $this->table;
        $id       = $this->id;

        // Values --------
        $errors = GenQuery::parseFormValues($DB_TABLE, $id);
        if ($errors) {
            WForm::update_setErrors($errors, $id);
            return $errors;
        }

        $formValues = GenQuery::getFormValues($DB_TABLE, $listValues);

        // Update row ---
        $this->update($formValues);

        // Update "Event::ROW_ID" ---
        \Event::setRowId($id);

        // Message ok ---
        \Messages::set(\Local::$t['Saved']);
    }
    //--------------------------------------------------
    // PRIVATE
    //--------------------------------------------------
    private static function getTableColumns()
    {
        $tableName = self::getTableName();
        return DB::schema()->getColumnListing($tableName);
    }

    private static function getTableName()
    {
        return (new static)->getTable();
    }
    //--------------------------------------------------
}
