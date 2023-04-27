<?php

session_start();

$data = $_SESSION["interactionsData"];

if ( ! empty( $data ) ) {

	header( 'Content-Type: text/csv' );
	header( 'Content-Disposition: attachment; filename="icalc_statistics_export.csv"' );
	header( 'Pragma: no-cache' );
	header( 'Expires: 0' );

	$csvFile = fopen( 'php://output', 'w' );

	fputcsv( $csvFile, array( 'Interaction Id', 'Calculation Id', 'Calculation Body', 'User ID', 'Time of creation' ) );

	foreach ( $data as $record ) {
		fputcsv( $csvFile, $record );
	}

	fclose( $csvFile );
} else {
	echo "No data found";
}