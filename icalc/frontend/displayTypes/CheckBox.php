<?php

namespace icalc\fe\displayTypes;

class CheckBox extends DisplayType {


	private $id;
	private $name;
	private float $value;
	private $classes;
	private $displayLabel = false;
	private $label;
	private $labelClasses;

	public function __construct() {
	}

	function render(): string {
		$wrapper = '<div class="icalc-form-group form-outline form-group row">';

		$wrapper = $wrapper . $this->showLabel();

		$wrapper = $wrapper . $this->displayInput();

		$wrapper = $wrapper . '</div>';

		return $wrapper;
	}

	public function fillData( $args ): void {
		$id           = $args["id"];
		$masterObject = $args['masterObject'];
		$conf         = $args['conf'];

		$this->id           = $id;
		$this->displayLabel = boolval( $conf->configuration->{'show-label'} );
		$this->labelClasses = $conf->configuration->{'label-classes'};
		if ( $masterObject == null ) {
			$this->label = $conf->configuration->{'custom-label'};
			$this->value = floatval( $conf->configuration->{'base-value'} );
		} else {
			$this->label = $masterObject->name;
			$this->value = floatval( $masterObject->price );
		}

		$this->classes = str_replace( ";", " ", $conf->configuration->{'input-classes'} );
	}

	private function showLabel(): string {
		if ( $this->displayLabel ) {
			$label = new Label();
			$label->showLabel( $this->id, $this->labelClasses, $this->label );

			return $label->render();
		}

		return "";
	}


	private function displayInput(): string {
		$returnValue = '<input class="icalc-calculation-checkbox-input ' . $this->classes . '" type="checkbox" id="' . $this->id . '" 
		data-value="' . $this->value . '"';

		if ( ! is_null( $this->name ) ) {
			$returnValue = $returnValue . 'name="' . $this->name . '"';
		}

		return $returnValue . '/>';
	}

}