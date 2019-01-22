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

    public static function read();

    public static function readById($id);
}
