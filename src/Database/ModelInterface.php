<?php
/**
 * ModelInterface
 */

namespace angelrove\membrillo\Database;

interface ModelInterface
{
    public static function read(array $filtros=array());

    public static function findById($id, $asArray=true, $setHtmlSpecialChars = true);

    public static function getValueById($id, $field);

    public static function find(array $filters);

    public static function findEmpty();

    public static function create(array $listValues=array());

    public static function update(array $listValues=array(), $id='');

    public static function delete();
}
