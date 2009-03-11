<?php 

class CacheXcache extends Cache
{
    public function __construct($options='')
    {
        if ( !function_exists('xcache_info') ) {    
            throw_exception(L('_NOT_SUPPERT_').':Xcache');
        }
        $this->type = strtoupper(substr(__CLASS__,6));
		$this->expire = isset($options['expire'])?$options['expire']:C('DATA_CACHE_TIME');
    }

    public function get($name)
    {
		$this->Q(1);
		if (xcache_isset($name)) {
			return xcache_get($name);
		}
        return false;
    }

    public function set($name, $value,$expire='')
    {
		$this->W(1);
		if(empty($expire)) {
			$expire = $this->expire ;
		}
		return xcache_set($name, $value, $expire);
    }
    public function rm($name)
    {
		return xcache_unset($name);
    }

}

?>