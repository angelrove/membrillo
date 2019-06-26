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
    /**
     *  from Sql
     *  Ejem..: $sqlQ = "SELECT id, CONCAT(apellido,' ',nombre) AS name FROM users";
     *  $selected: puede ser un id o una lista de IDs (para selects multiples)
     */
    public static function get($sqlQ, $value, $name='', $required=false, $placeholder = '')
    {
        if (!$sqlQ) {
            return '';
        }

        $sqlQ .= " ORDER BY name";
        $strSelect        = '';
        $selected_isArray = is_array($value);

        $rows = Db_mysql::getList($sqlQ);
        foreach ($rows as $id => $row) {
            $nombre = ($row['name'])? $row['name'] : $id;

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

            // Placeholder ---
            if ($placeholder) {
                $value = '';
                $label = $placeholder;
                if($placeholder == 'NULL') {
                    $value = 'NULL';
                    $label = '-';
                }

                $placeholder = '<option value="'.$value.'" class="placeholder">-- '.$label.' --</option>';
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

        foreach ($datos as $key => $row)
        {
            $id = $key;
            $nombre = $row;

            // Object type ---
            if ($tipoId == 'AUTO_VALUE') {
                $id = $row;
            }
            if (is_array($row)) {
                if (isset($row['nombre'])) {
                    $nombre = $row['nombre'];
                } else {
                    $nombre = $row['name'];
                }
            }
            if (is_object($row)) {
                $id     = $row->id;
                $nombre = $row->name;
            }

            // Selected ---
            $SELECTED = '';
            if ($id == $id_selected) {
                $SELECTED = ' SELECTED';
            }

            // optgroup ---
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

            // Placeholder ---
            if ($placeholder) {
                $value = '';
                $label = $placeholder;
                if($placeholder == 'NULL') {
                    $value = 'NULL';
                    $label = '-';
                }

                $placeholder = '<option value="'.$value.'" class="placeholder">-- '.$label.' --</option>';
            }

            //----
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
