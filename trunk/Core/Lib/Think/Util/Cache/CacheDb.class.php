<?php 

class CacheDb extends Cache
{
    var $db;

    function __construct($options='')
    {
        if(empty($options)){
            $options= array
            (
                'db'        => C('DB_NAME'),
                'table'     => C('DB_PREFIX').C('DATA_CACHE_TABLE'),
                'expire'    => C('DATA_CACHE_TIME'),
            );
        }
        $this->options = $options;
		import('Think.Db.Db');
        $this->db  = DB::getInstance();
        $this->connected = is_resource($this->db);
        $this->type = strtoupper(substr(__CLASS__,6));

    }

    private function isConnected()
    {
        return $this->connected;
    }

    public function get($name)
    {
        $name  =  addslashes($name);
		$this->Q(1);
        $result  =  $this->db->getRow('select `data`,`datacrc`,`datasize` from `'.$this->options['table'].'` where `cachekey`=\''.$name.'\' and (`expire` =-1 OR `expire`>'.time().') limit 0,1');
        if(false !== $result ) {
            if(is_object($result)) {
            	$result  =  get_object_vars($result);
            }
            if(C('DATA_CACHE_CHECK')) {
                if($result['datacrc'] != md5($result['data'])) {
                    return false;
                }
            }
            $content   =  $result['data'];
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
	
    public function set($name, $value,$expireTime=0)
    {
        $data   =   serialize($value);
        $name  =  addslashes($name);
		$this->W(1);
        if( C('DATA_CACHE_COMPRESS') && function_exists('gzcompress')) {
            $data   =   gzcompress($data,3);
        }
        if(C('DATA_CACHE_CHECK')) {
        	$crc  =  md5($data);
        }else {
        	$crc  =  '';
        }
        $expire =  !empty($expireTime)? $expireTime : $this->options['expire'];
        $map    = array();
        $map['cachekey']	 =	 $name;
        $map['data']	=	$data	 ;
        $map['datacrc']	=	$crc;
        $map['expire']	=	($expire==-1)?-1: (time()+$expire) ;
        $map['datasize']	=	strlen($data);
        $result  =  $this->db->getRow('select `id` from `'.$this->options['table'].'` where `cachekey`=\''.$name.'\' limit 0,1');
        if(false !== $result ) {
            $result  =  $this->db->save($map,$this->options['table'],'`cachekey`=\''.$name.'\'');
        }else {
             $result  =  $this->db->add($map,$this->options['table']);
        }
        if($result) {
            return true;
        }else {
        	return false;
        }
    }

    public function rm($name)
    {
        $name  =  addslashes($name);
        return $this->db->_execute('delete from `'.$this->options['table'].'` where `cachekey`=\''.$name.'\'');
    }

    public function clear()
    {
        return $this->db->_execute('truncate table `'.$this->options['table'].'`');
    }

}

?>