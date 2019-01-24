<?php
/**
 * ModelInterface
 */

namespace angelrove\membrillo\ApiRestCrud;

interface ApiRestCrudInterface
{
    public static function create($data);

    public static function update($id, $data);

    public static function delete($id);

    public static function read($asJson=false, array $params=array());

    public static function readById($id);
}
