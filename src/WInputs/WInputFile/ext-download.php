<?
// Download
$file     = $_REQUEST['f'];
$fileUser = $_REQUEST['fu'];
$mime     = $_REQUEST['mime'];
//$size = filesize($file);

header("Content-type: $mime;");
//header("Content-Length: $size;");
header('Content-Disposition: attachment; filename="'.$fileUser.'";');
readfile($file);
