<?php
namespace angelrove\membrillo\ApiRestCrud;

use angelrove\membrillo\ApiRestCrud\ApiRestCrudInterface;
use angelrove\membrillo\ApiRestCrud\ApiRestCrudHelper;

abstract class ApiRestCrud implements ApiRestCrudInterface
{
    abstract protected static function read_defaultParams();
    abstract protected static function update_parseData(array $data);

    public static function read($asJson=false, array $params=array())
    {
        $params_def = static::read_defaultParams();
        $params = array_merge($params, $params_def);
        return ApiRestCrudHelper::read(static::CONF, $asJson, $params);
    }

    public static function readById($id) {
        $data = ApiRestCrudHelper::readById(static::CONF, $id);
        return (array) $data['body'];
    }

    public static function readEmpty() {
        return ApiRestCrudHelper::readEmpty(static::$columns);
    }

    public static function create($data) {
        $data = static::update_parseData($data);
        return ApiRestCrudHelper::create(static::CONF, static::$columns, $data);
    }

    public static function update($id, $data) {
        $data = static::update_parseData($data);
        return ApiRestCrudHelper::update(static::CONF, static::$columns, $id, $data);
    }

    public static function delete($id) {
        return ApiRestCrudHelper::delete(static::CONF, $id);
    }
}
