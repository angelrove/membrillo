<?php
use angelrove\utils\Vendor;

// jquery ---
Vendor::conf('jquery', '//cdnjs.cloudflare.com/ajax/libs/', array(
    'jquery/3.3.1/jquery.min.js',
    'jqueryui/1.12.1/jquery-ui.min.js',
    'jqueryui/1.12.1/themes/base/jquery-ui.css',
    // 'popper.js/1.12.3/umd/popper.min.js',
));

// bootstrap ---
Vendor::conf('bootstrap', '//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/', array(
    'js/bootstrap.min.js',
    'css/bootstrap.min.css',
));

// font-awesome ---
Vendor::conf('font-awesome', '', array(
    'https://use.fontawesome.com/releases/v5.7.0/js/all.js',
));
\angelrove\utils\CssJsLoad::set_script("
    window.FontAwesomeConfig.searchPseudoElements = true;
");

// material-icons ---
Vendor::conf('material-icons', '', array(
    'https://fonts.googleapis.com/icon?family=Material+Icons',
));

// less ---
// Vendor::conf('less', '//cdnjs.cloudflare.com/ajax/libs/less.js/2.7.2/', array(
//    'less.min.js'
// ));

// lightbox ---
Vendor::conf('lightbox', '//cdnjs.cloudflare.com/ajax/libs/lightbox2/2.9.0/', array(
    'js/lightbox.min.js',
    'css/lightbox.min.css',
));

// Datatables ---
Vendor::conf('datatables',
   'https://cdn.datatables.net/v/bs/dt-1.10.18/b-1.5.4/b-colvis-1.5.4/b-html5-1.5.4/b-print-1.5.4/sl-1.2.6/', array(
         'datatables.min.js',
         'datatables.min.css',
));
