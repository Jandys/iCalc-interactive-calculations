<?php
/*
 *
 *   This file is part of the 'Inter Calcus' project.
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

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Amenadiel\JpGraph\Graph\Graph;
use Amenadiel\JpGraph\Plot\LinePlot;

header('Content-Type: image/png');

session_start();

$data = $_SESSION["interactionsInTime"];

$inTime = new Graph(800, 400);
$inTime->SetScale("datlin");
$inTime->title->Set('Interactions in Time');
$inTime->title->SetFont(FF_DEFAULT, FS_BOLD);


$inTime->SetMargin(110, 60, 60, 120);
$inTime->xaxis->SetLabelFormatString('Y-m-d H:i:s', true);
$inTime->xaxis->title->Set('Time');
$inTime->yaxis->title->Set('Interactions');
$inTime->yaxis->title->SetMargin(15);
$inTime->xaxis->title->SetMargin(25);
$inTime->xaxis->SetLabelAngle(45);
$inTime->xaxis->SetTextTickInterval(4);
$inTime->yaxis->scale->ticks->Set(1, 0);


$lineplot = new LinePlot(array_column($data, 1), array_column($data, 0));
$lineplot->SetFillColor('lightred@0.5');
$lineplot->value->Show();
$lineplot->value->HideZero();
$lineplot->value->SetFormat('%d');

$inTime->Add($lineplot);
$customLabels = array_map(function ($item) {
    return date('m-d H:i', $item[0]);
}, $data);

$inTime->xaxis->SetTickLabels($customLabels);


$inTime->Stroke();
session_write_close();
?>