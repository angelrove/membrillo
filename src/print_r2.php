<?php
//------------------------------------------------------------------
function print_r2($object, bool $setComment = false)
{
    $varDump = htmlspecialchars(print_r($object, true));

    if ($setComment === true) {
        echo "\n<!--\n" . $varDump . "\n-->\n";
    } else {
        echo '<span style="font-size:10px"><pre style="text-align:left">' . $varDump . '</pre></span>';
    }
}
//------------------------------------------------------------------
function ddx($object, bool $setComment = false)
{
    print_r2($object, $setComment = false);
}
//------------------------------------------------------------------
