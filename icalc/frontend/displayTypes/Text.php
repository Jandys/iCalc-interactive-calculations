<?php

namespace icalc\fe\displayTypes;

class Text extends DisplayType
{
	private $id;
	private $name;
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
		} else {
			$this->label = $masterObject->name;
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
		$returnValue = '<input class="icalc-calculation-text-input ' . $this->classes . '" type="text" id="' . $this->id . '"';

		if ( ! is_null( $this->name ) ) {
			$returnValue = $returnValue . 'name="' . $this->name . '"';
		}

		return $returnValue . '/>';
	}

}