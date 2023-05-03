<?php


use PHPUnit\Framework\TestCase;

class CheckBoxTest extends TestCase
{


    public function testFillDataEmptyFill()
    {
        $check_box = new icalc\fe\displayTypes\CheckBox();
        $this->expectException(TypeError::class);
        $check_box->fillData("");
    }

    public function testIsTypeCheckbox()
    {
        $checkBox = new icalc\fe\displayTypes\CheckBox();
        $checkBox->fillData([
            'id' => 'test_checkbox',
            'masterObject' => null,
            'conf' => (object)[
                'configuration' => (object)[
                    'show-label' => true,
                    'label-classes' => 'label-class',
                    'custom-label' => 'Test Label',
                    'base-value' => 10.0,
                    'input-classes' => 'input-class'
                ]
            ]
        ]);

        $html = $checkBox->render();

        $this->assertStringContainsString('type="checkbox"', $html);
    }

    public function testRenderWithLabel()
    {
        $checkBox = new icalc\fe\displayTypes\CheckBox();
        $checkBox->fillData([
            'id' => 'test_checkbox',
            'masterObject' => null,
            'conf' => (object)[
                'configuration' => (object)[
                    'show-label' => true,
                    'label-classes' => 'label-class',
                    'custom-label' => 'Test Label',
                    'base-value' => 10.0,
                    'input-classes' => 'input-class'
                ]
            ]
        ]);

        $html = $checkBox->render();

        $this->assertStringContainsString('<label', $html);
        $this->assertStringContainsString('Test Label', $html);
    }

    public function testRenderWithoutLabel()
    {
        $checkBox = new icalc\fe\displayTypes\CheckBox();
        $checkBox->fillData([
            'id' => 'test_checkbox',
            'masterObject' => null,
            'conf' => (object)[
                'configuration' => (object)[
                    'show-label' => false,
                    'label-classes' => 'label-class',
                    'custom-label' => 'Test Label',
                    'base-value' => 10.0,
                    'input-classes' => 'input-class'
                ]
            ]
        ]);

        $html = $checkBox->render();

        $this->assertStringNotContainsString('<label', $html);
    }

    public function testFillDataWithMasterObject()
    {
        $masterObject = (object)[
            'name' => 'Master Object Label',
            'price' => 20.0
        ];

        $checkBox = new icalc\fe\displayTypes\CheckBox();
        $checkBox->fillData([
            'id' => 'test_checkbox',
            'masterObject' => $masterObject,
            'conf' => (object)[
                'configuration' => (object)[
                    'show-label' => true,
                    'label-classes' => 'label-class',
                    'custom-label' => 'Custom Label',
                    'base-value' => 10.0,
                    'input-classes' => 'input-class'
                ]
            ]
        ]);

        $html = $checkBox->render();

        $this->assertStringContainsString('Master Object Label', $html);
    }

    public function testFillDataWithCustomConfiguration()
    {
        $checkBox = new icalc\fe\displayTypes\CheckBox();
        $checkBox->fillData([
            'id' => 'test_checkbox',
            'masterObject' => null,
            'conf' => (object)[
                'configuration' => (object)[
                    'show-label' => true,
                    'label-classes' => 'label-class',
                    'custom-label' => 'Custom Label',
                    'base-value' => 10.0,
                    'input-classes' => 'input-class'
                ]
            ]
        ]);

        $html = $checkBox->render();

        $this->assertStringContainsString('Custom Label', $html);
    }
}
