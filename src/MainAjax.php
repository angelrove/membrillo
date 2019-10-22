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
    public static function run(string $document_root)
    {
        new MainAjax($document_root);
    }
    //------------------------------------------------------
    public function __construct(string $document_root)
    {
        parent::__construct($document_root);
    }
    //------------------------------------------------------
}
