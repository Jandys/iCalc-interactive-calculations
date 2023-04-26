<?php

namespace icalc\fe;

use icalc\db\model\Product;
use icalc\db\model\Service;
use icalc\fe\displayTypes\CheckBox;
use icalc\fe\displayTypes\ChooseList;
use icalc\fe\displayTypes\DisplayTypeManager;
use icalc\fe\displayTypes\Label;
use icalc\fe\displayTypes\Number;
use icalc\fe\displayTypes\Slider;

class Component {


	private $id;
	private $type;
	private $domId;
	private $displayType;
	private $parentComponent;
	private $configuration;
	private $componentRenderer;

	private $sumPrefix;
	private $sumPostFix;

	private $baseValue;
	private $masterObjectData;

	/**
	 * @param $id
	 * @param $type
	 * @param $domId
	 * @param $displayType
	 * @param $parentComponent
	 * @param $configuration
	 */
	public function __construct( $id, $type, $domId, $displayType, $parentComponent, $configuration ) {
		$this->id              = $id;
		$this->type            = $type;
		$this->domId           = $domId;
		$this->displayType     = $displayType;
		$this->parentComponent = $parentComponent;
		$this->configuration   = $configuration;

		$this->createComponentRenderer();
		$this->setCalculationValues();
	}


	public function createComponentRenderer() {
		$this->masterObjectData  = match ( $this->type ) {
			'product' => Product::get( "id", $this->id ),
			'service' => Service::get( "id", $this->id ),
			default => null,
		};
		$this->componentRenderer = $this->createDisplayType( $this->displayType, $this->domId, $this->configuration, $this->masterObjectData );
	}

	public function render() {
		if ( $this->componentRenderer == null ) {
			return "";
		}

		return $this->componentRenderer->render();
	}

	public function __toString(): string {
		return "Id: " . $this->id . ", " .
		       "Type: " . $this->type . ", " .
		       "domId: " . $this->domId . ", " .
		       "displayType: " . $this->displayType . ", " .
		       "parentComponent" . $this->parentComponent . ", " .
		       "masterObject" . json_encode( $this->masterObjectData ) . ", " .
		       "conf: " . json_encode( $this->configuration );
	}


	public function createDisplayType( $type, $id, $configuration, $masterObject ) {
		error_log("ASKING FOR OBJECT TYPE: $type");
		$classToCreate = DisplayTypeManager::fromNameToClass( $type );
		if ( ! $classToCreate ) {
			return null;
		}

		$displayType = new $classToCreate;
		$args        = array( 'id' => $id, 'conf' => $configuration, 'masterObject' => $masterObject );
		$displayType->fillData( $args );
		return $displayType;

	}

	private function setCalculationValues(){
		if(strtolower(trim($this->type)) == 'genericcomponent'){
			$this->baseValue=$this->configuration->configuration->{'base-value'};
		}else{
			$this->baseValue=$this->masterObjectData->price;
		}
		if(strtolower(trim($this->displayType))=='sum'){
			$this->sumPostFix=$this->configuration->configuration->{'sum-postfix'};
			$this->sumPrefix=$this->configuration->configuration->{'sum-prefix'};
		}
	}

	/**
	 * @return mixed
	 */
	public function get_base_value() {
		if(!is_numeric( $this->baseValue)){
			return 1;
		}else{
			return $this->baseValue;
		}
	}


	/**
	 * @return mixed
	 */
	public function get_type() {
		return $this->type;
	}

	/**
	 * @return mixed
	 */
	public function get_display_type() {
		return $this->displayType;
	}

	/**
	 * @return mixed
	 */
	public function get_dom_id() {
		return $this->domId;
	}

	public function getSumPrefix(){
		return $this->sumPrefix;
	}


	public function getSumPostFix(){
		return $this->sumPostFix;
	}



}