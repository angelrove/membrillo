<?php
/**
 * ModelHelper
 */

namespace angelrove\membrillo\Database;

use angelrove\membrillo\Database\GenQuery;
use angelrove\utils\Db_mysql;

class ModelHelper
{
    public static function read($CONF, array $filters=array(), $strict=false)
    {
        if ($CONF['soft_delete'] && !isset($filters['deleted_at'])) {
            $filters['deleted_at'] = 'NULL';
        }

        $sqlFiltros = self::getSqlFiltros($filters, $strict);

        return GenQuery::select($CONF['table']).$sqlFiltros;
    }

    public static function findById($CONF, $id, $asArray=true, $setHtmlSpecialChars = true)
    {
        $sql = GenQuery::selectRow($CONF['table'], $id);

        if ($asArray) {
            return Db_mysql::getRow($sql, $setHtmlSpecialChars);
        } else{
            return Db_mysql::getRowObject($sql, $setHtmlSpecialChars);
        }
    }

    public static function getValueById($CONF, $id, $field)
    {
        $sql = GenQuery::selectRow($CONF['table'], $id);
        $data = Db_mysql::getRow($sql);

        return $data[$field];
    }

    public static function find($CONF, array $filters, $strict=true)
    {
        $sqlFiltros = self::getSqlFiltros($filters, $strict);

        $sql = "SELECT * FROM " . $CONF['table'] . $sqlFiltros." LIMIT 1";

        return Db_mysql::getRow($sql);
    }

    public static function findEmpty($CONF)
    {
        $columns = Db_mysql::getListOneField("SHOW COLUMNS FROM " . $CONF['table']);
        foreach ($columns as $key => $value) {
            $datos[$key] = '';
        }

        return $datos;
    }

    public static function create($CONF, array $listValues=array())
    {
        return GenQuery::helper_insert($CONF['table'], $listValues);
    }

    public static function update($CONF, array $listValues=array(), $id = '')
    {
        return GenQuery::helper_update($CONF['table'], $listValues, $id);
    }

    public static function delete($CONF)
    {
        if ($CONF['soft_delete']) {
            return GenQuery::softDelete($CONF['table']);
        } else {
            return GenQuery::delete($CONF['table']);
        }
    }
    //--------------------------------------------
    public static function getSqlFiltros(array $filters, $strict=false)
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
        $sqlFiltros = \angelrove\utils\UtilsBasic::implode(' AND ', $listWhere);

        if ($sqlFiltros) {
            $sqlFiltros = ' WHERE '.$sqlFiltros;
        }

        return $sqlFiltros;
    }
    //--------------------------------------------
}
