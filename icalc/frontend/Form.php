<?php

namespace icalc\fe;

/**
 * Class Form
 *
 * @package icalc\fe
 * @since 1.0.0
 */
class Form
{

    private array $components = array();

    public function __construct()
    {
    }

    /**
     * Adds a component to the form.
     *
     * @param mixed $component The component to add.
     *
     * @return void
     */
    public function addComponent(mixed $component)
    {
        $id = $component->id;
        $type = $component->type;
        $domId = $component->domId;
        $displayType = $component->displayType;
        $parentComponent = $component->parentComponent;
        $configuration = $component->conf;


        $componentObject = new Component($id, $type, $domId, $displayType, $parentComponent, $configuration);
        $this->components[$domId] = $componentObject;
    }

    /**
     * Renders the form and its components.
     *
     * @return string The rendered form and its components.
     */
    public function render(): string
    {
        $formRendering = "<form>";

        foreach ($this->components as $component) {
            if ($component != null) {
                $formRendering = $formRendering . $component->render() . "   ";
            }
        }


        return $formRendering . "</form>";
    }

    /**
     * Checks if the form has a component of a given type.
     *
     * @param string $what The type of component to check for.
     *
     * @return bool True if the form has a component of the given type, false otherwise.
     */
    public function has($what): bool
    {
        foreach ($this->components as $component) {
            $type = $component->get_display_type();
            if (strtolower(trim($type)) == strtolower(trim($what))) {
                return true;
            }
        }
        return false;
    }

    /**
     * Gets all components of the form.
     *
     * @return array The components of the form.
     */
    public function get_components(): array
    {
        return $this->components;
    }
}