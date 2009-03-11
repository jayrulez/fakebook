<?php

class Stack extends ArrayList
{
    public function __construct($values = array())
    {
        parent::__construct($values);
    }

    public function peek()
    {
        return reset($this->toArray());
    }

    public function pop()
    {
        $el_array = $this->toArray();
        $return_val = array_pop($el_array);
        $this->_elements = $el_array;
        return $return_val;
    }
	
    public function push($value)
    {
        $this->add($value);
        return $value;
    }
}

?>
