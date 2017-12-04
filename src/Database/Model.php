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

    //--------------------------------------------
    // CRUD
    //--------------------------------------------
    public static function read()
    {
        return GenQuery::select(self::$TABLE);
    }

    public static function findById($id)
    {
        $sql = GenQuery::selectRow(self::$TABLE, $id);
        return Db_mysql::getRow($sql);
    }

    public static function find(array $filters)
    {
        //---
        $listWhere = array();
        foreach ($filters as $key => $value) {
            $listWhere[] = " $key = '$value'";
        }

        //---
        $strWhere = \angelrove\utils\UtilsBasic::array_implode(' AND ', $listWhere);

        //---
        $sql = "SELECT * FROM ".self::$TABLE.' WHERE '.$strWhere." LIMIT 1";

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
