<?php
/*
 *
 *   This file is part of the 'iCalcus - Interactive Calculations' project.
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

namespace icalcus\fe\displayTypes;

/**
 * Class CheckBox
 *
 * A class that represents a checkbox display type.
 *
 * @package icalcus\fe\displayTypes
 * @since 1.0.0
 */
class CheckBox extends DisplayType
{

    private $id;
    private $name;
    private float $value;
    private $classes;
    private $displayLabel = false;
    private $label;
    private $labelClasses;

    public function __construct()
    {
    }

    /**
     * Renders the checkbox.
     * Wrapped in generic div.
     *
     * @return string The rendered HTML for the checkbox.
     */
    function render(): string
    {
        $wrapper = '<div class="icalcus-form-group form-outline form-group row">';

        $wrapper = $wrapper . $this->showLabel();

        $wrapper = $wrapper . $this->displayInput();

        $wrapper = $wrapper . '</div>';

        return $wrapper;
    }

    /**
     * Renders the checkbox.
     *
     * @return string The rendered HTML for the checkbox.
     */
    public function fillData($args): void
    {
        $id = $args["id"];
        $masterObject = $args['masterObject'];
        $conf = $args['conf'];

        $this->id = $id;
        $this->displayLabel = boolval($conf->configuration->{'show-label'});
        $this->labelClasses = $conf->configuration->{'label-classes'};
        if ($masterObject == null) {
            $this->label = $conf->configuration->{'custom-label'};
            $this->value = floatval($conf->configuration->{'base-value'});
        } else {
            $this->label = $masterObject->name;
            $this->value = floatval($masterObject->price);
        }

        $this->classes = str_replace(";", " ", $conf->configuration->{'input-classes'});
    }

    /**
     * Displays the label associated with the checkbox.
     *
     * @return string The rendered HTML for the label.
     */
    private function showLabel(): string
    {
        if ($this->displayLabel) {
            $label = new Label();
            $label->showLabel($this->id, $this->labelClasses, $this->label);

            return $label->render();
        }

        return "";
    }

    /**
     * @return string
     */
    private function displayInput(): string
    {
        $returnValue = '<input class="icalcus-calculation-checkbox-input ' . $this->classes . '" type="checkbox" id="' . $this->id . '" 
		data-value="' . $this->value . '"';

        if (!is_null($this->name)) {
            $returnValue = $returnValue . 'name="' . $this->name . '"';
        }

        return $returnValue . '/>';
    }

}