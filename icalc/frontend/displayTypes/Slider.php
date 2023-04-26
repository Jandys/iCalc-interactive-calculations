<?php

namespace icalc\fe\displayTypes;

class Slider extends DisplayType {
	private $id;
	private $name;
	private $max;
	private $value;
	private $classes;
	private $displayLabel = false;
	private $showValue = false;
	private $label;
	private $unit;
	private $labelClasses;


	public function __construct() {
	}

	function render(): string {
		$wrapper = '<div class="icalc-form-group form-outline form-group row">';

		$wrapper = $wrapper . $this->showLabel();

		$wrapper = $wrapper . $this->displayInput();

		$wrapper = $wrapper . $this->showValue();

		$wrapper = $wrapper . '</div>';

		return $wrapper;
	}

	public function fillData( $args ): void {
		$id           = $args["id"];
		$masterObject = $args['masterObject'];
		$conf         = $args['conf'];

		$this->id           = $id;
		$this->displayLabel = boolval( $conf->configuration->{'slider-show-value'} );
		$this->showValue    = boolval( $conf->configuration->{'slider-show-value'} );
		$this->labelClasses = $conf->configuration->{'label-classes'};
		if ( $masterObject == null ) {
			$this->unit  = "";
			$this->label = $conf->configuration->{'custom-label'};

		} else {
			$this->label = $masterObject->name;
			$this->unit  = $masterObject->unit;
		}

		$this->classes = str_replace( ";", " ", $conf->configuration->{'input-classes'} );
		$this->name    = $id;
		$this->max     = $conf->configuration->{'slider-max'};
		$this->value   = 0;
		$this->min     = 0;

	}

	private function showLabel(): string {
		if ( $this->displayLabel ) {
			$label = new Label();
			$label->showLabel( $this->id, $this->labelClasses, $this->label );

			return $label->render();
		}

		return "";
	}

	private function showValue(): string {
		if ( $this->showValue ) {
			return '<div id="displayValue-' . $this->id . '" class="icalc-display-slider-value">' . $this->value . '</div>';
		}

		return "";
	}

	/**
	 * @return bool
	 */
	public function is_show_value(): bool {
		return $this->showValue;
	}


	private function displayInput(): string {
		$returnValue = '<input class="icalc-calculation-slider ' . $this->classes . '" type="range" id="' . $this->id . '"';

		if ( ! is_null( $this->name ) ) {
			$returnValue = $returnValue . 'name="' . $this->name . '"';
		}

		if ( ! is_null( $this->min ) ) {
			$returnValue = $returnValue . 'min="' . $this->min . '"';
		}
		if ( ! is_null( $this->max ) && ! is_null( $this->min ) && $this->max > $this->min ) {
			$returnValue = $returnValue . ' max="' . $this->max . '"';
		}

		if ( is_null( $this->max ) ) {
			$this->max = 100;
		}

		if ( ! is_null( $this->value ) && $this->min <= $this->value && $this->value <= $this->max ) {
			$returnValue = $returnValue . 'value="' . $this->value . '"';
		} else {
			$returnValue = $returnValue . 'value="' . $this->min . '"';
		}

		return $returnValue . '/>';
	}
}