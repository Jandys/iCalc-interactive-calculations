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
 * The ScriptWrapper class is a helper class that provides a way to build a JavaScript script that can be added to an HTML page.
 */
class ScriptWrapper
{


    private string $contents = "";
    private bool $wrapWithOnLoad = true;
    private bool $wrapWithScript = true;

    public function __construct()
    {
    }


    /**
     * Generates the script based on the current configuration of the ScriptWrapper instance.
     *
     * @return string The generated script.
     */
    public function getScripts(): string
    {
        $retVal = "";
        if ($this->wrapWithScript) {
            $retVal = $retVal . '<script>';
        }

        if ($this->wrapWithOnLoad) {
            $retVal = $retVal . 'window.addEventListener(\'load\', function () {';
        }

        $retVal = $retVal . $this->contents;

        if ($this->wrapWithOnLoad) {
            $retVal = $retVal . '});';
        }

        if ($this->wrapWithScript) {
            $retVal = $retVal . '</script>';
        }

        return $retVal;
    }

    /**
     * Checks whether the ScriptWrapper instance is empty.
     *
     * @return bool True if the ScriptWrapper instance is empty, false otherwise.
     */
    public function isEmpty(): bool
    {
        return empty($this->contents);
    }


    /**
     * Adds content to the script.
     *
     * @param string $addition The content to add.
     */
    public function addToContent($addition): void
    {
        $this->contents = $this->contents . $addition;
    }

    /**
     * Enables or disables wrapping the script with an onload event.
     *
     * @param bool $doWrapWithOnLoad True to wrap the script with an onload event, false otherwise.
     */
    public function wrapWithOnLoad($doWrapWithOnLoad = true): void
    {
        $this->wrapWithOnLoad = $doWrapWithOnLoad;
    }

    /**
     * Enables or disables wrapping the script with a script tags.
     *
     * @param bool $doWrapWithScript True to wrap the script with a script tags, false otherwise.
     */
    public function wrapWithScrip($doWrapWithScript = true): void
    {
        $this->wrapWithScript = $doWrapWithScript;
    }
}