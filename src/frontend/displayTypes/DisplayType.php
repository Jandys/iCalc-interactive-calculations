<?php

namespace icalc\fe\displayTypes;

abstract class DisplayType
{


    protected function getDisplayType(){
        return explode('\\', strtolower(get_called_class()));
    }


    abstract public function render();
}