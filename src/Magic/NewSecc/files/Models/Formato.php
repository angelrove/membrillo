<?php
namespace App\Models;

use angelrove\membrillo2\Database\ModelInterface;
use angelrove\membrillo2\Database\ModelHelper;

class Formato implements ModelInterface
{
    public static $TABLE = 'formatos';

    public static function rows()
    {
        return ModelHelper::rows(self::$TABLE)." ORDER BY name";
    }

    public static function read(array $filtros=array(), $strict=false)
    {
        return ModelHelper::read(self::$TABLE, $filtros, $strict);
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

    //--------------------------------------------------------
    public static function create()
    {
        return ModelHelper::create(self::$TABLE);
    }

    public static function update(array $listValues=array(), $id='')
    {
        return ModelHelper::update(self::$TABLE, $listValues, $id);
    }

    public static function delete()
    {
        // ModelHelper::delete(self::$TABLE);
    }
    //--------------------------------------------------------
}
