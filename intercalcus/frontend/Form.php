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

namespace intercalcus\fe;

/**
 * Class Form
 *
 * @package intercalcus\fe
 * @since 1.0.0
 */
class Form
{

    private array $components = array();

    public function __construct()
    {
    }

    /**
     * Adds a component to the form.
     *
     * @param mixed $component The component to add.
     *
     * @return void
     */
    public function addComponent($component)
    {
        $id = $component->id;
        $type = $component->type;
        $domId = $component->domId;
        $displayType = $component->displayType;
        $parentComponent = $component->parentComponent;
        $configuration = $component->conf;


        $componentObject = new Component($id, $type, $domId, $displayType, $parentComponent, $configuration);
        $this->components[$domId] = $componentObject;
    }

    /**
     * Renders the form and its components.
     *
     * @return string The rendered form and its components.
     */
    public function render(): string
    {
        $formRendering = "<form>";

        foreach ($this->components as $component) {
            if ($component != null) {
                $formRendering = $formRendering . $component->render() . "   ";
            }
        }


        return $formRendering . "</form>";
    }

    /**
     * Checks if the form has a component of a given type.
     *
     * @param string $what The type of component to check for.
     *
     * @return bool True if the form has a component of the given type, false otherwise.
     */
    public function has($what): bool
    {
        foreach ($this->components as $component) {
            $type = $component->get_display_type();
            if (strtolower(trim($type)) == strtolower(trim($what))) {
                return true;
            }
        }
        return false;
    }

    /**
     * Gets all components of the form.
     *
     * @return array The components of the form.
     */
    public function get_components(): array
    {
        return $this->components;
    }
}