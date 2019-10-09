<?php

namespace angelrove\membrillo\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Capsule\Manager as Capsule;

class ModelPlus extends Model
{
    //--------------------------------------------------
    public static function emptyRow(): array
    {
        $emptyRow = [];

        $columns = self::getTableColumns();

        foreach ($columns as $column) {
            $emptyRow[$column] = '';
        }

        return $emptyRow;
    }
    //--------------------------------------------------
    // PRIVATE
    //--------------------------------------------------
    private static function getTableColumns()
    {
        $tableName = self::getTableName();
        return Capsule::schema()->getColumnListing($tableName);
    }

    private static function getTableName()
    {
        return (new static)->getTable();
    }
    //--------------------------------------------------
}
