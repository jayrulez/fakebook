<?php

function data_conf_vars()
{
	return array
	(
		'xml_encoding'		=>	C('XML_ENCODING'),
		'output_content_type'	=>	C('OUTPUT_CONTENT_TYPE'),
		'output_charset'	=>	C('OUTPUT_CHARSET'),
		'lang_id'		=>	C('LANG_ID'),
	);
}

function tpl_conf_vars($conf_array=array())
{
	$_confvars = array();
	$_conf_map = data_conf_vars();

	if(empty($conf_array))
	{
		$conf_array = include(INC_PATH.'config.php');
	}
	$_confvars = $conf_array;
	$_confvars = array_merge($_confvars,$_conf_map);
	return $_confvars;
}

function tpl_lang_vars($lang_array=array())
{
	if(empty($lang_array))
	{
		$lang_array = L();
	}
	$_langvars = $lang_array;
	return $_langvars;
}

function get_cookie_domain()
{
	if(C('GLOBAL_COOKIE_ON'))
	{
		$domain = '.'.DOMAIN_NAME;
	}else{
		$domain = DOMAIN_NAME;
	}
	return $domain;
}

function throw_exception($msg,$type='myException',$code=0)
{
	if(class_exists($type,false))
	{
		throw new $type($msg,$code,true);
	}else{
		halt($msg);
	}
}

function halt($error)
{

}

function start_app()
{
	import('lib.util.syserror');
	$syserror = new syserror();

	C(include(INC_PATH.'system.php'));
	C(include(INC_PATH.'config.php'));
	import('lib.util.cookie');
	checkLanguage();
	checkTemplate();
}

function file_exists_case($filename)
{
	if(is_file($filename))
	{
		if(IS_WIN && C('CHECK_FILE_CASE'))
		{
			if(basename(realpath($filename)) != basename($filename))
			{
				return false;
			}
		}
		return true;
	}
	return false;
}


function checkTemplate()
{
	$defaultTmpl = C('DEFAULT_TMPL');

	if(C('TMPL_SWITCH_ON'))
	{
		if(C('AUTO_DETECT_TMPL'))
		{
			$t = C('VAR_TMPL');

			if ( isset($_GET[$t]) )
			{
				$templateSet = $_GET[$t];
				Cookie::set('_template',$templateSet,time()+3600);
			} else {
				if(Cookie::is_set('_template'))
				{
					$templateSet = Cookie::get('_template');
				}else {
					$templateSet = $defaultTmpl;
					Cookie::set('_template',$templateSet,time()+3600);
				}
			}
			if(!is_dir(THEME_PATH.$templateSet))
			{
				$templateSet = $defaultTmpl;
			}
		}else{
			$templateSet = $defaultTmpl;
		}

		define('TMPL_NAME',$templateSet);
		define('TMPL_PATH',THEME_PATH.TMPL_NAME.DS.'templates');
	}else{
		define('TMPL_NAME',$defaultTmpl);
		define('TMPL_PATH',THEME_PATH.$defaultTmpl.DS.'templates');
	}

}

