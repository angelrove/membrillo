<?php
namespace angelrove\membrillo\Database;

use angelrove\membrillo\Database\ModelInterface;
use angelrove\membrillo\Database\GenQuery;
use angelrove\utils\Db_mysql;

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
    public static function read(array $filter_conditions = array(), array $filter_data = array())
    {
        if (static::CONF['soft_delete'] && !$filter_conditions) {
            $filter_conditions[] = 'deleted_at IS NULL';
        }
        $sqlFilters = GenQuery::getSqlFilters($filter_conditions, $filter_data);
        // print_r2($sqlFilters);

        $sqlQ = GenQuery::select(static::CONF['table']).$sqlFilters;
        // print_r2($sqlQ);

        return $sqlQ;
    }

    /**
     * Return one row by id
     */
    public static function findById($id, $asArray = true, $setHtmlSpecialChars = true)
    {
        $sql = GenQuery::selectRow(static::CONF['table'], $id);

        if ($asArray) {
            return Db_mysql::getRow($sql, $setHtmlSpecialChars);
        } else {
            return Db_mysql::getRowObject($sql, $setHtmlSpecialChars);
        }
    }

    public static function getValueById($id, $field)
    {
        $sql = GenQuery::selectRow(static::CONF['table'], $id);
        $data = Db_mysql::getRow($sql);

        return $data[$field];
    }

    public static function find(array $filter_conditions)
    {
        $sqlFilters = GenQuery::getSqlFilters($filter_conditions);

        $sql = "SELECT * FROM " . static::CONF['table'] . $sqlFilters." LIMIT 1";

        return Db_mysql::getRow($sql);
    }

    public static function findEmpty()
    {
        $columns = Db_mysql::getListOneField("SHOW COLUMNS FROM " . static::CONF['table']);
        foreach ($columns as $key => $value) {
            $datos[$key] = '';
        }

        return $datos;
    }

    public static function create(array $listValues = array(), $messageAuto = true)
    {
        return GenQuery::helper_insert(static::CONF['table'], $listValues, $messageAuto);
    }

    public static function update(array $listValues = array(), $id = '')
    {
        return GenQuery::helper_update(static::CONF['table'], $listValues, $id);
    }

    public static function delete($id='')
    {
        $ROW_ID = ($id)? $id : Event::$ROW_ID;

        if (static::CONF['soft_delete']) {
            return GenQuery::softDelete(static::CONF['table'], $ROW_ID);
        } else {
            return GenQuery::delete(static::CONF['table'], $ROW_ID);
        }
    }
    //-----------------------------------------------------------------
    // Login
    //-----------------------------------------------------------------
    // $hash = password_hash($inputValue, PASSWORD_BCRYPT);
    public static function login($email, $passwd, array $conditions = array()) : ?array
    {
        $conditions[] = "email='$email'";
        $conditions[] = "password='$passwd'";
        $conditions[] = "deleted_at IS NULL";

        if ($data = Db_mysql::getRow(self::read($conditions))) {
            return $data;
        }

        return null;
    }

    public static function login_hash($email, $passwd, array $conditions = array()) : ?array
    {
        $conditions[] = "email='$email'";
        $conditions[] = "deleted_at IS NULL";

        // Password hash verify ---
        if ($data = Db_mysql::getRow(self::read($conditions))) {
            if (password_verify($passwd, $data['password'])) {
                return $data;
            }
        }

        return null;
    }
    //-----------------------------------------------------------------
}
