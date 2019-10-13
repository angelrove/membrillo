<?php
/**
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 *
 */

namespace angelrove\membrillo\WInputs;

class WInputTextarea
{
    public static function get(string $name, $value, bool $required = false, string $title = '', $maxlength = '', string $attributes = '')
    {
        $required = ($required) ? 'required' : '';
        $placeholder = ($title)? 'placeholder="'.$title.'"' : '';

        return '<textarea '.$placeholder.
                    ' name="'.$name.'"'.
                    ' class="form-control"'.
                    ' '.$attributes.
                    ' maxlength="'.$maxlength.'" '.
                    $required.
                '>'.$value.'</textarea>';
    }
}
