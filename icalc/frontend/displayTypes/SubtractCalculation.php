<?php

namespace icalc\fe\displayTypes;

class SubtractCalculation extends Sum {

	protected function displayInput() {
		return '<input id="'. $this->id .'" type="text" disabled class="form-control icalc-calculation-subtract-calculation">';
	}

}