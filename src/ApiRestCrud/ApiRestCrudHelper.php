<?php
/**
 * ApiRestCrudHelper
 */

namespace angelrove\membrillo\ApiRestCrud;

use angelrove\membrillo\ApiRestCrud\ApiRestCrudInterface;
use angelrove\membrillo\Messages;
use angelrove\utils\CallApi\CallApi;

class ApiRestCrudHelper
{
    private const API_ENVIROMENT = API_ENVIROMENT;
    private const API_AUTH_TOKEN = API_AUTH_TOKEN;
    private const API_ACCEPT_LANGUAGE = API_ACCEPT_LANGUAGE;

    public static function read(array $conf, $asJson=false, $params='')
    {
        $entity = ($conf['ENTITY_READ'])?? $conf['ENTITY'];
        return self::callApi('GET', $entity, array(), array(), $asJson, $params);
    }

    public static function readById(array $conf, $id)
    {
        $entity = $conf['ENTITY'].'/'.$id;
        return self::callApi('GET', $entity);
    }

    public static function readEmpty($columns)
    {
        return array_fill_keys($columns, '');
    }

    public static function create(array $conf, $columns, $data)
    {
        $body = self::parseData($data, $columns);
        return self::callApi('POST', $conf['ENTITY'], array(), $body);
    }

    public static function update(array $conf, $columns, $id, $data)
    {
        $body = self::parseData($data, $columns);
        $entity = $conf['ENTITY'].'/'.$id;

        return self::callApi($conf['UPDATE_METHOD'], $entity, array(), $body);
    }

    public static function delete(array $conf, $id)
    {
        $entity = $conf['ENTITY'].'/'.$id;
        $body = array('status' => false);

        return self::callApi('PUT', $entity, array(), $body);
    }
    //--------------------------------------------------------------
    // PRIVATE
    //--------------------------------------------------------------
    private static function callApi($method, $entity, array $headers=array(), array $body=array(), $asJson=false, array $params=array())
    {
        // Params ---
        $paramsStr = '';
        foreach ($params as $param => $value) {
            $paramsStr .= $param.'='.$value.'&';
        }

        // Url ---
        $url = self::API_ENVIROMENT.$entity.'?'.$paramsStr;
        // print_r2($url);

        // Header ---
        $headers_def = array(
            'x-auth-token'   => self::API_AUTH_TOKEN,
            'Accept-Language'=> self::API_ACCEPT_LANGUAGE
        );
        $headers = array_merge($headers_def, $headers);

        // Call API --------
        try {
            $response = CallApi::call($method, $url, $headers, $body, $asJson);
        } catch (\Exception $e) {
            $response = new \StdClass();
            $response->statusCode = 'Exception';
            $response->body       = $e->getMessage();
        }

        // Parse result ----
        return self::parseResult($response, $method);
    }

    private static function parseData($data, $columns)
    {
        $dataParsed = array();
        foreach ($columns as $column) {
            if (isset($data[$column])) {
                $dataParsed[$column] = $data[$column];
            }
        }

        return $dataParsed;
    }

    private static function parseResult($result, $method)
    {
        // GET ---
        if ($method == 'GET' && $result->statusCode == '200') {
            return [
                'result' => true,
                'statuscode' => $result->statusCode,
                'body'   => $result->body
            ];
        }

        // POST ---
        if ($method == 'POST' && $result->statusCode == '200') {
            return [
                'result' => true,
                'statuscode' => $result->statusCode,
                'id'     => $result->body->id
            ];
        }

        // PATCH ---
        if ($method == 'PATCH' && $result->statusCode == '202') {
            return [
                'result' => true,
                'statuscode' => $result->statusCode
            ];
        }

        // PUT ---
        if ($method == 'PUT' && $result->statusCode == '202') {
            return [
                'result' => true,
                'statuscode' => $result->statusCode
            ];
        }

        // ERROR ---
        $ret = [
            'result' => false,
            'statuscode' => $result->statusCode
        ];

        if (isset($result->body->message)) {
            $ret['message'] = $result->body->message;
        } else {
            $ret['message'] = htmlspecialchars(print_r($result->body, true));
        }
        $ret['message_format'] = self::formatMessage(
                                  'Status: '.$result->statusCode.'<br>'.$ret['message']
                                 );

        // Message
        Messages::set($ret['message_format'], 'danger');

        return $ret;
    }

    private static function formatMessage($message)
    {
        return
         '<div style="max-width:500px;color:#333;font-size:12px;background-color:#fff;padding:4px">'.
             $message.
         '</div>';
    }
}
