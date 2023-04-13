<?php

namespace icalc\fe\displayTypes;

class Number extends DisplayType
{

    private $id;
    private $label;
    private $name;
    private $min;
    private $max;
    private $step;
    private $value;


    public function __construct($id, $label, $name = null, $min = null, $max = null, $step = null, $value = null)
    {
        $this->id = $id;
        $this->label = $label;
        $this->name = $name;
        $this->min = $min;
        $this->max = $max;
        $this->step = $step;
        $this->value = $value;

    }

    function render() :string
    {
        $returnValue = '<input class="icalc-number-input" type="number" id="' . $this->id . '"';

        if (!is_null($this->name)) {
            $returnValue = $returnValue . 'name="' . $this->name . '"';
        }

        if (!is_null($this->min) && !is_null($this->max) && $this->max > $this->min) {
            $returnValue = $returnValue . 'min="' . $this->min . '" max="' . $this->max . '"';
        } else {
            $returnValue = $returnValue . 'min="' . 0 . '" max="' . 100 . '"';
            $this->min = 0;
            $this->max = 100;
        }

        if (!is_null($this->step) && $this->step < $this->max) {
            $returnValue = $returnValue . 'step="' . $this->step . '"';
        }

        if (!is_null($this->value) && $this->min <= $this->value && $this->value <= $this->max) {
            $returnValue = $returnValue . 'value="' . $this->value . '"';
        } else {
            $returnValue = $returnValue . 'value="' . $this->min . '"';
        }

        $returnValue = $returnValue . '/>';

        return $returnValue;
    }
}