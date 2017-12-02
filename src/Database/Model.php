<?php
/**
 *
 */

namespace angelrove\membrillo2\Database;

use angelrove\membrillo2\Database\GenQuery;
use angelrove\utils\Db_mysql;

class Model
{
    public static $TABLE = '';

    public static function init($TABLE) {
        self::$TABLE = $TABLE;
    }

    //--------------------------------------------
    // CRUD
    //--------------------------------------------
    public static function read()
    {
        return GenQuery::select(self::$TABLE);
    }

    public static function find($id)
    {
        $sql = GenQuery::selectRow(self::$TABLE, $id);
        return Db_mysql::getRow($sql);
    }

    public static function findEmpty()
    {
        $columns = Db_mysql::getListOneField("SHOW COLUMNS FROM " . self::$TABLE);
        foreach ($columns as $key => $value) {
            $datos[$key] = '';
        }

        return $datos;
    }

    public static function create()
    {
        GenQuery::helper_insert(self::$TABLE);
    }

    public static function update()
    {
        GenQuery::helper_update(self::$TABLE);
    }

    public static function delete()
    {
        GenQuery::delete(self::$TABLE);
    }
    //--------
    public static function rows()
    {
        return self::read();
    }
    //--------------------------------------------
}
