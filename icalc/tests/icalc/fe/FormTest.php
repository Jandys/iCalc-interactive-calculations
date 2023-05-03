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
use icalc\fe\Form;
use PHPUnit\Framework\TestCase;

class FormTest extends TestCase
{
    public function testAddComponent()
    {
        $form = new Form();
        $componentData = [
            'id' => 1,
            'type' => 'genericComponent',
            'domId' => 'component-1',
            'displayType' => 'text',
            'parentComponent' => null,
            'conf' => json_decode("{
                                    'show-label': true,
                                    'label-classes': '',
                                    'custom-label': '',
                                    'base-value': '1',
                                    'input-classes': '',
                                }")
        ];

        $form->addComponent((object)$componentData);
        $components = $form->get_components();

        $this->assertCount(1, $components);
        $this->assertInstanceOf(Component::class, $components['component-1']);
    }

    public function testRender()
    {
        $form = new Form();
        $componentData = [
            'id' => 1,
            'type' => 'genericComponent',
            'domId' => 'component-1',
            'displayType' => 'label',
            'parentComponent' => null,
            'conf' => []
        ];

        $form->addComponent((object)$componentData);

        $output = $form->render();
        $this->assertStringContainsString("<form>", $output);
        $this->assertStringContainsString("</form>", $output);
    }

    public function testHas()
    {
        $form = new Form();
        $componentData = [
            'id' => 1,
            'type' => 'genericComponent',
            'domId' => 'component-1',
            'displayType' => 'checkbox',
            'parentComponent' => null,
            'conf' => []
        ];

        $form->addComponent((object)$componentData);

        $this->assertTrue($form->has('checkbox'));
        $this->assertFalse($form->has('genericComponent'));
    }

    public function testGetComponents()
    {
        $form = new Form();
        $componentData1 = [
            'id' => 1,
            'type' => 'genericComponent',
            'domId' => 'component-1',
            'displayType' => 'number',
            'parentComponent' => null,
            'conf' => []
        ];

        $componentData2 = [
            'id' => 2,
            'type' => 'genericComponent',
            'domId' => 'component-2',
            'displayType' => 'textarea',
            'parentComponent' => null,
            'conf' => []
        ];

        $form->addComponent((object)$componentData1);
        $form->addComponent((object)$componentData2);

        $components = $form->get_components();
        $this->assertCount(2, $components);
        $this->assertInstanceOf(Component::class, $components['component-1']);
        $this->assertInstanceOf(Component::class, $components['component-2']);
    }
}
