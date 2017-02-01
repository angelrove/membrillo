<?
$base_path = './_uploads/';

/** Evitar HotLink **/
 if(isRemoteIp($_SERVER['REMOTE_ADDR'])) {
    $setError = 'ext-getImage.php: external access';

    header("HTTP/1.0 404 Not Found");
    header("Status: 404 Not Found");
    exit();
 }

/** xxxx **/
 readfile($base_path.'usuarios_foto_'.$_GET['id'].'.jpg');

//---------------------------------------------------
function isRemoteIp($ip) {
  $localIps = array('127.0.0.1',
                    '111.168',
                    );

  foreach($localIps as $localIp) {
     if(strstr($ip, $localIp)) return false;
  }

  return true;
}
//---------------------------------------------------
