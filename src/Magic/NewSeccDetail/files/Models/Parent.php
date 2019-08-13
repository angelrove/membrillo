<?php
namespace App\Models;

use angelrove\membrillo\Database\Model;
use angelrove\membrillo\Login\Login;
use angelrove\utils\Db_mysql;

class [name_model] extends Model
{
    protected const CONF = array(
        'table' => '[name_table]',
        'soft_delete' => true,
    );

}
