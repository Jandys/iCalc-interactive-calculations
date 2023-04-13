<?php

namespace icalc\fe\displayTypes;

class DisplayTypeManager
{


    private static $dislpayTypes = Array(
      "number"=>Number::class,
      "test"=>Text::class,
      "slider"=>Slider::class,
      "list"=>ChooseList::class,
      "label"=>Label::class
    );

    public static function getAllDisplayTypes(): array
    {
       return array_keys(DisplayTypeManager::$dislpayTypes);
    }

    public static function fromNameToClass($name){
        return DisplayTypeManager::$dislpayTypes[$name];
    }




}