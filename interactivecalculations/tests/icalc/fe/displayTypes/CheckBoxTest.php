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

use PHPUnit\Framework\TestCase;

class CheckBoxTest extends TestCase
{


    public function testFillDataEmptyFill()
    {
        $check_box = new interactivecalculations\fe\displayTypes\CheckBox();
        $this->expectException(TypeError::class);
        $check_box->fillData("");
    }

    public function testIsTypeCheckbox()
    {
        $checkBox = new interactivecalculations\fe\displayTypes\CheckBox();
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
        $checkBox = new interactivecalculations\fe\displayTypes\CheckBox();
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
        $checkBox = new interactivecalculations\fe\displayTypes\CheckBox();
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

        $checkBox = new interactivecalculations\fe\displayTypes\CheckBox();
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
        $checkBox = new interactivecalculations\fe\displayTypes\CheckBox();
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
