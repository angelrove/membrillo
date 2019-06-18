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
}
