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

class Spacer extends DisplayType
{

    private $classes;

    public function render(): string
    {
        return '<div class=" ' . $this->classes . '" ></div>';
    }

    public function fillData($args): void
    {
        $id = $args["id"];
        $masterObject = $args['masterObject'];
        $conf = $args['conf'];


        $this->classes = str_replace(";", " ", $conf->configuration->{'input-classes'});

    }
}