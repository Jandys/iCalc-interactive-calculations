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


use interactivecalculations\db\model\IcalculationsDescription;

class CalculationsDescriptionsAdminFrontend extends AbstractAdminFrontend
{

    public static function configuration()
    {
        self::populateinteractivecalculationsJSData();
        $data = IcalculationsDescription::get_all();

        if (is_null($data)) {
            error_log("ERROR Fetching data from API");
        }
        $tbody = "";
        $html = "";

        foreach ($data as $item) {
            $tbody = $tbody . '
            <tr>
                    <td>' . $item["id"] . '</td>
                    <td>' . $item["name"] . '</td>
                    <td>' . $item["description"] . '</td>
                    <td class="interactivecalculations-long-text-clipping">' . $item["body"] . '</td>
                    <td>' . $item["created_at"] . '</td>
                    <td>' . $item["modified_at"] . '</td>
                    <td class="text-center"><button class="btn btn-info" onclick="interactivecalculations_process_calculation_edit_action(\'' . $item["id"] . '\')"><span class="dashicons dashicons-edit"></span></button></td>
                    <td class="text-center"><button class="btn btn-danger" onclick="interactivecalculations_process_calculation_delete_action(' . $item["id"] . ',\'' . $item["name"] . '\')"><span class="dashicons dashicons-trash"></span></button></td>
                </tr>';
        }

        $howToInsertCalculation = "";
        if (!empty($tbody)) {
            $howToInsertCalculation = __("To insert calculation use shortcode in form: <span class='interactivecalculations-shortcode'>[interactivecalculations_calculation id=3]</span> or if you are using Elementor plugin you can just drag and drop widget");
        }


        $html = $html . '
    <div class="container pt-5">
            <!-- Table -->
            <span class="interactivecalculations-shortcode-reminder">' . $howToInsertCalculation . '</span>
            <table class="table table-bordered table-striped table-hover col-12">
                <thead class="thead-dark">
                    <tr class="col-12">
                        <th class="p-2 m-2">' . __("ID") . '</th>
                        <th class="p-2 m-2">' . __("Name") . '</th>
                        <th class="p-2 m-2">' . __("Description") . '</th>
                        <th class="p-2 m-2 interactivecalculations-long-text-clipping">' . __("Body") . '</th>
                        <th class="p-2 m-2">' . __("Created At") . '</th>
                        <th class="p-2 m-2">' . __("Modified At") . '</th>
                        <th class="col-1"></th>
                        <th class="col-1"></th>
                    </tr>
                </thead>
                <tbody id="table-body">
                ' . $tbody . '
                </tbody>
            </table>
        <!-- Pagination -->
        <div class="wp-block-navigation">
            <!-- Add pagination links here -->
        </div>
    </div>';

        return $html;
    }
}