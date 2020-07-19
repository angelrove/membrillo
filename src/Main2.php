<?php
/**
 * Class Main
 *
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 */

namespace angelrove\membrillo;

use angelrove\membrillo\AppCms2;

class Main2 extends AppCms2
{
    //------------------------------------------------------
    public static function run(string $document_root)
    {
        new Main2($document_root);
    }
    //------------------------------------------------------
    public function __construct(string $document_root)
    {
        parent::__construct($document_root);
    }
    //------------------------------------------------------
}
