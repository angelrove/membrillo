<?php
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 */

namespace angelrove\membrillo\WInputs;

use angelrove\utils\UtilsBasic;

class WInputRadios
{
    //------------------------------------------------------------------
    /* From array */
    public static function get(
        string $name,
        $listDatos,
        $id_selected = '',
        bool $required = false,
        array $listColors = [],
        $is_assoc = ''
    ) {
        $strSelect = '';

        $required = ($required) ? 'required' : '';

        // ¿Viene con claves?
        $isAsociativo = $is_assoc;
        // if ($is_assoc == '') {
        //     $isAsociativo = UtilsBasic::array_is_assoc($listDatos);
        // }

        foreach ($listDatos as $key => $row) {
            // if ($isAsociativo == false) {
            //     $id = $label;
            // }

            // Data ---
            $optionId = '';
            $optionLabel = '';

            if (is_array($row)) {
                $optionId    = $row['id'];
                $optionLabel = $row['name'];
                // $optionLabel = ($row['name'])?? $id;
            } elseif (is_object($row)) {
                $optionId    = $row->id;
                $optionLabel = $row->name;
            } else {
                $optionId    = $key;
                $optionLabel = $row;
            }

            // Selected
            $SELECTED = '';
            if (strcmp($optionId, $id_selected) == 0) {
                $SELECTED = ' checked';
            }

            $idCheck = $name . '_' . $optionId;

            // Color
            $style_bg = '';
            // if ($listColors && isset($listColors[$optionId])) {
            //     $style_bg = 'style="background:' . $listColors[$optionId] . '"';
            // }

            // Option
            $strSelect .= <<<EOD
                 <label class="radio-inline" $style_bg>
                    <input type="radio" $required
                        id="$idCheck"
                        name="$name"
                        value="$optionId" $SELECTED>$optionLabel
                 </label>\n
                 EOD;
        }

        return  <<<EOD
             \n
             <!-- RADIOS -->
             <div class="WInputRadios" id="WInputRadios_$name">
                $strSelect
             </div>
             <!-- RADIOS -->\n
             EOD;
    }
    //------------------------------------------------------------------
    /* Con imagenes */
    public static function show2($name, $id_check, $listDatos, $id_selected, $required = false)
    {
        $strSelect = '';

        $required = ($required) ? 'required' : '';

        // ¿Viene con claves?
        $isAsociativo = ($listDatos[0]) ? false : true;

        $nameCheck = $name . '[' . $id_check . ']';
        $idCheck   = $name . '_' . $id_check . '_';

        foreach ($listDatos as $id => $image) {
            // Selected
            $SELECTED = '';
            if ($id == $id_selected) {
                $SELECTED = ' checked';
            }

            $idCheck .= $id;

            // Option
            $strSelect .= <<<EOD
          <label class="radio-inline">
            <input type="radio" $required
                   id="$idCheck"
                   name="$nameCheck"
                   value="$id" $SELECTED>
            $image
          </label>
EOD;
        }

        return
            '<div ' . $required . ' class="WInputRadios_img" id="WInputRadios_img_' . $name . '">' .
            $strSelect .
            '</div>';
    }
    //----------------------------------------------------------------------
}
