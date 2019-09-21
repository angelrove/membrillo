<?php
/**
 * ModelInterface
 */

namespace angelrove\membrillo\Database;

interface ModelInterface
{
    public static function read(array $filter_conditions=array(), array $filter_data=array());

    public static function findById($id, $asArray=true, $setHtmlSpecialChars = true);

    public static function getValueById($id, $field);

    public static function find(array $filter_conditions);

    public static function findEmpty();

    public static function create(array $listValues=array(), $messageAuto = true);

    public static function update(array $listValues=array(), $id='');

    public static function delete();
}
