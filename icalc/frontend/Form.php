<?php

namespace icalc\fe;

class Form {

	private $components = array();

	public function __construct() {
	}

	public function addComponent( mixed $component ) {
		$id = $component->id;
		$type = $component->type;
		$domId = $component->domId;
		$displayType = $component->displayType;
		$parentComponent = $component->parentComponent;
		$configuration = $component->conf;


		$componentObject = new Component( $id, $type, $domId, $displayType, $parentComponent, $configuration );
		$this->components[$domId] = $componentObject;
	}

	public function render():string {
		$formRendering = "<form>";

		foreach ($this->components as $component){
			if ($component != null){
				$formRendering = $formRendering . $component->render() . "   ";
			}
		}


		return $formRendering . "</form>";
	}

	public function hasSum():bool{
		foreach ($this->components as $component){
			$type = $component->getType();
			if(strtolower(trim($type))=="sum"){
				return true;
			}
		}
		return false;
	}


}