<?php
use angelrove\utils\CallApi\CallApi;

$URL_API = '/v3/public/posts?type=plan&search=';

// Call API --------------
$url_api = API_ENVIROMENT.$URL_API.$_GET['q'];
$response = CallApi::callAsObject('GET', $url_api, array());

// Parse data ------------
$result = array();
foreach ($response->body->data as $key => $data) {
    $result[] = '{label: "'.labelToShow($data).'", value: '.$data->id.'}';
}
$result = implode(',', $result);

// Output ----------------
echo $_GET['callback'] . '(['. $result . ']);';

//------------------------------------------------
function labelToShow($data) {
    return $data->content.' - '.$data->city.' - '.$data->id;
}
//------------------------------------------------
