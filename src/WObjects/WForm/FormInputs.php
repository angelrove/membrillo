<?php
/**
 * FormInputs
 * <?=$form->fInput('text', 'job_number', 'Job Nº')->required()->get()?>
 *
 * @author José A. Romero Vegas <jangel.romero@gmail.com>
 */

namespace angelrove\membrillo\WObjects\WForm;

use angelrove\membrillo\WInputs\WInputSelect;
use angelrove\membrillo\WInputs\WInputCheck;
use angelrove\membrillo\WInputs\WInputTextarea;

class FormInputs
{
    private $type;
    private $name;
    private $value;
    private $title = '';
    private $required = false;
    private $readonly = false;
    private $placeholder = false;
    private $listData;

    private $params = [];
    private $htmlAttributes = '';

    //-------------------------------------------------------
    public function __construct(string $type, string $name = '', string $title = '', $value = '')
    {
        $this->type = $type;
        $this->name = $name;
        $this->title = $title;
        $this->value = $value;

        if ($this->title === false) {
        } elseif ($this->title == '') {
            $this->title = $this->name;
        }
    }
    //-------------------------------------------------------
    // Properties
    //-------------------------------------------------------
    public function title(string $title)
    {
        $this->title = $title;
        return $this;
    }

    public function value(string $value)
    {
        $this->value = $value;
        return $this;
    }

    public function required(bool $required = true)
    {
        $this->required = $required;
        return $this;
    }

    public function listData($listData)
    {
        $this->listData = $listData;
        return $this;
    }

    public function readonly(bool $readonly = true)
    {
        $this->readonly = $readonly;
        return $this;
    }

    public function placeholder(string $placeholder = '')
    {
        $this->placeholder = ($placeholder)? $placeholder : true;
        return $this;
    }

    public function htmlAttributes(string $htmlAttributes)
    {
        $this->htmlAttributes = $htmlAttributes;
        return $this;
    }

    public function params(array $params)
    {
        $this->params = $params;
        return $this;
    }
    //-------------------------------------------------------
    // Get input
    //-------------------------------------------------------
    public function get()
    {
        switch ($this->type) {
            case 'hidden':
                return '<input type="hidden" name="'.$this->name.'" value="'.$this->value.'">';
                break;

            case 'select':
                $htmInput = $this->inputSelect();
                break;

            case 'checkbox':
                $htmInput = WInputCheck::get($this->name, '&nbsp;', $this->value, $this->required);
                break;

            case 'textarea':
                $htmInput = $this->getInput('textarea');
                break;

            case 'datetime':
            case 'datetime-local':
                $htmInput = $this->inputDateTimeLocal();
                break;

            case 'number':
                $this->htmlAttributes .= 'style="width:initial"';
                $htmInput = $this->getInput('number');
                break;

            case 'percentage':
                $this->htmlAttributes .= ' min="0" max="100" step=".01" style="width:initial"';
                $this->title .= ' (%)';
                $htmInput = $this->getInput('number');
                break;

            case 'price':
                $this->htmlAttributes .= ' min="0" step=".01" style="width:initial"';
                $htmInput = $this->getInput('number');
                break;

            case 'url':
                $htmInput = $this->inputUrl();
                break;

            default: // others
                $htmInput = $this->getInput($this->type);
                break;
        }

        // If no title ---
        if ($this->title === false) {
            return $htmInput;
        }

        // Bootstrap container ---
        return self::inputContainer($this->title, $htmInput, $this->name);
    }
    //------------------------------------------------------------------
    public static function inputContainer(string $title, string $htmInput, string $name = ''): string
    {
        return '
        <div class="form-group" id="obj_'.$name.'">
           <label class="col-sm-3 control-label">'.$title.'</label>
           <div class="col-sm-9">'.$htmInput.'</div>
        </div>
        ';
    }
    //-------------------------------------------------------
    // Inputs
    //-------------------------------------------------------
    public function inputSelect(): string
    {
        if ($this->readonly) {
            $this->htmlAttributes .= ' disabled ';
            $this->name = '';
        }

        // Params ---
        $placeholder = '';
        if ($this->placeholder) {
            $placeholder = ($this->placeholder === true)? '-' : $this->placeholder;
        }

        return WInputSelect::getFromArray(
            $this->listData,
            $this->value,
            $this->name,
            $this->required,
            '',
            $placeholder
        );
    }

    public function inputUrl(): string
    {
        $this->htmlAttributes .= ' style="display:initial;width:95%" ';

        if ($this->value) {
            $this->title .= ' &nbsp;<a target="_blank" href="'.$this->value.'"><i class="fas fa-external-link-alt"></i></a>';
        }

        return $this->getInput($this->type);
    }

    public function inputDateTimeLocal()
    {
        // value ---
        if (is_integer($this->value)) {
            $this->value = self::timestampToDate($this->value, 'Y-m-d\TH:i', Login::$timezone);
        } else {
            $this->value = str_replace(" ", "T", $this->value);
        }

        return $this->getInput('datetime-local');
    }
    //------------------------------------------------------------------
    // Generic input
    //------------------------------------------------------------------
    public function getInput(string $type = 'text'): string
    {
        // Readonly ---
        if ($this->readonly) {
            $this->htmlAttributes .= ' disabled ';
            $this->name = '';
        }

        // Required ---
        if ($this->required) {
            $this->htmlAttributes .= ' required';
        }

        // Placeholder ---
        if ($this->placeholder === true) {
            $this->htmlAttributes .= ' placeholder="' . $this->title . '"';
        } else if ($this->placeholder) {
            $this->htmlAttributes .= ' placeholder="' . $this->placeholder . '"';
        }

        //-------------
        if ($type == 'textarea') {
            // Max lenght ---
            if (isset($this->params['maxlength'])) {
                $this->htmlAttributes .= ' maxlength="'.$this->params['maxlength'].'" ';
            }

            return '<textarea class="form-control type_'.$type.'"'.
                       ' ' . $this->htmlAttributes .
                       ' name="' . $this->name . '"'.
                    '>'.$this->value.'</textarea>';
        } else {
            return '<input class="form-control type_'.$type.'"'.
                       ' ' . $this->htmlAttributes .
                       ' type="'  . $type  . '"'.
                       ' name="'  . $this->name  . '"'.
                       ' value="' . $this->value . '"'.
                   '>';
        }
    }
    //---------------------------------------------------
    // Input datetime helpers
    //---------------------------------------------------
    private static function timestampToDate($timestamp, string $toFormat = 'Y-m-d\TH:i', $toTimezone = null)
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
