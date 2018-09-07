<?php
/**
 * DEPRECATED!
 */

namespace angelrove\membrillo2\Database;

use angelrove\membrillo2\Database\GenQuery;
use angelrove\membrillo2\Database\ModelHelper;
use angelrove\utils\Db_mysql;

class Model
{
    public static $TABLE = '';

    //--------------------------------------------
    // CRUD
    //--------------------------------------------
    public static function read(array $filters=array(), $strict=false)
    {
        return ModelHelper::read(self::$TABLE, $filters, $strict);
    }

    public static function findById($id, $asArray=true, $setHtmlSpecialChars = true)
    {
        return ModelHelper::findById(self::$TABLE, $id, $asArray, $setHtmlSpecialChars);
    }

    public static function getValueById($id, $field)
    {
        return ModelHelper::getValueById(self::$TABLE, $id, $field);
    }

    public static function find(array $filters)
    {
        return ModelHelper::find(self::$TABLE, $filters);
    }

    public static function findEmpty()
    {
        return ModelHelper::findEmpty(self::$TABLE);
    }
    //--------------------------------------------
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
