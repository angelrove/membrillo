<?php
/**
 * Class Main
 *
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 */

namespace angelrove\membrillo;

use angelrove\membrillo\AppCmsAjax;

class MainAjax extends AppCmsAjax
{
    //------------------------------------------------------
    public static function run($document_root)
    {
        new MainAjax($document_root);
    }
    //------------------------------------------------------
    public function __construct($document_root)
    {
        parent::__construct($document_root);
    }
    //------------------------------------------------------
}
