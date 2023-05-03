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

use icalc\fe\Component;
use PHPUnit\Framework\TestCase;

class ComponentTest extends TestCase
{

    private function getConfiguration()
    {
        return json_decode("{
            'show-label': true,
            'label-classes': '',
            'custom-label': '',
            'base-value': '1',
            'input-classes': '',
        }");

    }

    public function testCreateComponentRenderer()
    {
        // Assuming the necessary dependencies are mocked or autoloaded
        $component = new Component(1, 'genericComponent', 'component-1', 'number', null, $this->getConfiguration());

        $this->assertNotNull($component->get_type());
        $this->assertEquals('genericComponent', $component->get_type());
    }

    public function testRender()
    {
        // Assuming the necessary dependencies are mocked or autoloaded
        $component = new Component(1, 'genericComponent', 'component-1', 'number', null, $this->getConfiguration());

        // This test may need to be adjusted depending on the render() method implementation in the componentRenderer
        $this->assertNotEmpty($component->render());
    }

    public function testGetDisplayType()
    {
        $component = new Component(1, 'genericComponent', 'component-1', 'number', null, $this->getConfiguration());
        $this->assertEquals('number', $component->get_display_type());
    }

    public function testGetDomId()
    {
        $component = new Component(1, 'genericComponent', 'component-1', 'number', null, $this->getConfiguration());
        $this->assertEquals('component-1', $component->get_dom_id());
    }

    public function testGetBaseValue()
    {
        // Assuming the necessary dependencies are mocked or autoloaded

        $configuration = $this->getConfiguration();
        $configuration->{'base-value'} = 10;

        $component = new Component(1, 'genericComponent', 'component-1', 'number', null, (object)$configuration);

        $this->assertEquals(10, $component->get_base_value());
    }

    public function testToString()
    {
        // Assuming the necessary dependencies are mocked or autoloaded
        $component = new Component(1, 'genericComponent', 'component-1', 'number', null, $this->getConfiguration());

        $this->assertNotEmpty((string)$component);
    }
}
