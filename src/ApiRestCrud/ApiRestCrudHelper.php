<?php
/**
 * ApiRestCrudHelper
 */

namespace angelrove\membrillo\ApiRestCrud;

use angelrove\membrillo\ApiRestCrud\ApiRestCrudInterface;
use angelrove\utils\CallApi\CallApi;

class ApiRestCrudHelper
{
    private const API_ENVIROMENT = API_ENVIROMENT;
    private const API_AUTH_TOKEN = API_AUTH_TOKEN;
    private const API_ACCEPT_LANGUAGE = API_ACCEPT_LANGUAGE;

    //--------------------------------------------------------------
    public static function create($entity, $body)
    {
        return self::callApi('POST', $entity, array(), $body);
    }
    //--------------------------------------------------------------
    public static function update($entity, $id, $body)
    {
        $entity = $entity.'/'.$id;
        return self::callApi('PUT', $entity, array(), $body);
    }
    //--------------------------------------------------------------
    public static function delete($id)
    {
        $entity = $entity.'/'.$id;
        $body = array('status' => false);

        return self::callApi('PUT', $entity, array(), $body);
    }
    //--------------------------------------------------------------
    public static function read($entity, $asJson=false, $params='')
    {
        return self::callApi('GET', $entity, array(), array(), $asJson, $params);
    }
    //--------------------------------------------------------------
    public static function readById($entity, $id)
    {
        $entity = $entity.'/'.$id;
        return self::callApi('GET', $entity);
    }
    //--------------------------------------------------------------
    // PRIVATE
    //--------------------------------------------------------------
    private static function callApi($method, $entity, array $headers=array(), array $body=array(), $asJson=false, $params)
    {
        $url = self::API_ENVIROMENT.$entity.'?'.$params;

        $headers_def = array(
            'x-auth-token'   => self::API_AUTH_TOKEN,
            'Accept-Language'=> self::API_ACCEPT_LANGUAGE
        );
        $headers = array_merge($headers_def, $headers);

        // Call API --------
        $response = CallApi::call($method, $url, $headers, $body, $asJson);

        // Parse result ----
        return self::parseResult($response, $method);
    }
    //--------------------------------------------------------------
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

        return $ret;
    }
    //---------------------------------------------------------
    private static function formatMessage($message)
    {
        return
         '<div style="max-width:500px;color:#333;font-size:12px;background-color:#fff;padding:4px">'.
             $message.
         '</div>';
    }
    //--------------------------------------------------------------
}
