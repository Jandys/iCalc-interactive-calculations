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

namespace interactivecalculations\fe\displayTypes;

class Label extends DisplayType
{

    private $forId;
    private $label;
    private $class;

    public function __construct()
    {
    }

    public function showLabel($forId, $classes, $label): Label
    {
        $this->forId = $forId;
        $this->class = $classes;
        $this->label = $label;
        return $this;
    }

    public function render(): string
    {
        $for = "";
        if ($this->forId) {
            $for = 'for="' . $this->forId . '"';
        }

        return '<label class="interactivecalculations-calculation-label ' . $this->class . '" ' . $for . '>' . $this->label . '</label>';
    }

    //$args=array('id'=>$id,'conf'=>$configuration,'masterObject'=>$masterObject);
    public function fillData($args): void
    {
        $masterObject = $args['masterObject'];
        $conf = $args['conf'];

        if ($masterObject == null) {
            $this->label = $conf->configuration->{'custom-label'};
        } else {
            $this->label = $masterObject->name;
            $this->forId = $args['id'];
        }

        $this->class = str_replace(";", " ", $conf->configuration->{'label-classes'});
    }
}