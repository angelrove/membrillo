<?php
namespace App\Models;

use angelrove\membrillo\Database\Model;
use angelrove\membrillo\Database\GenQuery;

class [name_model] extends Model
{
    protected const CONF = array(
        'table' => '[table_name]',
        'soft_delete' => true,
    );

    // public static function read(array $conditions=array(), array $filter_data=array())
    // {
    //     $conditions[] = "id <> 1";
    //
    //     // Search ---
    //     $conditions['f_text'] = "name LIKE '%[VALUE]%'";
    //     $conditions['f_deleted'] = [
    //         'default' => "deleted_at IS NULL",
    //         1 => "deleted_at IS NOT NULL",
    //     ];

    //     return parent::read($conditions, $filter_data);
    // }
}
