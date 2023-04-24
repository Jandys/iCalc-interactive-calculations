<?php

namespace icalc\fe\displayTypes;

class Label extends DisplayType
{

    private $forId;
    private $label;

    public function __construct($forId, $label)
    {
        $this->forId=$forId;
        $this->label=$label;
    }

    public function render(): string
    {
        return '<label class="icalc-number-label" for="' . $this->forId . '">' . $this->label . '</label>';
    }
}