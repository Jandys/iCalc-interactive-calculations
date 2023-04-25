<?php

namespace icalc\fe\displayTypes;

abstract class DisplayType
{


    protected function getDisplayType(){
        return explode('\\', strtolower(get_called_class()));
    }


    abstract public function render() :string;


	//$args=array('id'=>$id,'conf'=>$configuration,'masterObject'=>$masterObject);
	abstract public function fillData($args):void;
}