<?php
/**
 * WForm
 *
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 */

namespace angelrove\membrillo\WObjects\WForm;

use angelrove\membrillo\Messages;
use angelrove\membrillo\WObjectsStatus\Event;
use angelrove\utils\CssJsLoad;

trait ErrorsTrait
{
    private static $errors = false;

    //------------------------------------------------------------------
    /**
     * Para ser llamada desde la operación de insert
     */
    public static function setValueError(array $listErrors, $id = ''): void
    {
        if (!$listErrors) {
            return;
        }

        self::$errors = $listErrors;

        // Continue with edit(no redirection): mantener los datos recibidos por post
        Event::$REDIRECT_AFTER_OPER = false;

        // Event ---
        if ($id) {
            Event::setEvent(CRUD_EDIT_UPDATE);
        } else {
            Event::setEvent(CRUD_EDIT_NEW);
        }

        // Js to Highlight errors ---
        $js = '';
        foreach ($listErrors as $name => $err) {
            Messages::set($err, 'danger');
            $js .= '$("[name=' . $name . ']").css("border", "2px solid red");';
        }

        // focus in the first failed input
        // end($listErrors);
        // $js .= '$("[name='.key($listErrors).']").focus();'."\n";

        CssJsLoad::set_script('$(document).ready(function() {' . $js . '});');
    }
    //------------------------------------------------------------------
}
