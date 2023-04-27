<?php

namespace icalc\fe;

use icalc\db\model\Icalculations;

class StatisticsAdminFrontend extends AbstractAdminFrontend {

	public static function configuration() {


		$data = Icalculations::get_all();

		$interactionsByCalculationId = [];
		$interactionsInTime          = [];

		foreach ( $data as $record ) {
			array_push( $interactionsByCalculationId, $record["calculationId"] );
			array_push( $interactionsInTime, $record["created_at"] );
		}

		$_SESSION["interactionsByCalculationId"] = self::groupByValue( $interactionsByCalculationId );
		$_SESSION["interactionsInTime"]          = self::groupInteractionsByTenMinutes( $interactionsInTime );
		$_SESSION["interactionsData"]            = $data;

		$byTime      = plugins_url( 'graph_inTime.php', __FILE__ );
		$byCalc      = plugins_url( 'graph_byCalc.php', __FILE__ );
		$makeCsvData = plugins_url( 'makeCsvData.php', __FILE__ );

		$inTimeHasValues = true;
		$byCalcHasValues = true;

		if ( empty( $_SESSION["interactionsInTime"] ) ) {
			$inTimeHasValues = false;
		}
		if ( empty( $_SESSION["interactionsByCalculationId"] ) ) {
			$byCalcHasValues = false;
		}

		if ( $byCalcHasValues && $inTimeHasValues ) {
			echo "<a class='text-decoration-none' href='" . esc_url( $makeCsvData ) . "'><button  class='button btn-info position-relative mt-4 float-right'>" . __( "Download Interactions CSV" ) . "</button></a>";
		}

		if ( $inTimeHasValues ) {
			echo '<img src="' . esc_url( $byTime ) . '" alt="Graph in time" class="mt-4">';
		}
		if ( $inTimeHasValues ) {
			echo '<img src="' . esc_url( $byCalc ) . '" alt="Graph by calculation" class="mt-4">';
		}

		if ( ! $byCalcHasValues && ! $inTimeHasValues ) {
			echo "<div><p>" . __( "There are no statistics to be shown yet. Once there will be interactions with your calculations, come back." ) . "</p></div>";

			return;
		}


	}

	private static function groupInteractionsByTenMinutes( $interactionArray ): array {
		$counts = array();
		foreach ( $interactionArray as $timestamp ) {
			$roundedTimestamp  = strtotime( $timestamp ) - ( strtotime( $timestamp ) % 600 );
			$tenMinuteInterval = date( 'Y-m-d H:i:00', $roundedTimestamp );
			if ( ! isset( $counts[ $tenMinuteInterval ] ) ) {
				$counts[ $tenMinuteInterval ] = 1;
			} else {
				$counts[ $tenMinuteInterval ] ++;
			}
		}
		$result = array();
		foreach ( $counts as $tenMinuteInterval => $count ) {
			$result[] = array( strtotime( $tenMinuteInterval ), $count );
		}

		// Sort the result array by timestamp (ascending order)
		usort( $result, function ( $a, $b ) {
			return $a[0] - $b[0];
		} );

		return $result;
	}

	private static function groupByValue( $groupByValue ): array {
		$counts = array();
		foreach ( $groupByValue as $value ) {
			if ( ! isset( $counts[ intval( $value ) ] ) ) {
				$counts[ intval( $value ) ] = 1;
			} else {
				$counts[ intval( $value ) ] ++;
			}
		}

		return $counts;
	}

}