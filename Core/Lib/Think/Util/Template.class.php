<?php

class Template extends Base
{
    protected $name =  '';

    private $_tpl =  null;

    static function getInstance() {
        return get_instance_of(__CLASS__);
    }

    public function __construct() {
        $this->name   =  C('TMPL_ENGINE_TYPE')?C('TMPL_ENGINE_TYPE'):'PHP';
        $className   = 'Template'.ucwords(strtolower($this->name));
        require_cache(dirname(__FILE__).'/Template/'.$className.'.class.php');
        $this->_tpl   =  new $className;
    }

     public function fetch($templateFile,$var,$charset,$varPrefix) {
         $this->_tpl->fetch($templateFile,$var,$charset,$varPrefix);
     }
}
?>