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

/**
 * Abstract base class for all display types.
 */
abstract class DisplayType
{
    /**
     * Returns the name of the display type class.
     *
     * @return string The name of the display type class.
     * @since 1.0.0
     */
    protected function getDisplayType()
    {
        return explode('\\', strtolower(get_called_class()));
    }

    /**
     * Renders the HTML for the display type.
     *
     * @return string The HTML for the display type.
     * @since 1.0.0
     */
    abstract public function render(): string;

    /**
     * Fills the display type object with data from the arguments.
     *
     * @param array $args An array of arguments used to fill the display type object.
     * @return void
     * @since 1.0.0
     */
    abstract public function fillData($args): void;
}