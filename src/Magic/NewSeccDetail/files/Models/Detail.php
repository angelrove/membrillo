<?php
namespace App\Models;

use angelrove\membrillo\Database\Model;
use angelrove\membrillo\Database\GenQuery;
use angelrove\membrillo\Login\Login;

class [name_model] extends Model
{
    protected const CONF = array(
        'table' => '[name_table]',
        'soft_delete' => true,
    );

    //-------------------------------------------------------------------
    public static function list(array $filter_data)
    {
        // [parent_id]
        if (isset($filter_data['f_parent'])) {
            if ($filter_data['f_parent'] == 'NULL') {
                $conditions['f_parent'] = "A.[parent_id] IS NULL";
            } else {
                $conditions['f_parent'] = "A.[parent_id] = '[VALUE]'";
            }
        }

        // Search ---
        $conditions['f_text'] = "(A.name LIKE '%[VALUE]%' OR A.email LIKE '%[VALUE]%')";
        if ($filter_data['f_text']) {
            $conditions['f_parent'] = false;
        }

        $conditions['f_deleted'] = [
           'default' => "A.deleted_at IS NULL",
                   1 => "A.deleted_at IS NOT NULL",
        ];

        $conditions['f_profile'] = "A.profile = '[VALUE]'";

        // Query ---
        $sqlFilters = GenQuery::getSqlFilters($conditions, $filter_data);
        // print_r2($sqlFilters);

        return "SELECT A.*,
                       B.name AS department
                 FROM ".self::CONF['table']." AS A
                 LEFT JOIN [table_parent] B ON(A.[parent_id]=B.id)
                 $sqlFilters";
    }
    //-------------------------------------------------------------------
}
