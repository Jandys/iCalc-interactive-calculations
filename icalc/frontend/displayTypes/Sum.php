<?php

namespace icalc\fe\displayTypes;

class Sum extends DisplayType {

	protected $id;
	protected $labelClasses;
	protected $label;

	public function render(): string {
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



	}

	protected function showLabel() {
		if ( $this->displayLabel ) {
			$label = new Label();
			$label->showLabel( $this->id, $this->labelClasses, $this->label );

			return $label->render();
		}

		return "";
	}

	protected function displayInput() {
		return '<input id="'. $this->id .'" type="text" disabled class="form-control icalc-calculation-sum">';
	}
}