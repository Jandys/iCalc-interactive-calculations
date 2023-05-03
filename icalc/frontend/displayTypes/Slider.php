<?php
/*
 *
 *   This file is part of the 'iCalc - Interactive Calculations' project.
 *
 *   Copyright (C) 2023, Jakub Jandák
 *
 *   This program is free software: you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation, either version 3 of the License, or
 *   (at your option) any later version.
 *
 *   This program is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with this program. If not, see <https://www.gnu.org/licenses/>.
 *
 *
 */

namespace icalc\fe\displayTypes;

class Slider extends DisplayType
{
    private $id;
    private $name;
    private $max;
    private $value;
    private $classes;
    private $displayLabel = false;
    private $showValue = false;
    private $label;
    private $unit;
    private $labelClasses;


    public function __construct()
    {
    }

    function render(): string
    {
        $wrapper = '<div class="icalc-form-group form-outline form-group row">';

        $wrapper = $wrapper . $this->showLabel();

        $wrapper = $wrapper . $this->displayInput();

        $wrapper = $wrapper . $this->showValue();

        $wrapper = $wrapper . '</div>';

        return $wrapper;
    }

    public function fillData($args): void
    {
        $id = $args["id"];
        $masterObject = $args['masterObject'];
        $conf = $args['conf'];

        $this->id = $id;
        $this->displayLabel = boolval($conf->configuration->{'slider-show-value'});
        $this->showValue = boolval($conf->configuration->{'slider-show-value'});
        $this->labelClasses = $conf->configuration->{'label-classes'};
        if ($masterObject == null) {
            $this->unit = "";
            $this->label = $conf->configuration->{'custom-label'};

        } else {
            $this->label = $masterObject->name;
            $this->unit = $masterObject->unit;
        }

        $this->classes = str_replace(";", " ", $conf->configuration->{'input-classes'});
        $this->name = $id;
        $this->max = $conf->configuration->{'slider-max'};
        $this->value = 0;
        $this->min = 0;

    }

    private function showLabel(): string
    {
        if ($this->displayLabel) {
            $label = new Label();
            $label->showLabel($this->id, $this->labelClasses, $this->label);

            return $label->render();
        }

        return "";
    }

    private function showValue(): string
    {
        if ($this->showValue) {
            return '<div id="displayValue-' . $this->id . '" class="icalc-display-slider-value">' . $this->value . '</div>';
        }

        return "";
    }

    /**
     * @return bool
     */
    public function is_show_value(): bool
    {
        return $this->showValue;
    }


    private function displayInput(): string
    {
        $returnValue = '<input class="icalc-calculation-slider ' . $this->classes . '" type="range" id="' . $this->id . '"';

        if (!is_null($this->name)) {
            $returnValue = $returnValue . 'name="' . $this->name . '"';
        }

        if (!is_null($this->min)) {
            $returnValue = $returnValue . 'min="' . $this->min . '"';
        }
        if (!is_null($this->max) && !is_null($this->min) && $this->max > $this->min) {
            $returnValue = $returnValue . ' max="' . $this->max . '"';
        }

        if (is_null($this->max)) {
            $this->max = 100;
        }

        if (!is_null($this->value) && $this->min <= $this->value && $this->value <= $this->max) {
            $returnValue = $returnValue . 'value="' . $this->value . '"';
        } else {
            $returnValue = $returnValue . 'value="' . $this->min . '"';
        }

        return $returnValue . '/>';
    }
}