<?php

if(!defined('DS'))
{
	define('DS',DIRECTORY_SEPARATOR);
}

if(!defined('APP_NAME')) define('APP_NAME', md5(CORE_PATH));
if(!defined('APP_PATH')) define('APP_PATH', dirname(CORE_PATH).APP_NAME);
if(!defined('APP_DATA_PATH')) define('APP_DATA_PATH',DATA_PATH);

if(version_compare(PHP_VERSION,'6.0.0','<') ) {
    @set_magic_quotes_runtime (0);
    define('MAGIC_QUOTES_GPC',get_magic_quotes_gpc()?true:false);
}
define('MEMORY_LIMIT_ON',function_exists('memory_get_usage')?true:false);

if(MEMORY_LIMIT_ON) {
     $GLOBALS['_startUseMems'] = memory_get_usage();
}
define('IS_CLI',PHP_SAPI=='cli'? 1   :   0);
define('IS_CGI',substr(PHP_SAPI, 0,3)=='cgi' ? 1 : 0 );
define('IS_WIN',strstr(PHP_OS, 'WIN') ? 1 : 0 );
define('IS_LINUX',strstr(PHP_OS, 'Linux') ? 1 : 0 );
define('IS_FREEBSD',strstr(PHP_OS, 'FreeBSD') ? 1 : 0 );

if(!IS_CLI) {
    if(!defined('_PHP_FILE_')) {
        if(IS_CGI) {
            $_temp  = explode('.php',$_SERVER["PHP_SELF"]);
            define('_PHP_FILE_',  rtrim(str_replace($_SERVER["HTTP_HOST"],'',$_temp[0].'.php'),'/'));
        }else {
            define('_PHP_FILE_',    rtrim($_SERVER["SCRIPT_NAME"],'/'));
        }
    }
    if(!defined('WEB_URL')) {
        if( strtoupper(APP_NAME) == strtoupper(basename(dirname(_PHP_FILE_))) ) {
            $_root = dirname(dirname(_PHP_FILE_));
        }else {
            $_root = dirname(_PHP_FILE_);
        }
        define('WEB_URL',   (($_root=='/' || $_root=='\\')?'':$_root));
    }

    define('URL_COMMON',      0);   //COMMON
    define('URL_PATHINFO',    1);   //PATHINFO
    define('URL_REWRITE',     2);   //REWRITE
    define('URL_COMPAT',      3);     //COMPAT
}

define('CACHE_DIR',  'Cache');
define('HTML_DIR',    'Html');
define('CONFIG_DIR',    'Config');
define('LIB_DIR',        'Lib');
define('LOG_DIR',      'Logs');
define('LANG_DIR',    'Lang');
define('RUNTIME_DIR',    'Runtime');
define('THEMES_DIR',     'Themes');
define('COMMON_DIR',     'Common');
define('UPLOAD_DIR',     'Uploads');
define('PLUGIN_DIR',     'PlugIns');
define('MODEL_DATA_DIR',     'Model_Data');
define('RESOURCE_DATA_DIR',    'Resource');
define('VENDOR_DIR',     'Vendor');

define('COMMON_PATH',   APP_PATH.COMMON_DIR.DS); 
define('LIB_PATH',         APP_PATH.LIB_DIR.DS); 
define('CONFIG_PATH',  APP_PATH.CONFIG_DIR.DS); 
define('LANG_PATH',     APP_PATH.LANG_DIR.DS); 
define('TMPL_PATH',APP_PATH.THEMES_DIR.DS);
define('PLUGIN_PATH', APP_PATH.PLUGIN_DIR.DS);

define('LOG_PATH',       APP_DATA_PATH.LOG_DIR.DS); 
define('HTML_PATH',    APP_DATA_PATH.HTML_DIR.DS); 
define('CACHE_PATH',   APP_DATA_PATH.CACHE_DIR.DS); 
define('RUNTIME_PATH',      APP_DATA_PATH.RUNTIME_DIR.DS); 
define('UPLOAD_PATH', APP_DATA_PATH.UPLOAD_DIR.DS);
define('MODEL_DATA_PATH', APP_DATA_PATH.MODEL_DATA_DIR.DS); 
define('RESOURCE_DATA_PATH',APP_DATA_PATH.RESOURCE_DATA_DIR.DS);

define('DATA_TYPE_OBJ',1);
define('DATA_TYPE_ARRAY',0);
define('VENDOR_PATH',CORE_PATH.DS.VENDOR_DIR.DS);

set_include_path(get_include_path() . PATH_SEPARATOR . VENDOR_PATH);

define('THINK_VERSION', '1.5.1beta r1173');

?>