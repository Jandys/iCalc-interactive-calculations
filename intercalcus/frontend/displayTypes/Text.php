<?php
/*
 *
 *   This file is part of the 'Inter Calcus' project.
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

namespace intercalcus\fe\displayTypes;

class Text extends DisplayType
{
    private $id;
    private $name;
    private $classes;
    private $displayLabel = false;
    private $label;
    private $labelClasses;

    public function __construct()
    {
    }

    function render(): string
    {
        $wrapper = '<div class="intercalcus-form-group form-outline form-group row">';

        $wrapper = $wrapper . $this->showLabel();

        $wrapper = $wrapper . $this->displayInput();

        $wrapper = $wrapper . '</div>';

        return $wrapper;
    }

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
        } else {
            $this->label = $masterObject->name;
        }
        $this->classes = str_replace(";", " ", $conf->configuration->{'input-classes'});
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


    private function displayInput(): string
    {
        $returnValue = '<input class="intercalcus-calculation-text-input ' . $this->classes . '" type="text" id="' . $this->id . '"';

        if (!is_null($this->name)) {
            $returnValue = $returnValue . 'name="' . $this->name . '"';
        }

        return $returnValue . '/>';
    }

}