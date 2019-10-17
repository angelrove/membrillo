<?php
/**
 *
 */

namespace angelrove\membrillo\WInputs;

class WInputDatetime
{
    private $name;
    private $value;
    private $timezone;
    private $required;
    private $readOnly;

    //------------------------------------------------------------------
    public function __construct($name, $value)
    {
        $this->name = $name;
        $this->value = $value;
    }
    //------------------------------------------------------------------
    public function timezone($timezone)
    {
        $this->timezone = $timezone;
        return $this;
    }
    //------------------------------------------------------------------
    public function readOnly(bool $readOnly = true)
    {
        $this->readOnly = $readOnly;
        return $this;
    }
    //------------------------------------------------------------------
    public function required(bool $required = true)
    {
        $this->required = $required;
        return $this;
    }
    //------------------------------------------------------------------
    public function get()
    {
        $htmlAttributes = '';

        // Value ---
        $this->value = self::parseValue($this->value, $this->timezone);

        // ReadOnly ---
        if ($this->readOnly) {
            $htmlAttributes .= ' disabled ';
            $this->name = '';
        }

        // Required ---
        if ($this->required) {
            $htmlAttributes .= ' required';
        }

        //------
        return '<input class="form-control type_datetime"
                       style="width: initial"
                       type="datetime-local"
                       name="'.$this->name.'"
                       value="'.$this->value.'"
                       '.$htmlAttributes.'
                       >';
    }
    //------------------------------------------------------------------
    /**
     * @param $value mixed [int timestamp, Carbon object, string datetime]
     */
    private static function parseValue($value, string $timezone = null): string
    {
        if ($value instanceof \Illuminate\Support\Carbon) {
        } else {
            $value = \Carbon::parse($value);
        }

        return $value->timezone($timezone)->format('Y-m-d\TH:i');
    }
    //---------------------------------------------------
    // Helper to DB insert
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
        // 2018-01-01 22:02:00 -------
        elseif ($date = \DateTime::createFromFormat('Y-m-d H:i:s', $dateTime)) {
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
