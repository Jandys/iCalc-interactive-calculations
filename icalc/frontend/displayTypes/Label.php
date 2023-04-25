<?php

namespace icalc\fe\displayTypes;

class Label extends DisplayType {

	private $forId;
	private $label;
	private $class;

	public function __construct() {
	}

	public function showLabel($forId, $classes, $label): Label{
		$this->forId = $forId;
		$this->class = $classes;
		$this->label = $label;
		return $this;
	}

	public function render(): string {
		$for = "";
		if($this->forId){
			$for = 'for="' . $this->forId . '"';
		}

		return '<label class="icalc-calculation-label ' . $this->class . '" '. $for .'>' . $this->label . '</label>';
	}

	//$args=array('id'=>$id,'conf'=>$configuration,'masterObject'=>$masterObject);
	public function fillData( $args ): void {
		$masterObject = $args['masterObject'];
		$conf =  $args['conf'];

		if($masterObject==null){
			$this->label=$conf->configuration->{'custom-label'};
		}else{
			$this->label=$masterObject->name;
			$this->forId = $args['id'];
		}

		$this->class=str_replace(";", " ", $conf->configuration->{'label-classes'});
	}
}