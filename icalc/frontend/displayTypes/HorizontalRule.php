<?php

namespace icalc\fe\displayTypes;

class HorizontalRule extends DisplayType {

	private $classes;

	public function render(): string {
		return '<hr class=" '. $this->classes .'" />';
	}

	public function fillData( $args ): void {
		$id           = $args["id"];
		$masterObject = $args['masterObject'];
		$conf         = $args['conf'];


		$this->classes = $conf->configuration->{'input-classes'};

	}
}