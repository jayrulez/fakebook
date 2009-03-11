<?php

class Cache extends Base
{
    protected $connected  ;

    protected $handler    ;

    protected $prefix='~@';

    protected $options = array();

    protected $type       ;

    protected $expire     ;

    public function connect($type='',$options=array())
    {
        if(empty($type)){
            $type = C('DATA_CACHE_TYPE');
        }
        if(Session::is_set('CACHE_'.strtoupper($type))) {
            $cacheClass   = Session::get('CACHE_'.strtoupper($type));
        }else {
            $cachePath = dirname(__FILE__).'/Cache/';
            $cacheClass = 'Cache'.ucwords(strtolower(trim($type)));
            require_cache($cachePath.$cacheClass.'.class.php');
        }
        if(class_exists($cacheClass)){
            $cache = new $cacheClass($options);
        }else {
            throw_exception(L('_CACHE_TYPE_INVALID_').':'.$type);
        }
        return $cache;
    }

    public function __get($name) {
        return $this->get($name);
    }

    public function __set($name,$value) {
        return $this->set($name,$value);
    }

    public function setOptions($name,$value) {
        $this->options[$name]   =   $value;
    }

    public function getOptions($name) {
        return $this->options[$name];
    }
	
    static function getInstance()
    {
       $param = func_get_args();
        return get_instance_of(__CLASS__,'connect',$param);
    }

    public function Q($times='') {
        static $_times = 0;
        if(empty($times)) {
            return $_times;
        }else{
            $_times++;
        }
    }

    public function W($times='') {
        static $_times = 0;
        if(empty($times)) {
            return $_times;
        }else{
            $_times++;
        }
    }
}

?>