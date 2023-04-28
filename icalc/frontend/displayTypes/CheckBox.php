<?php

namespace icalc\fe\displayTypes;

/**
 * Class CheckBox
 *
 * A class that represents a checkbox display type.
 *
 * @package icalc\fe\displayTypes
 * @since 1.0.0
 */
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

    /**
     * Renders the checkbox.
     * Wrapped in generic div.
     *
     * @return string The rendered HTML for the checkbox.
     */
	function render(): string {
		$wrapper = '<div class="icalc-form-group form-outline form-group row">';

		$wrapper = $wrapper . $this->showLabel();

		$wrapper = $wrapper . $this->displayInput();

		$wrapper = $wrapper . '</div>';

		return $wrapper;
	}

    /**
     * Renders the checkbox.
     *
     * @return string The rendered HTML for the checkbox.
     */
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

    /**
     * Displays the label associated with the checkbox.
     *
     * @return string The rendered HTML for the label.
     */
	private function showLabel(): string {
		if ( $this->displayLabel ) {
			$label = new Label();
			$label->showLabel( $this->id, $this->labelClasses, $this->label );

			return $label->render();
		}

		return "";
	}

    /**
     * @return string
     */
	private function displayInput(): string {
		$returnValue = '<input class="icalc-calculation-checkbox-input ' . $this->classes . '" type="checkbox" id="' . $this->id . '" 
		data-value="' . $this->value . '"';

		if ( ! is_null( $this->name ) ) {
			$returnValue = $returnValue . 'name="' . $this->name . '"';
		}

		return $returnValue . '/>';
	}

}