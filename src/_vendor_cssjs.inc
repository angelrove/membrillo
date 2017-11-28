<?php
use angelrove\utils\Vendor;

// Basics -----
Vendor::conf('jquery', '//cdnjs.cloudflare.com/ajax/libs/', array(
    'jquery/3.2.1/jquery.min.js',
    'jqueryui/1.12.1/jquery-ui.min.js',
    // 'popper.js/1.12.3/umd/popper.min.js',
));

// 'https://code.jquery.com/jquery-3.2.1.slim.min.js',
// 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js',
// Vendor::conf('bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/', array(
//     'css/bootstrap.min.css',
//     'js/bootstrap.min.js',
// ));

Vendor::conf('bootstrap', '//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/', array(
    'js/bootstrap.min.js',
    'css/bootstrap.min.css',
));

Vendor::conf('font-awesome', '//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/', array(
    'css/font-awesome.min.css',
));

// Vendor::conf('less', '//cdnjs.cloudflare.com/ajax/libs/less.js/2.7.2/', array(
//    'less.min.js'
// ));

Vendor::conf('lightbox', '//cdnjs.cloudflare.com/ajax/libs/lightbox2/2.9.0/', array(
    'js/lightbox.min.js',
    'css/lightbox.min.css',
));
//-------------
