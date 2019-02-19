<?php
use angelrove\utils\CallApi\CallApi;

// Call API --------------
$URL_API = '/v3/public/posts?type=plan&search=';
$URL_API = API_ENVIROMENT.$URL_API.$_GET['q'];
$response = CallApi::call('GET', $URL_API, array());

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
