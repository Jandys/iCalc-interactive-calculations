<?php


require_once dirname( __DIR__ ) . '/vendor/autoload.php';

use Amenadiel\JpGraph\Graph\Graph;
use Amenadiel\JpGraph\Plot\LinePlot;
header( 'Content-Type: image/png' );

session_start();

$data = $_SESSION["interactionsInTime"];

$inTime = new Graph( 800, 300 );
$inTime->SetScale( "datlin" );
$inTime->title->Set( 'Interactions in Time' );

$inTime->SetMargin( 60, 60, 60, 60 );
$inTime->xaxis->SetLabelFormatString( 'Y-m-d H:i:s', true );
$inTime->xaxis->title->Set( 'Time' );
$inTime->yaxis->title->Set( 'Interactions' );
$inTime->xaxis->SetLabelAngle(20);
$inTime->yaxis->scale->ticks->Set(1, 0);


$lineplot = new LinePlot( array_column( $data, 1 ), array_column( $data, 0 ) );
$lineplot->SetFillColor( 'lightblue@0.5' );
$lineplot->value->Show();
$lineplot->value->HideZero();
$lineplot->value->SetFormat('%d');

$inTime->Add( $lineplot );
$customLabels = array_map(function ($item) {
	return date('m-d H:i', $item[0]);
}, $data);

$inTime->xaxis->SetTickLabels($customLabels);


$inTime->Stroke();
?>