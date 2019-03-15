<?php
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 */

namespace angelrove\membrillo;

class CrudUrl
{
    //-------------------------------------------------------------
    public static function get($event = '', $control = 0, $id = '', $oper = '', $other_params = '')
    {
        $params = array();

        // Params ----
        if ($oper) {
            $params[] = "OPER=$oper";
        }

        // Other params ----
        if ($other_params) {
            $params[] = $other_params;
        }
        $params = implode('&', $params);

        if ($params) {
            $params = '?' . $params;
        }

        // CRUD ----
        $crd_event = ($event) ? "/$event" : '';
        $crd_id    = ($id) ? "/$id" : '';

        if ($control == '') {
            return '/' . $_GET['secc'].'/';
        }

        return '/' . $_GET['secc'] . '/crd/' . $control . $crd_event . $crd_id . '/' . $params;
    }
    //-------------------------------------------------------------
}
