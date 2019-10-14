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
use angelrove\membrillo\WInputs\WInputRadios;
use angelrove\membrillo\WInputs\WInputTextarea;
use angelrove\membrillo\WInputs\WInputFile\WInputFile;

class FormInputs
{
    private $type;
    private $name;
    private $title;
    private $value;

    private $required = false;
    private $readOnly = false;
    private $placeholder = false;
    private $listData;
    private $params = [];
    private $htmlAttributes = '';

    // Valid types ---
    public const HIDDEN   = 'hidden';
    public const TEXT     = 'text';
    public const TEXTAREA = 'textarea';
    public const SELECT   = 'select';
    public const CHECKBOX = 'checkbox';
    public const RADIOS   = 'radios';
    public const FILE     = 'file';
    public const DATETIME = 'datetime';
    public const NUMBER   = 'number';
    public const PRICE    = 'price';
    public const PERCENTAGE = 'percentage';
    public const URL      = 'url';

    //-------------------------------------------------------
    /**
     * @param $type
     */
    public function __construct(string $type, string $name = '', string $title = '', string $value = '')
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
    public function title(string $title): FormInputs
    {
        $this->title = $title;
        return $this;
    }

    public function value(string $value): FormInputs
    {
        $this->value = $value;
        return $this;
    }

    public function required(bool $required = true): FormInputs
    {
        $this->required = $required;
        return $this;
    }

    public function listData($listData): FormInputs
    {
        $this->listData = $listData;
        return $this;
    }

    public function readOnly(bool $readOnly = true): FormInputs
    {
        $this->readOnly = $readOnly;
        return $this;
    }

    public function placeholder(string $placeholder = ''): FormInputs
    {
        $this->placeholder = ($placeholder)? $placeholder : true;
        return $this;
    }

    public function htmlAttributes(string $htmlAttributes): FormInputs
    {
        $this->htmlAttributes = $htmlAttributes;
        return $this;
    }

    public function params(array $params): FormInputs
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
            case self::HIDDEN:
                return '<input type="hidden" name="'.$this->name.'" value="'.$this->value.'">';
                break;

            case self::TEXTAREA:
                $htmInput = $this->getInput('textarea');
                break;

            case self::SELECT:
                $htmInput = (new WInputSelect($this->name, $this->listData, $this->value))
                    ->required($this->required)
                    ->placeholder($this->placeholder)
                    ->readOnly($this->readOnly)
                    ->html();
                break;

            case self::CHECKBOX:
                $htmInput = WInputCheck::get($this->name, '&nbsp;', $this->value, $this->required);
                break;

            case self::RADIOS:
                $htmInput = WInputRadios::get(
                    $this->name,
                    $this->listData,
                    $this->value,
                    $this->required
                );
                break;

            case self::FILE:
                $input = new WInputFile($this->name, $this->value);
                $htmInput = $input->set_required($this->required)
                             ->setReadOnly($this->readOnly)
                             ->get();
                break;

            case self::DATETIME:
                $htmInput = $this->inputDateTimeLocal();
                break;

            case self::NUMBER:
                $this->htmlAttributes .= 'style="width:initial"';
                $htmInput = $this->getInput('number');
                break;

            case self::PERCENTAGE:
                $this->htmlAttributes .= ' min="0" max="100" step=".01" style="width:initial"';
                $this->title .= ' (%)';
                $htmInput = $this->getInput('number');
                break;

            case self::PRICE:
                $this->htmlAttributes .= ' min="0" step=".01" style="width:initial"';
                $htmInput = $this->getInput('number');
                break;

            case self::URL:
                $htmInput = $this->inputUrl();
                break;

            default: // others
                $htmInput = $this->getInput($this->type);
                break;
        }

        // If no title ---
        if ($this->title === false) {
            return $htmInput;
        } else {
            // With Bootstrap container ---
            return self::inputContainer($this->title, $htmInput, $this->name);
        }

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
    private function inputUrl(): string
    {
        $this->htmlAttributes .= ' style="display:initial;width:95%" ';

        if ($this->value) {
            $this->title .= ' &nbsp;<a target="_blank" href="'.$this->value.'"><i class="fas fa-external-link-alt"></i></a>';
        }

        return $this->getInput($this->type);
    }
    //-------------------------------------------------------
    private function inputDateTimeLocal()
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
    private function getInput(string $type = 'text'): string
    {
        // ReadOnly ---
        if ($this->readOnly) {
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
