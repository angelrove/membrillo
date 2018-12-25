<?php
/**
 * DEPRECATED!
 */

namespace angelrove\membrillo\Database;

use angelrove\membrillo\Database\GenQuery;
use angelrove\membrillo\Database\ModelHelper;
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

    public static function find(array $filters, $strict=true)
    {
        return ModelHelper::find(self::$TABLE, $filters, $strict);
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
