<?php
/**
 *
 */

namespace angelrove\membrillo2\Database;

interface ModelInterface
{
    public static function rows();

    public static function read(array $filtros=array());

    public static function findById($id, $asArray=true, $setHtmlSpecialChars = true);

    public static function getValueById($id, $field);

    public static function find(array $filters);

    public static function findEmpty();

    public static function create();

    public static function update(array $listValues=array());

    public static function delete();

}
