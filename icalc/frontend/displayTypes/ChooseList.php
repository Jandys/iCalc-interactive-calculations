<?php

namespace icalc\fe\displayTypes;

use Cassandra\Map;

class ChooseList extends DisplayType {

	private $id;
	private $name;
	private $options = [];
	private $is_multiple;
	private $default;
	private $displayLabel;
	private $labelClasses;
	private $label;
	private $classes;


	public function __construct() {
	}

	public function directConfiguration( $id, $name, $class, $options, $default = null, $is_multiple = false ) {
		$this->id          = $id;
		$this->name        = $name;
		$this->class       = $class;
		$this->options     = $options;
		$this->is_multiple = $is_multiple;
		$this->default     = $default;
	}


	function render(): string {

		error_log( "RENDER" );
		foreach ( $this->options as $option ) {
			error_log( "OPTION-> || OPTION NAME = " . $option["name"] . " || OPTION VALUE = " . $option["value"] );
		}


		$multiple = $this->is_multiple ? ' multiple ' : ' ';
		$select   = '<select name="' . $this->name . '" id="' . $this->id . '"  class="' . $this->classes . '" ' . $multiple . '>';

		if ( ! empty( $this->options ) ) {
			foreach ( $this->options as $option ) {
				$selected = '';
				if ( isset( $option["value"] ) && isset( $option["name"] ) ) {
					if ( $this->default != null && ( $option["name"] == $this->default || $option["value"] == $this->default ) ) {
						$selected = ' selected ';
					}
					$select = $select . ' <option value="' . $option["value"] . '"' . $selected . '>' . $option["name"] . '</option>';
				} else {
					if ( $this->default != null && $option == $this->default ) {
						$selected = ' selected ';
					}
					$select = $select . ' <option value="' . $option . '"' . $selected . '>' . $option . '</option>';
				}
			}
		}

		return $select . '</select>';

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
		$this->name    = $id;

		$newOptions = [];

		error_log( "FOR EACH AS KONFIGURACE" );
		foreach ( $conf->configuration as $key => $value ) {

			if ( str_contains( $key, "list" ) ) {
				$listId = - 1;
				if ( preg_match( '/\d+/', $key, $matches ) ) {
					$listId = $matches[0];
				}

				if ( ! isset( $newOptions[ $listId ] ) ) {
					$newOptions[ $listId ] = [];
				}


				if ( str_contains( $key, "list-value" ) ) {
					$newOptions[ $listId ]["value"] = $value;
				} elseif ( str_contains( $key, "list-option" ) ) {
					$newOptions[ $listId ]["name"] = $value;
				}
			}
		}

		foreach ( $newOptions as $option ) {
			$this->options[] = [
				"name"   => $option["name"],
				"value" => $option["value"],
			];
		}
	}
}