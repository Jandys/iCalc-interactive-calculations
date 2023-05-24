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

class Sum extends DisplayType
{

    protected $id;
    protected $labelClasses;
    protected $label;

    public function render(): string
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

    }

    protected function showLabel()
    {
        if ($this->displayLabel) {
            $label = new Label();
            $label->showLabel($this->id, $this->labelClasses, $this->label);

            return $label->render();
        }

        return "";
    }

    protected function displayInput()
    {
        return '<input id="' . $this->id . '" type="text" disabled class="form-control intercalcus-calculation-sum">';
    }
}