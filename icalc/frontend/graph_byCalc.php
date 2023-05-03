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

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Amenadiel\JpGraph\Graph\Graph;
use Amenadiel\JpGraph\Plot\BarPlot;

session_start();
header('Content-Type: image/png');

$data = $_SESSION["interactionsByCalculationId"];

// Extract the second value (interactions) for each bar
$interactionCounts = array_map('intval', array_values($data));
$calculationIds = array_map('intval', array_keys($data));

// Create the bar plot for interactions
$barPlot = new BarPlot($interactionCounts);
$barPlot->value->Show();
$barPlot->value->SetFormat('%d');

$barPlot->value->SetColor('black');
$barPlot->value->SetFont(FF_ARIAL, FS_NORMAL, 10);

$graph = new Graph(600, 400);
$graph->SetScale("textlin");
$graph->title->Set('Interactions by Calculation ID');
$graph->title->SetFont(FF_DEFAULT, FS_BOLD);


$graph->SetMargin(120, 30, 50, 50);
$graph->xaxis->SetTickLabels($calculationIds);
$graph->xaxis->title->Set('Calculation ID');
$graph->yaxis->title->Set('Interactions');
$graph->yaxis->title->SetMargin(15);

$graph->Add($barPlot);
header('Content-Type: image/png');
$graph->Stroke();

?>