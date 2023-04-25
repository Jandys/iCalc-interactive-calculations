<?php

namespace icalc\fe\displayTypes;

class DisplayTypeManager {


	private static $dislpayTypes = array(
		"number"          => Number::class,
		"number input"    => Number::class,
		"slider"          => Slider::class,
		"list"            => ChooseList::class,
		"label"           => Label::class,
		"text"            => Text::class,
		"checkbox"        => CheckBox::class,
		"hr"              => HorizontalRule::class,
		"horizontal rule" => HorizontalRule::class,
		"sum" => Sum::class,
		"complex calculation" => ComplexCalculation::class,
		"complex_calculation" => ComplexCalculation::class,
		"--none--"        => null,
		"-- none --"      => null,
	);

	private static $dislpayTypesProductAndService = array(
		"number"   => Number::class,
		"slider"   => Slider::class,
		"label"    => Label::class,
		"text"     => Text::class,
		"checkbox" => CheckBox::class
	);

	public static function getAllDisplayTypesForProductAndService(): array {
		return array_keys( DisplayTypeManager::$dislpayTypesProductAndService );
	}

	public static function fromNameToClass( $name ) {
		error_log("trinyg to get display type $name");
		return DisplayTypeManager::$dislpayTypes[ trim( strtolower( $name ) ) ];
	}
}