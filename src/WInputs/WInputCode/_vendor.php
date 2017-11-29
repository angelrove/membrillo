<?php
use angelrove\utils\Vendor;

Vendor::conf('codemirror', 'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.23.0/', array(
    'codemirror.css',
    'codemirror.js',
    // 'doc/docs.css',

    'addon/selection/active-line.js',
    'addon/edit/matchbrackets.js',

    // 'keymap/sublime.js',

    'addon/hint/show-hint.css',
    'addon/hint/show-hint.js',
    'addon/hint/css-hint.js',
    'theme/' . $theme . '.css',
    'mode/' . $type . '/' . $type . '.js',
));
