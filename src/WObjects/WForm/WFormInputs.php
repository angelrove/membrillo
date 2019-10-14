<?php
/**
 * WForm
 *
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 */

namespace angelrove\membrillo\WObjects\WForm;

use angelrove\membrillo\WInputs\WInputSelect;
use angelrove\membrillo\WInputs\WInputCheck;
use angelrove\membrillo\WInputs\WInputTextarea;

trait WFormInputs
{
    //------------------------------------------------------------------
    public function getField($title, $htmInput, $name = '')
    {
        return '
        <div class="form-group" id="obj_'.$name.'">
           <label class="col-sm-3 control-label">'.$title.'</label>
           <div class="col-sm-9">'.$htmInput.'</div>
        </div>
        ';
    }
    //------------------------------------------------------------------
    //------------------------------------------------------------------
    public function input($name, $type = 'text', $title = '', $required = false, array $params = [])
    {
        return $this->getInput($name, $title, $required, $type, $params);
    }
    //------------------------------------------------------------------
    /*
     * $type: select, select_query, select_array, select_object,
     *        checkbox, textarea, text_read, hidden, number, url
     */
    public function getInput($name, $title = '', $required = false, $type = 'text', array $params = [])
    {
        if ($title === false) {
        } elseif ($title == '') {
            $title = $name;
        }

        if (!$type) {
            $type = 'text';
        }

        switch ($type) {
            case 'select':
            case 'select_query':
                $sqlQ = ($params['query'])?? $params[0];

                $emptyOption = ($params['emptyOption'])?? $params[1] ?? '';
                if ($emptyOption === true) {
                    $emptyOption = '-';
                }

                $htmInput = WInputSelect::get($sqlQ, $this->datos[$name], $name, $required, $emptyOption);
                break;

            case 'select_array':
            case 'select_object':
                $values = $params[0];

                $emptyOption = '';
                if (isset($params[1]) && $params[1]) {
                    $emptyOption = ($params[1] === true)? '-' : $params[1];
                }

                $htmInput = WInputSelect::get($values, $this->datos[$name], $name, $required, '', $emptyOption);
                break;

            case 'checkbox':
                $htmInput = WInputCheck::get($name, '&nbsp;', $this->datos[$name], $required);
                break;

            case 'textarea':
                $maxlength  = (isset($params['maxlength']))? $params['maxlength'] : '';
                $attributes = (isset($params['attributes']))? $params['attributes'] : '';
                $htmInput = WInputTextarea::get($name, $this->datos[$name], $required, '', $maxlength, $attributes);
                break;

            case 'readonly':
            case 'text_read':
                $htmInput = '<input disabled class="form-control" value="'.$this->datos[$name].'">';
                break;

            case 'hidden':
                return '<input type="hidden" name="'.$name.'" value="'.$this->datos[$name].'">';
                break;

            case 'number':
                $extraHtml = $params[0]?? '';
                $extraHtml .= 'style="width:initial"';
                $htmInput = $this->getInput1($title, $name, $this->datos[$name], $type, $required, false, $extraHtml);
                break;

            case 'percentage':
                $type = 'number';
                $extraHtml = 'min="0" max="100" step=".01"';
                $extraHtml .= 'style="width:initial"';
                $htmInput = $this->getInput1($title, $name, $this->datos[$name], $type, $required, false, $extraHtml);
                break;

            case 'price':
                $extraHtml = $params[0]?? '';
                $extraHtml .= ' min="0" step=".01" style="width:initial"';
                $htmInput = $this->getInput1($title, $name, $this->datos[$name], 'number', $required, false, $extraHtml);
                break;

            case 'url':
                $extra = 'style="display:initial;width:95%"';

                if ($this->datos[$name]) {
                    $title = '<a target="_blank" href="'.$this->datos[$name].'">'.$title.'</a>';
                }

                $htmInput = $this->getInput1(
                    $title,
                    $name,
                    $this->datos[$name],
                    $type,
                    $required,
                    false,
                    $extra
                );
                break;

            default:
                $extraHtml = $params[0]?? '';
                $htmInput = $this->getInput1($title, $name, $this->datos[$name], $type, $required, false, $extraHtml);
                break;
        }

        if ($title === false) {
            return $htmInput;
        }
        return $this->getField($title, $htmInput, $name);
    }
    //------------------------------------------------------------------
    public function getInput1(
        $title,
        $name,
        $value = '',
        $type = 'text',
        $required = false,
        $flag_placeholder = false,
        $extraHtml = ''
    ) {
        $required = ($required) ? 'required' : '';

        $placeholder = '';
        if ($flag_placeholder) {
            $placeholder = 'placeholder="' . $title . '"';
        }

        switch ($type) {
            // See: UtilsBasic::strDateChromeToTimestamp()
            case 'datetime-local':
                $type = 'datetime-local';
                if (is_integer($value)) {
                    $value = self::timestampToDate($value, 'Y-m-d\TH:i', Login::$timezone);
                } else {
                    $value = str_replace(" ", "T", $value);
                }
                break;
        }

        return '<input class="form-control type_'.$type.'"'.
                   ' ' . $placeholder .
                   ' ' . $required .
                   ' ' . $extraHtml .
                   ' type="'  . $type  . '"'.
                   ' name="'  . $name  . '"'.
                   ' value="' . $value . '"'.
               '>';
    }
    //---------------------------------------------------
    // Input datetime helpers
    private static function timestampToDate($timestamp, $toFormat = 'Y-m-d\TH:i', $toTimezone = null)
    {
        $datetime = new \DateTime();
        $datetime->setTimestamp($timestamp);

        if ($toTimezone) {
            $datetime->setTimeZone(new \DateTimeZone($toTimezone));
        }

        return $datetime->format($toFormat);
    }
    //---------------------------------------------------
    public static function dateTimeToTimestamp($dateTime)
    {
        $time = false;

        // 2018-01-01T22:02 -------
        if ($date = \DateTime::createFromFormat('Y-m-d\TH:i', $dateTime)) {
        }
        // 2018-01-01T22:02:00 -------
        elseif ($date = \DateTime::createFromFormat('Y-m-d\TH:i:s', $dateTime)) {
        }

        if ($date) {
            return $date->getTimestamp();
        } else {
            throw new \Exception("WForm::dateTimeToTimestamp(): Error processing date!! [$dateTime]");
        }

        return $time;
    }
    //------------------------------------------------------------------
}
