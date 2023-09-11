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

class ChooseList extends DisplayType
{

    private $id;
    private $name;
    private $options = [];
    private $is_multiple;
    private $default;
    private $displayLabel;
    private $labelClasses;
    private $label;
    private $classes;


    public function __construct()
    {
    }

    public function directConfiguration($id, $name, $class, $options, $default = null, $is_multiple = false)
    {
        $this->id = $id;
        $this->name = $name;
        $this->class = $class;
        $this->options = $options;
        $this->is_multiple = $is_multiple;
        $this->default = $default;
    }


    function render(): string
    {
        $multiple = $this->is_multiple ? ' multiple ' : ' ';
        $select = '<select name="' . $this->name . '" id="' . $this->id . '"  class="' . $this->classes . '" ' . $multiple . '>';

        if (!empty($this->options)) {
            foreach ($this->options as $option) {
                $selected = '';
                if (isset($option["value"]) && isset($option["name"])) {
                    if ($this->default != null && ($option["name"] == $this->default || $option["value"] == $this->default)) {
                        $selected = ' selected ';
                    }
                    $select = $select . ' <option value="' . $option["value"] . '"' . $selected . '>' . $option["name"] . '</option>';
                } else {
                    if ($this->default != null && $option == $this->default) {
                        $selected = ' selected ';
                    }
                    $select = $select . ' <option value="' . $option . '"' . $selected . '>' . $option . '</option>';
                }
            }
        }

        return $select . '</select>';

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
        $this->name = $id;

        $newOptions = [];

        foreach ($conf->configuration as $key => $value) {

            if (str_contains($key, "list")) {
                $listId = -1;
                if (preg_match('/\d+/', $key, $matches)) {
                    $listId = $matches[0];
                }

                if (!isset($newOptions[$listId])) {
                    $newOptions[$listId] = [];
                }


                if (str_contains($key, "list-value")) {
                    $newOptions[$listId]["value"] = $value;
                } elseif (str_contains($key, "list-option")) {
                    $newOptions[$listId]["name"] = $value;
                }
            }
        }

        foreach ($newOptions as $option) {
            $this->options[] = [
                "name" => $option["name"],
                "value" => $option["value"],
            ];
        }
    }
}