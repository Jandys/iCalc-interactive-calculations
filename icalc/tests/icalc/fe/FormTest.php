<?php

use icalc\fe\Form;
use icalc\fe\Component;
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
            'conf' => []
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
        $this->assertStringContainsString("<form>",$output);
        $this->assertStringContainsString("</form>",$output);
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
