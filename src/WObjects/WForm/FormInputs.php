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
use angelrove\membrillo\WInputs\WInputDatetime;
use angelrove\membrillo\WInputs\WInputFile\WInputFile;

class FormInputs
{
    // general params ---
    private $type;
    private $name;
    private $title;
    private $value;
    private $required = false;
    private $readOnly = false;
    private $placeholder = false;

    // particular params ---
    private $listData; // selectors
    private $timezone; // datetime

    // Other ----
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
    public const MONTH    = 'month';
    public const NUMBER   = 'number';
    public const PRICE    = 'price';
    public const PERCENT  = 'percent';
    public const URL      = 'url';

    //-------------------------------------------------------
    /**
     * @param $type
     */
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
    public function title($title): FormInputs
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
    //------------------------------------------------
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
    //------------------------------------------------
    // Select, Radios
    public function listData($listData): FormInputs
    {
        $this->listData = $listData;
        return $this;
    }
    // Timezone
    public function timezone($timezone): FormInputs
    {
        $this->timezone = $timezone;
        return $this;
    }
    //-------------------------------------------------------
    // Get input
    //-------------------------------------------------------
    public function get(bool $withContainer = true)
    {
        switch ($this->type) {
            case self::HIDDEN:
                return '<input type="hidden" name="'.$this->name.'" value="'.$this->value.'">';
                break;

            case self::TEXTAREA:
                $htmInput = $this->getInput('textarea', $this->value);
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
                $htmInput = (new WInputDatetime($this->name, $this->value))
                    ->timezone($this->timezone)
                    ->readOnly($this->readOnly)
                    ->required($this->required)
                    ->get();
                break;

            case self::MONTH:
                $htmInput = $this->getInput('month', $this->value);
                break;

            case self::NUMBER:
                $this->htmlAttributes .= 'style="width:initial"';
                $htmInput = $this->getInput('number', $this->value);
                break;

            case self::PERCENT:
                $this->htmlAttributes .= ' min="0" max="100" step=".01" style="width:initial"';
                $this->title .= ' (%)';
                $htmInput = $this->getInput('number', $this->value);
                break;

            case self::PRICE:
                $this->htmlAttributes .= ' min="0" step=".01" style="width:initial"';
                $htmInput = $this->getInput('number', $this->value);
                break;

            case self::URL:
                $htmInput = $this->inputUrl();
                break;

            default: // others
                $htmInput = $this->getInput($this->type, $this->value);
                break;
        }

        // If no title ---
        if (!$withContainer || $this->title === false) {
            return $htmInput;
        } else {
            // With Bootstrap container ---
            return self::inputContainer($this->title, $htmInput, $this->name);
        }
    }
    //------------------------------------------------------------------
    public static function inputContainer(string $title, string $htmInput, string $name = ''): string
    {
        return self::inputContainer_start($title, $name).
                  $htmInput.
               self::inputContainer_end();
    }
    //------------------------------------------------------------------
    public static function inputContainer_start(string $title, string $name = ''): string
    {
        return '
        <div class="form-group" id="obj_'.$name.'">
           <label class="col-sm-3 control-label">'.$title.'</label>
           <div class="col-sm-9">
        ';
    }
    //------------------------------------------------------------------
    public static function inputContainer_end(): string
    {
        return '
           </div>
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

        return $this->getInput($this->type, $this->value);
    }
    //------------------------------------------------------------------
    // Generic input
    //------------------------------------------------------------------
    private function getInput(string $type, $value): string
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
                    '>'.$value.'</textarea>';
        } else {
            return '<input class="form-control type_'.$type.'"'.
                       ' ' . $this->htmlAttributes .
                       ' type="'  . $type  . '"'.
                       ' name="'  . $this->name  . '"'.
                       ' value="' . $value . '"'.
                   '>';
        }
    }
    //------------------------------------------------------------------
}
