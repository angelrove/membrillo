<?php
namespace angelrove\membrillo\Database;

use angelrove\membrillo\Database\ModelInterface;
use angelrove\membrillo\Database\GenQuery;
use angelrove\utils\Db_mysql;

class Model implements ModelInterface
{
    public static function read(array $filters=array(), $strict=false)
    {
        if (static::CONF['soft_delete'] && !isset($filters['deleted_at'])) {
            $filters['deleted_at'] = 'NULL';
        }

        $sqlFilters = self::getSqlFilters($filters, $strict);

        return GenQuery::select(static::CONF['table']).$sqlFilters;
    }

    public static function findById($id, $asArray=true, $setHtmlSpecialChars = true)
    {
        $sql = GenQuery::selectRow(static::CONF['table'], $id);

        if ($asArray) {
            return Db_mysql::getRow($sql, $setHtmlSpecialChars);
        } else{
            return Db_mysql::getRowObject($sql, $setHtmlSpecialChars);
        }
    }

    public static function getValueById($id, $field)
    {
        $sql = GenQuery::selectRow(static::CONF['table'], $id);
        $data = Db_mysql::getRow($sql);

        return $data[$field];
    }

    public static function find(array $filters)
    {
        $sqlFilters = self::getSqlFilters($filters, $strict);

        $sql = "SELECT * FROM " . static::CONF['table'] . $sqlFilters." LIMIT 1";

        return Db_mysql::getRow($sql);
    }

    public static function findEmpty()
    {
        $columns = Db_mysql::getListOneField("SHOW COLUMNS FROM " . static::CONF['table']);
        foreach ($columns as $key => $value) {
            $datos[$key] = '';
        }

        return $datos;
    }

    public static function create(array $listValues=array())
    {
        return GenQuery::helper_insert(static::CONF['table'], $listValues);
    }

    public static function update(array $listValues=array(), $id='')
    {
        return GenQuery::helper_update(static::CONF['table'], $listValues, $id);
    }

    public static function delete()
    {
        if (static::CONF['soft_delete']) {
            return GenQuery::softDelete(static::CONF['table']);
        } else {
            return GenQuery::delete(static::CONF['table']);
        }
    }
    //------------------------------------------------------------------------
    public static function getSqlFilters(array $filters, $strict=false)
    {
        $listWhere = array();
        foreach ($filters as $column => $filtro) {
            if ($filtro == 'NULL' || $filtro == 'NOT NULL') {
                $listWhere[] = " $column IS $filtro";
            }
            elseif ($strict == true) {
                $listWhere[] = " $column = '$filtro'";
            } else {
                $listWhere[] = " $column LIKE '%$filtro%'";
            }
        }
        $sqlFilters = \angelrove\utils\UtilsBasic::implode(' AND ', $listWhere);

        if ($sqlFilters) {
            $sqlFilters = ' WHERE '.$sqlFilters;
        }

        return $sqlFilters;
    }
    //------------------------------------------------------------------------
}
