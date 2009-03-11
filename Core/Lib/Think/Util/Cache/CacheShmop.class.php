<?php 

class CacheShmop extends Cache
{
    public function __construct($options='')
    {
        if ( !extension_loaded('shmop') ) {    
            throw_exception(L('_NOT_SUPPERT_').':shmop');
        }
        if(!empty($options)){
            $options = array(
                'size' => C('SHARE_MEM_SIZE'),
                'tmp'  => RUNTIME_PATH,
                'project' => 's'
                );
        }
        $this->options = $options;
        $this->handler = $this->_ftok($this->options['project']);
        $this->type = strtoupper(substr(__CLASS__,6));

    }

    public function get($name = false)
    {
		$this->Q(1);
        $id = shmop_open($this->handler, 'c', 0600, 0);
        if ($id !== false) {
            $ret = unserialize(shmop_read($id, 0, shmop_size($id)));
            shmop_close($id);

            if ($name === false) {
                return $ret;
            }
            if(isset($ret[$name])) {
                $content   =  $ret[$name];
                if(C('DATA_CACHE_COMPRESS') && function_exists('gzcompress')) {
                    $content   =   gzuncompress($content);
                }
                return $content;
            }else {
            	return null;
            }
        }else {
            return false;
        }

    }

    public function set($name, $value)
    {
		$this->W(1);
        $lh = $this->_lock();
        $val = $this->get();
        if (!is_array($val)) {
            $val = array();
        }
        if( C('DATA_CACHE_COMPRESS') && function_exists('gzcompress')) {
            $value   =   gzcompress($value,3);
        }
        $val[$name] = $value;
        $val = serialize($val);
        return $this->_write($val, $lh);
    }

    public function rm($name)
    {
        $lh = $this->_lock();

        $val = $this->get();
        if (!is_array($val)) {
            $val = array();
        }
        unset($val[$name]);
        $val = serialize($val);

        return $this->_write($val, $lh);
    }

    private function _ftok($project)
    {
        if (function_exists('ftok')) {
            return ftok(__FILE__, $project);
        }
        if(strtoupper(PHP_OS) == 'WINNT'){
            $s = stat(__FILE__);
            return sprintf("%u", (($s['ino'] & 0xffff) | (($s['dev'] & 0xff) << 16) |
            (($project & 0xff) << 24)));
        }else {
            $filename = __FILE__ . (string) $project;
            for($key = array(); sizeof($key) < strlen($filename); $key[] = ord(substr($filename, sizeof($key), 1)));
            return dechex(array_sum($key));
        }

    }

    private function _write(&$val, &$lh)
    {
        $id  = shmop_open($this->handler, 'c', 0600, $this->options['size']);
        if ($id) {
           $ret = shmop_write($id, $val, 0) == strlen($val);
           shmop_close($id);
           $this->_unlock($lh);
           return $ret;
        }

        $this->_unlock($lh);
        return false;
    }

    private function &_lock()
    {
        if (function_exists('sem_get')) {
            $fp = sem_get($this->handler, 1, 0600, 1);
            sem_acquire ($fp);
        } else {
            $fp = fopen($this->options['tmp'].$this->prefix.md5($this->handler), 'w');
            flock($fp, LOCK_EX);
        }

        return $fp;
    }

    private function _unlock(&$fp)
    {
        if (function_exists('sem_release')) {
            sem_release($fp);
        } else {
            fclose($fp);
        }
    }
}

?>