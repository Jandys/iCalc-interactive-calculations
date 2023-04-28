<?php

namespace icalc\fe\displayTypes;
/**
 * Abstract base class for all display types.
 */
abstract class DisplayType
{
    /**
     * Returns the name of the display type class.
     *
     * @return string The name of the display type class.
     * @since 1.0.0
     */
    protected function getDisplayType()
    {
        return explode('\\', strtolower(get_called_class()));
    }

    /**
     * Renders the HTML for the display type.
     *
     * @return string The HTML for the display type.
     * @since 1.0.0
     */
    abstract public function render(): string;

    /**
     * Fills the display type object with data from the arguments.
     *
     * @param array $args An array of arguments used to fill the display type object.
     * @return void
     * @since 1.0.0
     */
    abstract public function fillData($args): void;
}