<?php
//------------------------------------------------------------------
function print_r2($object, bool $setComment = false)
{
    if ($setComment === true) {
        $ret = "\n<!--\n" . print_r($object, true) . "\n-->\n";
    } else {
        $ret = '<span style="font-size:10px"><pre style="text-align:left">' . print_r($object, true) . '</pre></span>';
    }
    echo $ret;
}
//------------------------------------------------------------------
function dd($object, bool $setComment = false)
{
    print_r2($object, $setComment = false);
}
//------------------------------------------------------------------
