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

    private function getComponentData()
    {
        $configuration = new stdClass();
        $configuration->{'show-label'} = true;
        $configuration->{'label-classes'} = '';
        $configuration->{'custom-label'} = '';
        $configuration->{'base-value'} = 1;
        $configuration->{'input-classes'} = '';
        $componentData = [
            'id' => 1,
            'type' => 'genericComponent',
            'domId' => 'component-1',
            'displayType' => 'label',
            'parentComponent' => null,
            'conf' => json_encode($configuration)
        ];
        return $componentData;
    }

    public function testAddComponent()
    {
        $form = new Form();

        $form->addComponent((object)$this->getComponentData());
        $components = $form->get_components();

        $this->assertCount(1, $components);
        $this->assertInstanceOf(Component::class, $components['component-1']);
    }

    public function testRender()
    {
        $form = new Form();

        $form->addComponent((object)$this->getComponentData());

        $output = $form->render();
        $this->assertStringContainsString("<form>", $output);
        $this->assertStringContainsString("</form>", $output);
    }

    public function testHas()
    {
        $form = new Form();
        $componentData = $this->getComponentData();
        $componentData["displayType"] = "checkbox";

        $form->addComponent((object)$componentData);

        $this->assertTrue($form->has('checkbox'));
        $this->assertFalse($form->has('genericComponent'));
    }

    public function testGetComponents()
    {
        $form = new Form();


        $componentData1 = $this->getComponentData();
        $componentData1["domId"] = 'component-1';
        $componentData2 = $this->getComponentData();
        $componentData2["domId"] = 'component-2';

        $form->addComponent((object)$componentData1);
        $form->addComponent((object)$componentData2);

        $components = $form->get_components();
        $this->assertCount(2, $components);
        $this->assertInstanceOf(Component::class, $components['component-1']);
        $this->assertInstanceOf(Component::class, $components['component-2']);
    }
}
