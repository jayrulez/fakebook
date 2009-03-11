<?php 

class CacheApc extends Cache
{
    function __construct($options='')
    {
		if(!function_exists('apc_cache_info')) {
			throw_exception(L('_NOT_SUPPERT_').':Apc');
		}
		$this->expire = isset($options['expire'])?$options['expire']:C('DATA_CACHE_TIME');
        $this->type = strtoupper(substr(__CLASS__,6));
    }

     function get($name)
     {
		$this->Q(1);
         return apc_fetch($name);
     }

     function set($name, $value, $ttl = null)
     {
		$this->W(1);
        if(isset($ttl) && is_int($ttl))
            $expire = $ttl;
        else 
            $expire = $this->expire;
         return apc_store($name, $value, $expire);
     }

     function rm($name)
     {
         return apc_delete($name);
     }

    function clear()
    {
        return apc_clear_cache();
    }
}

?>