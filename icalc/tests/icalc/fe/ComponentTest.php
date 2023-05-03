<?php

use icalc\fe\Component;
use PHPUnit\Framework\TestCase;

class ComponentTest extends TestCase
{
    public function testCreateComponentRenderer()
    {
        // Assuming the necessary dependencies are mocked or autoloaded
        $component = new Component(1, 'genericComponent', 'component-1', 'number', null, []);

        $this->assertNotNull($component->get_type());
        $this->assertEquals('genericComponent', $component->get_type());
    }

    public function testRender()
    {
        // Assuming the necessary dependencies are mocked or autoloaded
        $component = new Component(1, 'genericComponent', 'component-1', 'number', null, []);

        // This test may need to be adjusted depending on the render() method implementation in the componentRenderer
        $this->assertNotEmpty($component->render());
    }

    public function testGetDisplayType()
    {
        $component = new Component(1, 'genericComponent', 'component-1', 'number', null, []);
        $this->assertEquals('number', $component->get_display_type());
    }

    public function testGetDomId()
    {
        $component = new Component(1, 'genericComponent', 'component-1', 'number', null, []);
        $this->assertEquals('component-1', $component->get_dom_id());
    }

    public function testGetBaseValue()
    {
        // Assuming the necessary dependencies are mocked or autoloaded
        $configuration = json_decode("{\"configuration\": {\"base-value\":10}}");
        $component = new Component(1, 'genericComponent', 'component-1', 'number', null, (object) $configuration);

        $this->assertEquals(10, $component->get_base_value());
    }

    public function testToString()
    {
        // Assuming the necessary dependencies are mocked or autoloaded
        $component = new Component(1, 'genericComponent', 'component-1', 'number', null, []);

        $this->assertNotEmpty((string) $component);
    }
}
