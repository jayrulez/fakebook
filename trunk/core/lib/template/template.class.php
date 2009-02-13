<?php

import('vendor.smarty.Smarty');

class template extends Smarty
{
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
		
		parent::assign('static','http://'.DOMAIN_NAME.'/'.'static'.DS.'rsrc.php');
		parent::assign('pagename',PAGE_NAME);
		parent::assign('conf',$_conf);
		parent::assign('lang',$_lang);
		parent::display($tpl);
	}
}

?>
