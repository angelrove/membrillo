<?php
/**
 *
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 */

namespace angelrove\membrillo\WInputs;

use angelrove\utils\Db_mysql;

class WInputSelect
{
    private $name;
    private $data;
    private $id_selected;
    private $required;
    private $placeholder;
    private $listColors;
    private $listGroup;

    //-------------------------------------------------------------
    /**
     * @param [collection / SQL string] $data
     * @param int $id_selected: un id o una lista de IDs (selects multiples)
     */
    public function __construct($name, $data, $id_selected)
    {
        $this->name = $name;
        $this->data = $data;
        $this->id_selected = $id_selected;
    }
    //-------------------------------------------------------------
    public function required(bool $required)
    {
        $this->required = $required;
        return $this;
    }

    public function placeholder(string $placeholder)
    {
        $this->placeholder = $placeholder;
        return $this;
    }

    public function colors($listColors)
    {
        $this->listColors = $listColors;
        return $this;
    }

    public function groups($listGroup)
    {
        $this->listGroup = $listGroup;
        return $this;
    }
    //-------------------------------------------------------------
    /**
     * Static version
     */
    public static function get(
        $data,
        $id_selected = '',
        string $name = '',
        bool $required = false,
        string $tipoId = '',
        string $placeholder = '',
        array $listColors = [],
        array $listGroup = []
    ) {
        $selector = new WInputSelect($name, $data, $id_selected);
        return $selector->required($required)
                 ->placeholder($placeholder)
                 ->colors($listColors)
                 ->groups($listGroup)
                 ->html();
    }
    //-------------------------------------------------------------
    public function html()
    {
        // Data in SQL format -----
        if (is_string($this->data)) {
            $data = Db_mysql::getList($this->data);
        }

        $isMultiSelect = is_array($this->id_selected);

        //-----------------
        $htmOptions = '';
        foreach ($this->data as $key => $row) {
            // $optionLabel = ($row['name'])? $row['name'] : $id;

            // Option data ---
            $optionId = '';
            $optionLabel = '';
            if (is_array($row)) {
                $optionId    = $row['id'];
                $optionLabel = $row['name'];
            } elseif (is_object($row)) {
                $optionId    = $row->id;
                $optionLabel = $row->name;
            } else {
                $optionId    = $key;
                $optionLabel = $row;
            }

            // Selected ---
            $SELECTED = '';
            if ($isMultiSelect) {
                if (array_search($optionId, $this->id_selected) !== false) {
                    $SELECTED = 'SELECTED';
                }
            } else {
                if ($optionId == $this->id_selected) {
                    $SELECTED = 'SELECTED';
                }
            }

            // optgroup ---
            if ($this->listGroup && isset($this->listGroup[$optionId])) {
                if ($htmOptions) {
                    $htmOptions .= '</optgroup>';
                }
                $htmOptions .= '<optgroup label="' . $this->listGroup[$optionId] . '">';
            }

            // Style -----
            $style = '';
            if ($this->listColors) {
                $style = 'style="background:' . $this->listColors[$optionId] . '"';
            }

            // Option ----
            $htmOptions .= "<option $style value=\"$optionId\" $SELECTED>$optionLabel</option>";
        }

        if ($this->listGroup) {
            $htmOptions .= '</optgroup>';
        }

        //------------
        if ($this->name) {
            return self::htmSelect($this->name, $htmOptions, $this->required, $this->placeholder);
        } else {
            return $htmOptions;
        }
    }
    //--------------------------------------------------------------
    private function htmSelect($name, $htmOptions, $required, $placeholder)
    {
        $required = ($required) ? 'required' : '';

        // Placeholder ---
        $optionPlaceholder = '';
        if ($placeholder) {
            $value = '';
            $label = $placeholder;
            if ($placeholder == 'NULL') {
                $value = 'NULL';
                $label = '-';
            }

            $optionPlaceholder = '<option value="'.$value.'" class="placeholder">-- '.$label.' --</option>';
        }

        // Selector ------
        return
        "<select name=\"$name\" class=\"form-control\" $required>" .
            $optionPlaceholder .
            $htmOptions .
        "</select>";
    }
}
