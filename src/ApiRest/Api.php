<?php
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 * 2017
 *
 */

namespace angelrove\membrillo2\ApiRest;

use angelrove\membrillo2\WObjectsStatus\Event;

class Api
{
    //-------------------------------------------------
    public static function response($data)
    {
        echo json_encode($data);
        exit();
    }
    //-------------------------------------------------
    public static function responseOrFail($data)
    {
        if (EVENT::$EVENT == API_GET_FIND) {
            if (!$data) {
                $data = array(
                    'code'=>'404',
                    'message'=>'api.exception.notfound'
                );
            }
        }
        elseif(EVENT::$EVENT == API_GET_EXIST) {
            if ($data) {
                $data = array(
                    'code'=>'200',
                    'message'=>'exist'
                );
            }
            else {
                $data = array(
                    'code'=>'400',
                    'message'=>'api.exception.notfound'
                );
            }
        }

        self::response($data);
    }
    //-------------------------------------------------
}
