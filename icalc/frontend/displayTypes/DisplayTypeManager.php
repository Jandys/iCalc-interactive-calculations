<?php

namespace icalc\fe\displayTypes;

/**
 * Class DisplayTypeManager
 * Manages the available display types for the calculator elements.
 */
class DisplayTypeManager
{

    /**
     * Array that maps display type names to their corresponding class names.
     *
     * @var array
     */
    private static $dislpayTypes = array(
        "number" => Number::class,
        "number input" => Number::class,
        "slider" => Slider::class,
        "list" => ChooseList::class,
        "label" => Label::class,
        "text" => Text::class,
        "checkbox" => CheckBox::class,
        "hr" => HorizontalRule::class,
        "horizontal rule" => HorizontalRule::class,
        "spacer" => Spacer::class,
        "sum" => Sum::class,
        "complex calculation" => ComplexCalculation::class,
        "complex_calculation" => ComplexCalculation::class,
        "product_calculation" => ProductCalculation::class,
        "product calculation" => ProductCalculation::class,
        "subtract calculation" => SubtractCalculation::class,
        "subtract_calculation" => SubtractCalculation::class,
        "--none--" => null,
        "-- none --" => null,
    );

    /**
     * Array of display types available for product and service calculator elements.
     *
     * @var array
     */
    private static $dislpayTypesProductAndService = array(
        "number" => Number::class,
        "slider" => Slider::class,
        "label" => Label::class,
        "text" => Text::class,
        "checkbox" => CheckBox::class
    );

    /**
     * Returns an array of all available display types for product and service calculator elements.
     *
     * @return array
     */
    public static function getAllDisplayTypesForProductAndService(): array
    {
        return array_keys(DisplayTypeManager::$dislpayTypesProductAndService);
    }

    /**
     * Returns the class name for a given display type name.
     *
     * @param string $name The display type name.
     * @return mixed|null The class name for the given display type name, or null if the name is invalid.
     * @since 1.0.0
     */
    public static function fromNameToClass($name)
    {
        return DisplayTypeManager::$dislpayTypes[trim(strtolower($name))];
    }
}