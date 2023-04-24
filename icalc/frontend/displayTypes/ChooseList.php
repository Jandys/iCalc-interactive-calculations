<?php

namespace icalc\fe\displayTypes;

class ChooseList extends DisplayType
{

    private $id;
    private $name;
    private $class;
    private $options;
    private $is_multiple;
    private $default;


    public function __construct($id, $name, $class, $options, $default=null, $is_multiple = false)
    {
        $this->id = $id;
        $this->name = $name;
        $this->class = $class;
        $this->options = $options;
        $this->is_multiple = $is_multiple;
        $this->default = $default;
    }

    function render() :string
    {

        $multiple = $this->is_multiple ? ' multiple ' : ' ';
        $select = '<select name="' . $this->name . '" id="' . $this->id . '"  class="' . $this->class . '" ' . $multiple . '>';

        if (!empty($this->options)) {
            foreach ($this->options as $option) {
                $selected = '';
                if (isset($option->value) && isset($option->name)) {
                    if($this->default!=null && ($option->name == $this->default || $option->value == $this->default)){
                        $selected = ' selected ';
                    }
                    $select = $select . ' <option value="' . $option->value . '"' . $selected .'>' . $option->name . '</option>';
                } else {
                    if($this->default!=null && $option == $this->default ){
                        $selected = ' selected ';
                    }
                    $select = $select . ' <option value="' . $option. '"' . $selected .'>' . $option . '</option>';
                }
            }
        }

        return $select . '</select>';

    }
}