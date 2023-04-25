<?php

namespace icalc\fe\displayTypes;

class ComplexCalculation extends Sum {

	protected function displayInput() {
		return '<input id="'. $this->id .'" type="text" disabled class="form-control icalc-calculation-complex-calculation">';
	}

}