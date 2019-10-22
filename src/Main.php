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
    public static function run(string $document_root)
    {
        new Main($document_root);
    }
    //------------------------------------------------------
    public function __construct(string $document_root)
    {
        parent::__construct($document_root);
    }
    //------------------------------------------------------
}
