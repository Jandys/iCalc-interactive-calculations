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
		"spacer" => Spacer::class,
		"sum" => Sum::class,
		"complex calculation" => ComplexCalculation::class,
		"complex_calculation" => ComplexCalculation::class,
		"product_calculation" => ProductCalculation::class,
		"product calculation" => ProductCalculation::class,
		"subtract calculation" => SubtractCalculation::class,
		"subtract_calculation" => SubtractCalculation::class,
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
		return DisplayTypeManager::$dislpayTypes[ trim( strtolower( $name ) ) ];
	}
}