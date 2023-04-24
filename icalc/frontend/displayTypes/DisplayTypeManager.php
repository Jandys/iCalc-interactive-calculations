<?php

namespace icalc\fe\displayTypes;

class DisplayTypeManager
{


    private static $dislpayTypes = Array(
      "number"=>Number::class,
      "slider"=>Slider::class,
	  "list"=>ChooseList::class,
      "label"=>Label::class,
      "text"=>Text::class,
	  "checkbox"=>CheckBox::class
    );

	private static $dislpayTypesProductAndService = Array(
		"number"=>Number::class,
		"slider"=>Slider::class,
		"label"=>Label::class,
		"text"=>Text::class,
		"checkbox"=>CheckBox::class
	);

    public static function getAllDisplayTypesForProductAndService(): array
    {
       return array_keys(DisplayTypeManager::$dislpayTypesProductAndService);
    }

    public static function fromNameToClass($name){
        return DisplayTypeManager::$dislpayTypes[$name];
    }




}