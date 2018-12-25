<?php
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 */

namespace angelrove\membrillo\WInputs;

use angelrove\utils\Db_mysql;

class WInputSelect
{
    //------------------------------------------------------------------
    public static function get2($dbTable, $value, $name = '', $required = false, $placeholder='')
    {
        $sqlQ = "SELECT id, name FROM $dbTable ORDER BY name";
        return self::get($sqlQ, $value, $name, $required, $placeholder);
    }
    //------------------------------------------------------------------
    /**
     * from Sql
     * Ejem..: $sqlQ = "SELECT idusuario AS id, CONCAT(apellido,' ',nombre) AS nombre FROM usuarios";
     *  $selected: puede ser un id o una lista de IDs (para selects multiples)
     */
    public static function get($sqlQ, $value, $name='', $required=false, $placeholder = '')
    {
        if (!$sqlQ) {
            return '';
        }

        $strSelect        = '';
        $selected_isArray = is_array($value);

        $rows = Db_mysql::getList($sqlQ);
        foreach ($rows as $id => $row) {

            $nombre = @($row['nombre'] || $row['name'])? @($row['nombre'].$row['name']) : $id;

            // Selected
            $SELECTED = '';
            if ($selected_isArray) {
                if (array_search($id, $value) !== false) {
                    $SELECTED = 'SELECTED';
                }

            } else {
                if ($id == $value) {
                    $SELECTED = 'SELECTED';
                }

            }

            // Option
            $strSelect .= "<option value=\"$id\" $SELECTED>$nombre</option>";
        }

        if ($name) {
            $required = ($required) ? 'required' : '';
            if ($placeholder) {
                $placeholder = '<option value="" class="placeholder">-- '.$placeholder.' --</option>';
            }

            $strSelect =
                "<select name=\"$name\" class=\"form-control\" $required>" .
                    $placeholder .
                    $strSelect .
                "</select>";
        }

        return $strSelect;
    }
    //------------------------------------------------------------------
    /**
     * from array
     * $tipoId: AUTO_INCR, AUTO_VALUE
     */
    public static function getFromArray($datos,
                                        $id_selected,
                                        $name = '',
                                        $required = false,
                                        $tipoId = '',
                                        $placeholder='',
                                        $listColors = '',
                                        $listGroup = array())
    {
        $strSelect = '';

        foreach ($datos as $id => $nombre) {
            if ($tipoId == 'AUTO_VALUE') {
                $id = $nombre;
            }
            if (is_array($nombre)) {
                if (isset($nombre['nombre'])) {
                    $nombre = $nombre['nombre'];
                }
                else {
                    $nombre = $nombre['name'];
                }
            }

            // Selected
            $SELECTED = '';
            if ($id == $id_selected) {
                $SELECTED = ' SELECTED';
            }

            // optgroup
            if (isset($listGroup[$id])) {
                if ($strSelect) {
                    $strSelect .= '</optgroup>';
                }
                $strSelect .= '<optgroup label="' . $listGroup[$id] . '">';
            }

            // Option
            $style = '';
            if ($listColors) {
                $style = 'style="background:' . $listColors[$id] . '"';
            }
            $strSelect .= '<option ' . $style . ' value="' . $id . '"' . $SELECTED . '>' . $nombre . '</option>';
        }
        if ($listGroup) {
            $strSelect .= '</optgroup>';
        }

        if ($name) {
            $required  = ($required) ? 'required' : '';

            if ($placeholder) {
                $placeholder = '<option value="" class="placeholder">-- '.$placeholder.' --</option>';
            }

            $strSelect =
                "<select name=\"$name\" class=\"form-control\" $required>" .
                   $placeholder .
                   $strSelect .
                '</select>';
        }

        return $strSelect;
    }
    //------------------------------------------------------------------
}
