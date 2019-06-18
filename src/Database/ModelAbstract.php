<?php
namespace angelrove\membrillo\Database;

use angelrove\membrillo\Database\ModelInterface;
use angelrove\membrillo\Database\ModelHelper;

class ModelAbstract implements ModelInterface
{
    public static function read(array $filtros=array(), $strict=false)
    {
        return ModelHelper::read(static::CONF, $filtros, $strict);
    }

    public static function findById($id, $asArray=true, $setHtmlSpecialChars = true)
    {
        return ModelHelper::findById(static::CONF, $id, $asArray, $setHtmlSpecialChars);
    }

    public static function getValueById($id, $field)
    {
        return ModelHelper::getValueById(static::CONF, $id, $field);
    }

    public static function find(array $filters)
    {
        return ModelHelper::find(static::CONF, $filters);
    }

    public static function findEmpty()
    {
        return ModelHelper::findEmpty(static::CONF);
    }

    public static function create(array $listValues=array())
    {
        return ModelHelper::create(static::CONF);
    }

    public static function update(array $listValues=array(), $id='')
    {
        return ModelHelper::update(static::CONF, $listValues, $id);
    }

    public static function delete()
    {
        return ModelHelper::delete(static::CONF);
    }
}
