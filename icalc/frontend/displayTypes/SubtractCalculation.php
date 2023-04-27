<?php

namespace icalc\fe\displayTypes;

class SubtractCalculation extends Sum {

	private float $startingValue;

	protected function displayInput() {
		return '<input id="' . $this->id . '" type="text" disabled class="form-control icalc-calculation-subtract-calculation" data-starting-value='.$this->startingValue.'>';
	}


	public function fillData( $args ): void {
		parent::fillData( $args );
		$conf = $args['conf'];

		$this->startingValue = floatval( $conf->configuration->{'subtract-value'} );
	}
}