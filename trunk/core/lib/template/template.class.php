<?php

import('vendor.smarty.Smarty');

class template extends Smarty
{
    static function getInstance()
    {
        return get_instance_of(__CLASS__);
    }
	
	public function __construct()
	{
		parent::__construct();

		$this->template_dir = TMPL_PATH;
		$this->compile_dir  = COMPILE_PATH;
		$this->config_dir   = CONFIG_PATH;
		$this->cache_dir    = CACHE_PATH;
	}

	public function display($tpl)
	{
		$_conf = tpl_conf_vars();
		$_lang = tpl_lang_vars();
		$_cbid = cssBrowserId();
		
		parent::assign('static',NON_SECURE_PROTOCOL.URL.'/'.'static'.'/'.'rsrc.php');
		parent::assign('cbid',$_cbid);
		parent::assign('pagename',PAGE_NAME);
		parent::assign('conf',$_conf);
		parent::assign('lang',$_lang);
		parent::assign('langset',LANG_SET);//for body class
		parent::assign('theme_path',NON_SECURE_PROTOCOL.URL.'/themes/'.TMPL_NAME);//for favicon path
		parent::display($tpl);
	}
}

?>
