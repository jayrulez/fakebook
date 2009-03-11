<?php

class CacheMemcache extends Cache
{
    function __construct($options='')
    {
        if ( !extension_loaded('memcache') ) {
            throw_exception(L('_NOT_SUPPERT_').':memcache');
        }
        if(empty($options)) {
            $options = array
            (
                'host'  => '127.0.0.1',
                'port'  => 11211,
                'timeout' => false,
                'persistent' => false
            );
        }
        $func = $options['persistent'] ? 'pconnect' : 'connect';
        $this->expire = isset($options['expire'])?$options['expire']:C('DATA_CACHE_TIME');
        $this->handler  = new Memcache;
        $this->connected = $options['timeout'] === false ?
            $this->handler->$func($options['host'], $options['port']) :
            $this->handler->$func($options['host'], $options['port'], $options['timeout']);
        $this->type = strtoupper(substr(__CLASS__,6));
    }

    private function isConnected()
    {
        return $this->connected;
    }

    public function get($name)
    {
		$this->Q(1);
        return $this->handler->get($name);
    }

    public function set($name, $value, $ttl = null)
    {
		$this->W(1);
        if(isset($ttl) && is_int($ttl))
            $expire = $ttl;
        else
            $expire = $this->expire;
        return $this->handler->set($name, $value, 0, $expire);
    }

    public function rm($name, $ttl = false)
    {
        return $ttl === false ?
            $this->handler->delete($name) :
            $this->handler->delete($name, $ttl);
    }

    public function clear()
    {
        return $this->handler->flush();
    }
}

?>