<?php
//------------------------------------------------------------------
function print_r2($object, bool $setComment = false)
{
    $varDump = htmlspecialchars(print_r($object, true));

    if ($setComment === true) {
        $ret = "\n<!--\n" . $varDump . "\n-->\n";
    } else {
        $ret = '<span style="font-size:10px"><pre style="text-align:left">' . $varDump . '</pre></span>';
    }
    echo $ret;
}
//------------------------------------------------------------------
function dd($object, bool $setComment = false)
{
    print_r2($object, $setComment = false);
}
//------------------------------------------------------------------
