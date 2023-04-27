<?php

require_once dirname( __DIR__ ) . '/vendor/autoload.php';

use Amenadiel\JpGraph\Graph\Graph;
use Amenadiel\JpGraph\Plot\BarPlot;
use Amenadiel\JpGraph\Plot\GroupBarPlot;

session_start();
header( 'Content-Type: image/png' );

$data = 	$_SESSION["interactionsByCalculationId"] ;

// Extract the second value (interactions) for each bar
$interactionCounts = array_map( 'intval', array_values($data) );
$calculationIds = array_map( 'intval', array_keys($data) );

// Create the bar plot for interactions
$barPlot = new BarPlot($interactionCounts);
$barPlot->value->Show();
$barPlot->value->SetFormat('%d');

$barPlot->value->SetColor('black');
$barPlot->value->SetFont(FF_ARIAL, FS_NORMAL, 10);

$graph = new Graph(600, 400);
$graph->SetScale("textlin");
$graph->title->Set('Interactions by Calculation ID');
$graph->title->SetFont(FF_DEFAULT,FS_BOLD);


$graph->SetMargin(120, 30, 50, 50);
$graph->xaxis->SetTickLabels( $calculationIds );
$graph->xaxis->title->Set('Calculation ID');
$graph->yaxis->title->Set('Interactions');
$graph->yaxis->title->SetMargin(15);

$graph->Add($barPlot);
header('Content-Type: image/png');
$graph->Stroke();

?>