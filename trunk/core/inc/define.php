<?php

define('IS_CLI',     PHP_SAPI=='cli'? 1   :   0);
define('IS_CGI',     substr(PHP_SAPI, 0,3)=='cgi' ? 1 : 0 );
define('IS_WIN',     strstr(PHP_OS, 'WIN') ? 1 : 0 );
define('IS_LINUX',   strstr(PHP_OS, 'Linux') ? 1 : 0 );
define('IS_FREEBSD', strstr(PHP_OS, 'FreeBSD') ? 1 : 0 );
define('SECURE_PROTOCOL','https://');
define('NON_SECURE_PROTOCOL','http://');

if(isset($_SERVER))
{
	define('DOMAIN_NAME', $_SERVER['HTTP_HOST']);
	define('HTTP_PROTOCOL',!empty($_SERVER['HTTPS']) ? SECURE_PROTOCOL : NON_SECURE_PROTOCOL);
	define('SCRIPT_NAME',$_SERVER['SCRIPT_NAME']);
}else{
	define('DOMAIN_NAME', getenv('HTTP_HOST'));
	//define('HTTP_PROTOCOL',condition ? SECURE_PROTOCOL : NON_SECURE_PROTOCOL);
	define('SCRIPT_NAME',getenv('SCRIPT_NAME'));
}

$urlTemp   = DOMAIN_NAME.SCRIPT_NAME;
$urlSplit  = explode('/',$urlTemp);
$urlParts  = count($urlSplit);
$scriptUrl = str_replace($urlSplit[$urlParts-1],'',$urlTemp);
$scriptUrl = substr($scriptUrl,0,-1);

define('URL',$scriptUrl);

define('LANG_PATH',      INC_PATH.'lang'.DS);
define('THEME_PATH',     ROOT_PATH.'themes'.DS);
define('COMPILE_PATH',   DATA_PATH.'compile'.DS);
define('CONFIG_PATH',    DATA_PATH.'configs'.DS);
define('CACHE_PATH',     DATA_PATH.'cache'.DS);
define('COOKIE_PATH',    DATA_PATH.'cookie'.DS);
define('SESSION_PATH',   DATA_PATH.'sessions'.DS);
define('LOG_PATH',       DATA_PATH.'logs'.DS);
define('UPLOAD_PATH',    DATA_PATH.'uploads'.DS);

?>
