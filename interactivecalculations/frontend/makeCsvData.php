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

session_start();

$data = $_SESSION["interactionsData"];

if (!empty($data)) {

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="interactivecalculations_statistics_export.csv"');
    header('Pragma: no-cache');
    header('Expires: 0');

    $csvFile = fopen('php://output', 'w');

    fputcsv($csvFile, array('Interaction Id', 'Calculation Id', 'Calculation Body', 'User ID', 'Time of creation'));

    foreach ($data as $record) {
        fputcsv($csvFile, $record);
    }

    fclose($csvFile);
} else {
    echo "No data found";
}