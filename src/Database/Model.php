<?php
namespace angelrove\membrillo\Database;

use angelrove\membrillo\Database\ModelInterface;
use angelrove\membrillo\Database\GenQuery;
use angelrove\membrillo\Messages;
use angelrove\membrillo\WApp\Local;
use angelrove\membrillo\WObjectsStatus\Event;
use angelrove\membrillo\WObjects\WForm\WForm;
use angelrove\utils\Db_mysql;
use Illuminate\Database\Capsule\Manager as DB;

class Model implements ModelInterface
{
    /*
     * Sample $conditions param:
        $conditions[] = "id <> 1";

        // Search ---
        $conditions['f_text'] = "name LIKE '%[VALUE]%'";
        $conditions['f_status'] = [
            'default' => "deleted_at IS NULL",
            'deleted' => "deleted_at IS NOT NULL",
        ];
     */
    public static function read(array $filter_conditions = [], array $filter_data = []): string
    {
        $DB_TABLE = static::CONF['table'];

        // Sql where ---
        if (static::CONF['soft_delete'] && !$filter_conditions) {
            $filter_conditions[] = 'deleted_at IS NULL';
        }
        $sqlFilters = GenQuery::getSqlFilters($filter_conditions, $filter_data);

        // Values format ---
        $strColumnsFormat = '';

        $listFields = GenQuery::getTableProperties($DB_TABLE);
        foreach ($listFields as $fieldName => $fieldProp) {
            switch ($fieldProp->type) {
                case 'date':
                    $strColumnsFormat .= ",\n DATE_FORMAT($fieldName, '%d/%m/%Y') AS " . $fieldName . "_format";
                    break;

                case 'timestamp':
                case 'datetime':
                    $strColumnsFormat .= ",\n DATE_FORMAT($fieldName, '%d/%m/%Y %H:%i') AS " . $fieldName . "_format";
                    $strColumnsFormat .= ",\n UNIX_TIMESTAMP($fieldName) AS " . $fieldName . "_unix";
                    break;

                case 'file':
                    $strColumnsFormat .= ",\n SUBSTRING_INDEX($fieldName, '#', 1) AS " . $fieldName . "_format";
                    break;
            }
        }

        // Query ---
        return "SELECT * $strColumnsFormat \nFROM $DB_TABLE".$sqlFilters;
    }

    public static function find(array $filter_conditions): ?array
    {
        $listWhere = GenQuery::getSqlWhere($filter_conditions);

        $query = DB::table(static::CONF['table']);
        if ($listWhere) {
            $query->whereRaw($listWhere);
        }

        return (array)$query->first();
    }

    /**
     * Return one row by id
     */
    public static function findById($id, $asArray = true, $setHtmlSpecialChars = true)
    {
        $row = DB::table(static::CONF['table'])->find($id);

        if ($asArray) {
            $row = (array)$row;
            if ($setHtmlSpecialChars) {
                return self::setHtmlSpecialChars($row);
            }
        }

        return $row;
    }

    public static function getValueById($id, $field)
    {
        return DB::table(static::CONF['table'])->where('id', $id)->value($field);
    }

    public static function findEmpty(): array
    {
        $emptyRow = [];

        $columns = DB::schema()->getColumnListing(static::CONF['table']);
        foreach ($columns as $column) {
            $emptyRow[$column] = '';
        }

        return $emptyRow;
    }

    public static function create(array $listValues = [], $messageAuto = true)
    {
        $DB_TABLE = static::CONF['table'];

        // Values --------
        $errors = GenQuery::parseFormValues($DB_TABLE);
        if ($errors) {
            WForm::update_setErrors($errors);
            return $errors;
        }

        $valuesToInsert = GenQuery::getFormValues($DB_TABLE, $listValues);

        // Insert row ---
        $id = \DB::table($DB_TABLE)->insertGetId($valuesToInsert);

        // Update "Event::ROW_ID" ---
        Event::setRowId($id);

        // Output message ---
        if ($messageAuto) {
            Messages::set(Local::$t['Saved']);
        }

        return null;
    }

    public static function update(array $listValues = [], $id = '')
    {
        $DB_TABLE = static::CONF['table'];

        if (!$id) {
            $id = Event::$ROW_ID;
        }

        // Values --------
        $errors = GenQuery::parseFormValues($DB_TABLE, $id);
        if ($errors) {
            WForm::update_setErrors($errors, $id);
            return $errors;
        }

        $valuesToUpdate = GenQuery::getFormValues($DB_TABLE, $listValues);

        // Update row ---
        if ($valuesToUpdate) {
            \DB::table($DB_TABLE)->where('id', $id)->update($valuesToUpdate);
        }

        // Update "Event::ROW_ID" ---
        Event::setRowId($id);

        return null;
    }

    public static function delete($id = '')
    {
        $DB_TABLE = static::CONF['table'];
        $ROW_ID = ($id)? $id : Event::$ROW_ID;

        if (static::CONF['soft_delete']) {
            // Delete row ---
            \DB::table($DB_TABLE)
                ->where('id', '=', $ROW_ID)
                ->update(['deleted_at' => \Carbon::now()]);
        } else {
            // Delete files ---
            GenQuery::deleteUploadsById($DB_TABLE, $ROW_ID);

            // Delete row ---
            \DB::table($DB_TABLE)->where('id', '=', $ROW_ID)->delete();
        }

        // Remove id from session ---
        Event::delRowId();

        return $ROW_ID;
    }
    //-----------------------------------------------------------------
    // Login
    //-----------------------------------------------------------------
    public static function login($email, $passwd, string $conditions = ''): ?array
    {
        $query = DB::table(static::CONF['table'])->where([
                'email' => $email,
                'password' => $password,
            ])->whereNull('deleted_at');

        if ($conditions) {
            $query->whereRaw($conditions);
        }

        return (array)$query->first();
    }

    // $hash = password_hash($inputValue, PASSWORD_BCRYPT);
    public static function loginHash($email, $passwd, string $conditions = ''): ?array
    {
        $query = DB::table(static::CONF['table'])->where([
                'email' => $email,
            ])->whereNull('deleted_at');

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
    //------------------------------------------------------------
    // Por si se va a mostrar en un input y hay algo de esto: &, ", ', <, >
    public static function setHtmlSpecialChars(array $data): ?array
    {
        return array_map('htmlspecialchars', $data);
    }
    //-----------------------------------------------------------------
}
