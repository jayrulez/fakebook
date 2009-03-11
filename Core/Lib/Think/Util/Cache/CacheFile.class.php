<?php

class CacheFile extends Cache
{
    public function __construct($options='')
    {
        if(!empty($options['temp'])){
            $this->options['temp'] = $options['temp'];
        }else {
            $this->options['temp'] = RUNTIME_PATH;
        }
        $this->expire = isset($options['expire'])?$options['expire']:C('DATA_CACHE_TIME');
        if(substr($this->options['temp'], -1) != "/")    $this->options['temp'] .= "/";
        $this->connected = is_dir($this->options['temp']) && is_writeable($this->options['temp']);
        $this->type = strtoupper(substr(__CLASS__,6));
        $this->init();

    }

    private function init()
    {
        $stat = stat($this->options['temp']);
		$dir_perms = $stat['mode'] & 0007777; // Get the permission bits.
		$file_perms = $dir_perms & 0000666; // Remove execute bits for files.

		if (!file_exists($this->options['temp'])) {
			if (!  mkdir($this->options['temp']))
				return false;
			 chmod($this->options['temp'], $dir_perms);
		}
    }

    private function isConnected()
    {
        return $this->connected;
    }

    private function filename($name)
    {
		$name	=	md5($name);
		if(C('DATA_CACHE_SUBDIR')) {
			$dir	=	$name{0};
			if(!is_dir($this->options['temp'].$dir)) {
				mkdir($this->options['temp'].$dir);
			}
			$filename	=	$dir.'/'.$this->prefix.$name.'.php';
		}else{
			$filename	=	$this->prefix.$name.'.php';
		}
        return $this->options['temp'].$filename;
    }

    public function get($name)
    {
        $filename   =   $this->filename($name);
        if (!$this->isConnected() || !is_file($filename)) {
           return false;
        }
		$this->Q(1);
        $content    =   file_get_contents($filename);
        if( false !== $content) {
            $expire  =  (int)substr($content,strlen(C('CACHE_SERIAL_HEADER')), 12);
            if($expire != -1 && time() > filemtime($filename) + $expire) {
                unlink($filename);
                return false;
            }
            if(C('DATA_CACHE_CHECK')) {
                $check  =  substr($content,strlen(C('CACHE_SERIAL_HEADER'))+12, 32);
                $content   =  substr($content,strlen(C('CACHE_SERIAL_HEADER'))+12+32, -strlen(C('CACHE_SERIAL_FOOTER')));
                if($check != md5($content)) {
                    return false;
                }
            }else {
            	$content   =  substr($content,strlen(C('CACHE_SERIAL_HEADER'))+12, -strlen(C('CACHE_SERIAL_FOOTER')));
            }
            if(C('DATA_CACHE_COMPRESS') && function_exists('gzcompress')) {
                $content   =   gzuncompress($content);
            }
            $content    =   unserialize($content);
            return $content;
        }
        else {
            return false;
        }
    }

    public function set($name,$value,$expire='')
    {
		$this->W(1);
        if('' === $expire) {
        	$expire =  $this->expire;
        }
        $filename   =   $this->filename($name);
        $data   =   serialize($value);
        if( C('DATA_CACHE_COMPRESS') && function_exists('gzcompress')) {
            $data   =   gzcompress($data,3);
        }
        if(C('DATA_CACHE_CHECK')) {
        	$check  =  md5($data);
        }else {
        	$check  =  '';
        }
        $data    = C('CACHE_SERIAL_HEADER').sprintf('%012d',$expire).$check.$data.C('CACHE_SERIAL_FOOTER');
        $result  =   file_put_contents($filename,$data);
        if($result) {
            clearstatcache();
            return true;
        }else {
        	return false;
        }
    }

    public function rm($name)
    {
        return unlink($this->filename($name));
    }

    public function clear()
    {
        import("ORG.Io.Dir");
        Dir::del($this->options['temp']);
    }
}

?>