function checkLanguage()
{
	$defaultLang = C('DEFAULT_LANG');
	$defaultLangId = C('DEFAULT_LANG_ID');

	if(C('LANG_SWITCH_ON'))
	{
		if(C('AUTO_DETECT_LANG'))
		{
			if(isset($_GET[C('VAR_LANG')]))
			{
				$langSet = $_GET[C('VAR_LANG')];
				Cookie::set('_language',$langSet,time()+3600);
			}elseif(Cookie::is_set('_language'))
			{
				$langSet = Cookie::get('_language');
			}else{
				if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
				{
					preg_match('/^([a-z\-]+)/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches);
					$langSet = $matches[1];
					Cookie::set('_language',$langSet,time()+3600);
				}else{
					$langSet = $defaultLang;
				}
			}
		}else{
			$langSet = $defaultLang;
		}

		define('LANG_SET',$langSet);

		if(C('LANG_CACHE_ON') && is_file(CACHE_PATH.PAGE_NAME.'_'.LANG_SET.'_lang.php'))
		{
			L(include(CACHE_PATH.PAGE_NAME.'_'.LANG_SET.'_lang.php'));
		}else{
			if(file_exists_case(LANG_PATH.LANG_SET.'.php'))
			{
				L(include(LANG_PATH.LANG_SET.'.php'));
			}else{
				L(include(LANG_PATH.$defaultLang.'.php'));
			}

			if(file_exists_case(LANG_PATH.LANG_SET.DS.'common.php'))
			{
				L(include(LANG_PATH.LANG_SET.DS.'common.php'));
			}

			if(file_exists_case(LANG_PATH.LANG_SET.DS.strtolower(PAGE_NAME).'.php'))
			{
				L(include(LANG_PATH.LANG_SET.DS.strtolower(PAGE_NAME).'.php'));
			}

			if(C('LANG_CACHE_ON'))
			{
				$content  = "<?php\r\nreturn ".var_export(L(),true).";\r\n?>";
				file_put_contents(CACHE_PATH.PAGE_NAME.'_'.LANG_SET.'_lang.php',$content);
			}
		}
		C('LANG_ID',substr(LANG_SET,0,2));
	}else{
		L(include(LANG_PATH.$defaultLang.'.php'));
	}
	return ;
}

function include_cache($filename)
{
	if(!isset($GLOBALS['import_file'][$filename]))
	{
		if(file_exists_case($filename))
		{
			include($filename);
			$GLOBALS['import_file'][$filename] = true;
		}else{
			$GLOBALS['import_file'][$filename] = false;
		}
	}
	return $GLOBALS['import_file'][$filename];
}

function require_cache($filename)
{
	if(!isset($GLOBALS['import_file'][$filename]))
	{
		if(file_exists_case($filename))
		{
			require($filename);
			$GLOBALS['import_file'][$filename] = true;
		}else{
			$GLOBALS['import_file'][$filename] = false;
		}
	}
	return $GLOBALS['import_file'][$filename];
}

function import($class,$baseUrl = '',$ext='.class.php',$subdir=false)
{
	static $_file  = array();
	static $_class = array();
	$class         = str_replace(array('.','#'), array(DS,'.'), $class);
	if(isset($_file[strtolower($class.$baseUrl)]))
	{
		return true;
	}else{
		$_file[strtolower($class.$baseUrl)] = true;
	}

	if(empty($baseUrl))
	{
		$baseUrl = CORE_PATH;
	}else{
		$isPath =  true;
	}

	$class_strut = explode(DS,$class);

	if(in_array(strtolower($class_strut[0]),array('lib','vendor')))
	{
		$baseUrl = CORE_PATH;
	}

	if(substr($baseUrl, -1) != DS)
	{
		$baseUrl .= DS;
	}

	$classfile = $baseUrl . $class . $ext;
	if(false !== strpos($classfile,'*') || false !== strpos($classfile,'?') )
	{
		$match = glob($classfile);
		if($match)
		{
			foreach($match as $key=>$val)
			{
				if(is_dir($val))
				{
					if($subdir)
					{
						import('*',$val.DS,$ext,$subdir);
					}
				}else{
					if($ext == '.class.php')
					{
						$class = basename($val,$ext);
						if(isset($_class[$class]))
						{
							throw_exception($class.L('_CLASS_CONFLICT_'));
						}
						$_class[$class] = $val;
					}
					require_cache($val);
				}
			}
			return true;
		}else{
			return false;
		}
	}else{
		if($ext == '.class.php' && is_file($classfile))
		{
			$class = basename($classfile,$ext);
			if(isset($_class[strtolower($class)]))
			{
				throw_exception(L('_CLASS_CONFLICT_').':'.$_class[strtolower($class)].' '.$classfile);
			}
			$_class[strtolower($class)] = $classfile;
		}
		return require_cache($classfile);
	}
}


function L($name='',$value=null)
{
	static $_lang = array();
	if(!is_null($value))
	{
		$_lang[strtolower($name)] = $value;
		return;
	}
	if(empty($name))
	{
		return $_lang;
	}
	if(is_array($name))
	{
		$_lang = array_merge($_lang,array_change_key_case($name));
		return;
	}
	if(isset($_lang[strtolower($name)]))
	{
		return $_lang[strtolower($name)];
	}else{
		return false;
	}
}


function C($name='',$value=null)
{
	static $_config = array();
	if(!is_null($value))
	{
		if(strpos($name,'.'))
		{
			$array                         = explode('.',strtolower($name));
			$_config[$array[0]][$array[1]] = $value;
		}else{
			$_config[strtolower($name)] =   $value;
		}
		return ;
	}

	if(empty($name))
	{
		return $_config;
	}

	if(is_array($name))
	{
		$_config = array_merge($_config,array_change_key_case($name));
		return $_config;
	}elseif(0===strpos($name,'?'))
	{
		$name = strtolower(substr($name,1));
		if(strpos($name,'.'))
		{
			$array = explode('.',$name);
			return isset($_config[$array[0]][$array[1]]);
		}else{
			return isset($_config[$name]);
		}
	}elseif(strpos($name,'.'))
	{
		$array = explode('.',strtolower($name));
		return $_config[$array[0]][$array[1]];
	}elseif(isset($_config[strtolower($name)]))
	{
		return $_config[strtolower($name)];
	}else{
		return null;
	}
}

?>
