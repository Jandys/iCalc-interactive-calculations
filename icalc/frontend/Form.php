<?php

namespace icalc\fe;

class Form {

	private array $components = array();

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

	public function has($what):bool{
		foreach ($this->components as $component){
			$type = $component->get_display_type();
			if(strtolower(trim($type))==strtolower(trim($what))){
				return true;
			}
		}
		return false;
	}

	/**
	 * @return array
	 */
	public function get_components(): array {
		return $this->components;
	}




}