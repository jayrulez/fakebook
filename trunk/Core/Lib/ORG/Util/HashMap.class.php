<?php

class HashMap extends Base  implements IteratorAggregate
{
    protected $_values = array();

    public function __construct($values = array())
    {
        if (!empty($values)) {
            $this->_values = $values;
        }
    }

    public function getIterator()
    {
        return new ArrayObject($this->_values);
    }

    public function clear()
    {
        $this->_values = array();
    }

    public function containsKey($key)
    {
        return array_key_exists($key, $this->_values);
    }

    public function containsValue($value)
    {
        return in_array($value, $this->_values);
    }

    public function contains($key, $value)
    {
        if ($this->containsKey($key))
        {
            return ($this->get($key) == $value);
        }
        return false;
    }

    public function get($key)
    {
        if ($this->containsKey($key)) {
            return $this->_values[$key];
        } else {
            return null;
        }
    }

    public function isEmpty()
    {
        return empty($this->_values);
    }

    public function toArray()
    {
        return $this->_values;
    }

    public function keySet()
    {
        return array_keys($this->_values);
    }

    public function put($key, $value)
    {
        $previous = $this->get($key);
        $this->_values[$key] =&$value;
        return $previous;
    }

    public function __get($name) {
        return $this->get($name);
    }

    public function __set($name,$value) {
        return $this->put($name,$value);
    }

    public function putAll($values)
    {
        if (is_array($values)) {
            foreach ($values as $key => $value) {
                $this->put($key, $value);
            }
        }
    }

    public function remove($key)
    {
        $value = $this->get($key);
        if (!is_null($value)) { unset($this->_values[$key]); }
        return $value;
    }

    public function size()
    {
        return count($this->_values);
    }

    public function values()
    {
        return array_values($this->_values);
    }
}

?>