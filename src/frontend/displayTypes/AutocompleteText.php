<?php

namespace icalc\fe\displayTypes;

use function icalc\util\requireAutocomplete;

class AutocompleteText extends DisplayType
{
    private $id;


    public function __construct($id)
    {
        $this->id = $id;
    }

    public function render() :string
    {
        requireAutocomplete();

        return '<input type="text" id="'.$this->id.'">
                <div id="suggest_'.$this->id.'"></div>';
    }

    public function autocomplete(){
        echo '<script>addAutocompleteToMyInput("'.$this->id.'","suggest_'.$this->id.'")</script>';
    }
}