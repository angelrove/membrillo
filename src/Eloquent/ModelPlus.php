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
    /*
     * $hash = password_hash($inputValue, PASSWORD_BCRYPT);
     */
    public static function loginHash($email, $passwd, string $conditions = ''): ?array
    {
        $DB_TABLE = self::getTableName();

        $query = DB::table($DB_TABLE)->where([
                'email' => $email,
            ]);

        if (isset(self::$conf_softdelete) && self::$conf_softdelete) {
            $query->whereNull('deleted_at');
        }

        if ($conditions) {
            $query->whereRaw($conditions);
        }

        $data = $query->first();

        // Password hash verify ---
        if ($data) {
            if (password_verify($passwd, $data->password)) {
                return (array)$data;
            }
        }

        return null;
    }
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

        // Get Form values ---
        $formValues = GenQuery::getFormValues($DB_TABLE, $listValues);

        // WForm show errors ---
        if (isset($formValues['errors'])) {
            WForm::setValueError($formValues['errors']);
            return false;
        }

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

        // Get Form values ---
        $formValues = GenQuery::getFormValues($DB_TABLE, $listValues, $id);

        // WForm show errors ---
        if (isset($formValues['errors'])) {
            WForm::setValueError($formValues['errors'], $id);
            return false;
        }

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
    private static function getTableColumns(): array
    {
        $tableName = self::getTableName();
        return DB::schema()->getColumnListing($tableName);
    }
    //--------------------------------------------------
    private static function getTableName(): string
    {
        return (new static)->getTable();
    }
    //--------------------------------------------------
}
