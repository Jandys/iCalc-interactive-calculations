<?php

namespace icalc\fe\displayTypes;

class Spacer extends DisplayType {

	private $classes;

	public function render(): string {
		return '<div class=" '. $this->classes .'" ></div>';
	}

	public function fillData( $args ): void {
		$id           = $args["id"];
		$masterObject = $args['masterObject'];
		$conf         = $args['conf'];


		$this->classes = str_replace( ";", " ", $conf->configuration->{'input-classes'} );

	}
}