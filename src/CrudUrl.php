<?php
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 */

namespace angelrove\membrillo;

class CrudUrl
{
    //-------------------------------------------------------------
    public static function get(
        string $event = '',
        string $control = '0',
        string $id = '',
        string $oper = '',
        $other_params = ''
    ): string {

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

        $crudUrl = '';
        if ($control === '') {
            $crudUrl = '/' . $_GET['secc'].'/';
        } else {
            $crudUrl = '/' . $_GET['secc'] . '/crd/' . $control . $crd_event . $crd_id . '/' . $params;
        }

        return $crudUrl;
    }
    //-------------------------------------------------------------
}
