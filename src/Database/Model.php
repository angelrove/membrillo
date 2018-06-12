<?php
/**
 * DEPRECATED!
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

    public static function findById($id, $asArray=true, $setHtmlSpecialChars = true)
    {
        $sql = GenQuery::selectRow(self::$TABLE, $id);

        if ($asArray) {
            return Db_mysql::getRow($sql, $setHtmlSpecialChars);
        } else{
            return Db_mysql::getRowObject($sql, $setHtmlSpecialChars);
        }
    }

    public static function getValueById($id, $field)
    {
        $sql = GenQuery::selectRow(self::$TABLE, $id);
        $data = Db_mysql::getRow($sql);

        return $data[$field];
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

    public static function create(array $listValues=array())
    {
        GenQuery::helper_insert(self::$TABLE, $listValues);
    }

    public static function update(array $listValues=array())
    {
        GenQuery::helper_update(self::$TABLE, $listValues);
    }

    public static function delete()
    {
        return GenQuery::delete(self::$TABLE);
    }
    //--------
    public static function rows()
    {
        return self::read();
    }
    //--------------------------------------------
}
