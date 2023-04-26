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

		error_log( "icalintgr" . json_encode( $interactionsByCalculationId ) );
		error_log( "grouped" . json_encode( self::groupByValue($interactionsByCalculationId) ) );



		$_SESSION["interactionsByCalculationId"] = self::groupByValue($interactionsByCalculationId);
		$_SESSION["interactionsInTime"]          = self::groupInteractionsByTenMinutes( $interactionsInTime );

		$byTime = plugins_url( 'graph_inTime.php', __FILE__ );
		$byCalc = plugins_url( 'graph_byCalc.php', __FILE__ );

		echo '<img src="' . esc_url( $byTime ) . '" alt="Graph in time" class="mt-4">';
		echo '<img src="' . esc_url( $byCalc ) . '" alt="Graph by calculation" class="mt-4">';

	}

	private static function groupInteractionsByTenMinutes( $interactionArray ): array {
		$counts = array();
		foreach ( $interactionArray as $timestamp ) {
			$roundedTimestamp = strtotime( $timestamp ) - (strtotime( $timestamp ) % 600);
			$tenMinuteInterval = date( 'Y-m-d H:i:00', $roundedTimestamp );
			if ( ! isset( $counts[ $tenMinuteInterval ] ) ) {
				$counts[ $tenMinuteInterval ] = 1;
			} else {
				$counts[ $tenMinuteInterval ] ++;
			}
		}
		$result = array();
		foreach ( $counts as $tenMinuteInterval => $count ) {
			$result[] = array( strtotime($tenMinuteInterval), $count );
		}

		// Sort the result array by timestamp (ascending order)
		usort($result, function($a, $b) {
			return $a[0] - $b[0];
		});

		return $result;
	}

	private static function groupByValue($groupByValue):array{
		$counts = array();
		foreach ($groupByValue as $value) {
			if (!isset($counts[intval($value)])) {
				$counts[intval($value)] = 1;
			} else {
				$counts[intval($value)]++;
			}
		}
		return $counts;
	}

}