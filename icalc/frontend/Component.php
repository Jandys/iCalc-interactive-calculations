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
		$classToCreate = DisplayTypeManager::fromNameToClass( $type );
		if ( ! $classToCreate ) {
			return null;
		}

		$displayType = new $classToCreate;

//
		$args        = array( 'id' => $id, 'conf' => $configuration, 'masterObject' => $masterObject );
//		$displayType = call_user_func( array( $classToCreate, '__construct' ) );
		$displayType->fillData( $args );

		return $displayType;

	}

}