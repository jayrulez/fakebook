<?php 

class CacheSqlite extends Cache
{
    public function __construct($options='')
    {
        if ( !extension_loaded('sqlite') ) {    
            throw_exception(L('_NOT_SUPPERT_').':sqlite');
        }
        if(empty($options)){
            $options= array
            (
                'db'        => ':memory:',
                'table'     => 'sharedmemory',
                'var'       => 'var',
                'value'     => 'value',
                'expire'    => 'expire',
                'persistent'=> false
            );
        }
        $this->options = $options;
        $func = $this->options['persistent'] ? 'sqlite_popen' : 'sqlite_open';
        $this->handler = $func($this->options['db']);
        $this->connected = is_resource($this->handler);
        $this->type = strtoupper(substr(__CLASS__,6));

    }

    private function isConnected()
    {
        return $this->connected;
    }

    public function get($name)
    {
		$this->Q(1);
		$name   = sqlite_escape_string($name);
        $sql = 'SELECT '.$this->options['value'].
               ' FROM '.$this->options['table'].
               ' WHERE '.$this->options['var'].'=\''.$name.'\' AND ('.$this->options['expire'].'=-1 OR '.$this->options['expire'].'>'.time().
               ') LIMIT 1';
        $result = sqlite_query($this->handler, $sql);
        if (sqlite_num_rows($result)) {
            $content   =  sqlite_fetch_single($result);
            if(C('DATA_CACHE_COMPRESS') && function_exists('gzcompress')) {
                $content   =   gzuncompress($content);
            }
            return unserialize($content);
        }
        return false;
    }

    public function set($name, $value,$expireTime=0)
    {
		$this->W(1);
        $expire =  !empty($expireTime)? $expireTime : C('DATA_CACHE_TIME');
        $name  = sqlite_escape_string($name);
        $value = sqlite_escape_string(serialize($value));
        $expire =  ($expireTime==-1)?-1: (time()+$expire);
        if( C('DATA_CACHE_COMPRESS') && function_exists('gzcompress')) {
            $value   =   gzcompress($value,3);
        }
        $sql  = 'REPLACE INTO '.$this->options['table'].
                ' ('.$this->options['var'].', '.$this->options['value'].','.$this->options['expire'].
                ') VALUES (\''.$name.'\', \''.$value.'\', \''.$expire.'\')';
        sqlite_query($this->handler, $sql);
        return true;
    }

    public function rm($name)
    {
        $name  = sqlite_escape_string($name);
        $sql  = 'DELETE FROM '.$this->options['table'].
               ' WHERE '.$this->options['var'].'=\''.$name.'\'';
        sqlite_query($this->handler, $sql);
        return true;
    }

    public function clear()
    {
        $sql  = 'delete from `'.$this->options['table'].'`';
        sqlite_query($this->handler, $sql);
        return ;
    }
}

?>