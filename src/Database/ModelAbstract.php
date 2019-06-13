<?php
namespace App\Models;

use angelrove\membrillo\Database\ModelInterface;
use angelrove\membrillo\Database\ModelHelper;

class ModelAbstract implements ModelInterface
{
    public static function rows()
    {
        return ModelHelper::rows(self::$TABLE);
    }

    public static function read(array $filtros=array(), $strict=false)
    {
        return ModelHelper::read(self::$TABLE, $filtros, $strict, $SOFT_DELETE);
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
    public static function create(array $listValues=array())
    {
        return ModelHelper::create(self::$TABLE, $listValues);
    }

    public static function update(array $listValues=array(), $id='')
    {
        return ModelHelper::update(self::$TABLE, $listValues, $id);
    }

    public static function delete()
    {
        return ModelHelper::softDelete(self::$TABLE);
    }
    //--------------------------------------------------------
}
