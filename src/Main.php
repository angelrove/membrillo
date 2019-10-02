<?php
/**
 * Class Main
 *
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 */

namespace angelrove\membrillo;

use angelrove\membrillo\AppCms;

class Main extends AppCms
{
    //------------------------------------------------------
    public static function run($document_root)
    {
        new Main($document_root);
    }
    //------------------------------------------------------
    public function __construct($document_root)
    {
        parent::__construct($document_root);
    }
    //------------------------------------------------------
}
