<?php

namespace icalc\fe;

use icalc\db\model\Product;
use icalc\db\model\Service;
use icalc\fe\displayTypes\DisplayTypeManager;

class Component {


	private $id;
	private $type;
	private $domId;
	private $displayType;
	private $parentComponent;
	private $configuration;
	private $componentRenderer;

	private $uncheckedValue;

	private string $complexCalculation;
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
        $parentComponentStr = $this->parentComponent ? (string) $this->parentComponent : "null";
        $masterObjectDataStr = $this->masterObjectData ? json_encode($this->masterObjectData) : "null";
        $configurationStr = $this->configuration ? json_encode($this->configuration) : "null";

        return "Id: " . $this->id . ", " .
            "Type: " . $this->type . ", " .
            "domId: " . $this->domId . ", " .
            "displayType: " . $this->displayType . ", " .
            "baseValue: " . $this->baseValue . ", " .
            "parentComponent: " . $parentComponentStr . ", " .
            "masterObjectData: " . $masterObjectDataStr . ", " .
            "configuration: " . $configurationStr;
    }


	public function createDisplayType( $type, $id, $configuration, $masterObject ) {
		$classToCreate = DisplayTypeManager::fromNameToClass( $type );
		if ( ! $classToCreate ) {
			return null;
		}

		$displayType = new $classToCreate;
		$args        = array( 'id' => $id, 'conf' => $configuration, 'masterObject' => $masterObject );
		$displayType->fillData( $args );

		return $displayType;

	}

	private function setCalculationValues() {
        if ( strcasecmp($this->type,'genericcomponent') == 0 || strcasecmp($this->type,'generic component') == 0 ) {
			$this->baseValue = $this->configuration->configuration->{'base-value'};
		} else {
            if(isset($this->masterObjectData) && isset($this->masterObjectData->price)){
                $this->baseValue = $this->masterObjectData->price;
            }else{
                $this->baseValue = floatval($this->configuration->configuration->{'base-value'});
            }
		}
		if ( strtolower( trim( $this->displayType ) ) == 'sum'
		     || strtolower( trim( $this->displayType ) ) == "product calculation"
		     || strtolower( trim( $this->displayType ) ) == "subtract calculation"
		     || strtolower( trim( $this->displayType ) ) == "complex calculation" ) {
			$this->sumPostFix = $this->configuration->configuration->{'sum-postfix'};
			$this->sumPrefix  = $this->configuration->configuration->{'sum-prefix'};
		}
		if ( strtolower( trim( $this->displayType ) ) == "complex calculation" ) {
			$this->complexCalculation = $this->configuration->configuration->{"complex-calculation"};
		}
		if ( strtolower( trim( $this->displayType ) ) == "checkbox" ) {
			$this->uncheckedValue = $this->configuration->configuration->{"unchecked-value"};
		}
	}

	/**
	 * @return mixed
	 */
	public function get_base_value() {
		if ( ! is_numeric( $this->baseValue ) ) {
			return 1;
		} else {
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

	public function getSumPrefix() {
		return $this->sumPrefix;
	}


	public function getSumPostFix() {
		return $this->sumPostFix;
	}

	/**
	 * @return string
	 */
	public function get_complex_calculation(): string {
		return $this->complexCalculation;
	}

	/**
	 * @return mixed
	 */
	public function get_unchecked_value() {
		if ( ! isset( $this->uncheckedValue ) ) {
			return 1;
		}

		return $this->uncheckedValue;
	}

	/**
	 * @return mixed
	 */
	public function get_parent_component() {
		return $this->parentComponent;
	}


}