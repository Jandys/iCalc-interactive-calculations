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

namespace interactivecalculations\fe;

use PHPUnit\Framework\TestCase;

class ScriptWrapperTest extends TestCase
{
    public function testAddToContent()
    {
        $scriptWrapper = new ScriptWrapper();
        $scriptWrapper->addToContent('console.log("Hello World!");');

        $this->assertFalse($scriptWrapper->isEmpty());
    }

    public function testIsEmpty()
    {
        $scriptWrapper = new ScriptWrapper();
        $this->assertTrue($scriptWrapper->isEmpty());
    }

    public function testGetScripts()
    {
        $scriptWrapper = new ScriptWrapper();
        $scriptWrapper->addToContent('console.log("Hello World!");');

        $expectedScript = '<script>window.addEventListener(\'load\', function () {console.log("Hello World!");});</script>';
        $this->assertEquals($expectedScript, $scriptWrapper->getScripts());
    }

    public function testWrapWithOnLoad()
    {
        $scriptWrapper = new ScriptWrapper();
        $scriptWrapper->addToContent('console.log("Hello World!");');
        $scriptWrapper->wrapWithOnLoad(false);

        $expectedScript = '<script>console.log("Hello World!");</script>';
        $this->assertEquals($expectedScript, $scriptWrapper->getScripts());
    }

    public function testWrapWithScrip()
    {
        $scriptWrapper = new ScriptWrapper();
        $scriptWrapper->addToContent('console.log("Hello World!");');
        $scriptWrapper->wrapWithScrip(false);

        $expectedScript = 'window.addEventListener(\'load\', function () {console.log("Hello World!");});';
        $this->assertEquals($expectedScript, $scriptWrapper->getScripts());
    }
}
