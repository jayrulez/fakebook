<?php 

class CacheEaccelerator extends Cache
{
    public function __construct($options='')
    {
        $this->type = strtoupper(substr(__CLASS__,6));
    }

     public function get($name)
     {
		$this->Q(1);
         return eaccelerator_get($name);
     }

     public function set($name, $value, $ttl = null)
     {
		$this->W(1);
        if(isset($ttl) && is_int($ttl))
            $expire = $ttl;
        else 
            $expire = $this->expire;
         eaccelerator_lock($name);
         return eaccelerator_put ($name, $value, $expire);
     }

     public function rm($name)
     {
         return eaccelerator_rm($name);
     }

}

?>