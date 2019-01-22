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
    public static function read($entity)
    {
        return self::callApi('GET', $entity);
    }
    //--------------------------------------------------------------
    public static function readById($entity, $id)
    {
        $entity = $entity.'/'.$id;
        return self::callApi('GET', $entity);
    }
    //--------------------------------------------------------------
    private static function callApi($method, $entity, array $headers=array(), array $body=array())
    {
        $url = self::API_ENVIROMENT.$entity;

        $headers_def = array(
            'x-auth-token'   => self::API_AUTH_TOKEN,
            'Accept-Language'=> self::API_ACCEPT_LANGUAGE
        );
        $headers = array_merge($headers_def, $headers);

        return CallApi::call($method, $url, $headers, $body);
    }
    //--------------------------------------------------------------
}
