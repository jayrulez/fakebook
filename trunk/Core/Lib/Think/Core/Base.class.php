<?php

abstract class Base
{
    public function __set($name ,$value)
    {
        if(property_exists($this,$name)){
            $this->$name = $value;
        }
    }

    public function __get($name)
    {
        if(isset($this->$name)){
            return $this->$name;
        }else {
            return null;
        }
    }

    protected function parseName($name,$type=0) {
        if($type) {
            return ucfirst(preg_replace("/_([a-zA-Z])/e", "strtoupper('\\1')", $name));
        }else{
            $name = preg_replace("/[A-Z]/", "_\\0", $name);
            return strtolower(trim($name, "_"));
        }
    }

}

?>