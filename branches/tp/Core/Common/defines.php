<?php
// +----------------------------------------------------------------------
// | ThinkPHP
// +----------------------------------------------------------------------
// | Copyright (c) 2008 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$

/**
 +------------------------------------------------------------------------------
 * 系统定义文件
 +------------------------------------------------------------------------------
 * @category   Think
 * @package  Common
 * @author   liu21st <liu21st@gmail.com>
 * @version  $Id$
 +------------------------------------------------------------------------------
 */
if (!defined('CORE_PATH')) exit();
//   系统信息
if(version_compare(PHP_VERSION,'6.0.0','<') ) {
    @set_magic_quotes_runtime (0);
    define('MAGIC_QUOTES_GPC',get_magic_quotes_gpc()?True:False);
}
define('MEMORY_LIMIT_ON',function_exists('memory_get_usage')?true:false);
// 记录内存初始使用
if(MEMORY_LIMIT_ON) {
     $GLOBALS['_startUseMems'] = memory_get_usage();
}
define('IS_CLI',PHP_SAPI=='cli'? 1   :   0);
define('IS_CGI',substr(PHP_SAPI, 0,3)=='cgi' ? 1 : 0 );
define('IS_WIN',strstr(PHP_OS, 'WIN') ? 1 : 0 );
define('IS_LINUX',strstr(PHP_OS, 'Linux') ? 1 : 0 );
define('IS_FREEBSD',strstr(PHP_OS, 'FreeBSD') ? 1 : 0 );

if(!IS_CLI) {
    // 当前文件名
    if(!defined('_PHP_FILE_')) {
        if(IS_CGI) {
            //CGI/FASTCGI模式下
            $_temp  = explode('.php',$_SERVER["PHP_SELF"]);
            define('_PHP_FILE_',  rtrim(str_replace($_SERVER["HTTP_HOST"],'',$_temp[0].'.php'),DS));
        }else {
            define('_PHP_FILE_',    rtrim($_SERVER["SCRIPT_NAME"],DS));
        }
    }
    if(!defined('WEB_URL')) {
        // 网站URL根目录
        if( strtoupper(APP_NAME) == strtoupper(basename(dirname(_PHP_FILE_))) ) {
            $_root = dirname(dirname(_PHP_FILE_));
        }else {
            $_root = dirname(_PHP_FILE_);
        }
        define('WEB_URL',   (($_root==DS || $_root=='\\')?'':$_root));
    }

    //支持的URL模式
    define('URL_COMMON',      0);   //普通模式
    define('URL_PATHINFO',    1);   //PATHINFO模式
    define('URL_REWRITE',     2);   //REWRITE模式
    define('URL_COMPAT',        3);     // 兼容模式
}
// 目录设置
define('THINK_DIR','Think');
define('COMMON_DIR','Common');
define('CONFIG_DIR',    'Config');
define('LIB_DIR',        'Lib');
define('CONTROLLER_DIR','Action');
define('MODEL_DIR','Model');
define('LOG_DIR',      'Logs');
define('THEMES_DIR',     'Themes');
define('CACHE_DIR',  'Cache');
define('LANG_DIR',    'Lang');
define('RUNTIME_DIR',    'Runtime');
define('MODEL_DATA_DIR','Model_Data');
define('HTML_DIR',    'Html');
define('UPLOAD_DIR','Uploads');
define('PLUGIN_DIR','PlugIns');
define('VENDOR_DIR','Vendor');
// 路径设置
define('THINK_PATH',CORE_PATH.DS.LIB_DIR.DS.THINK_DIR.DS);
define('TMPL_PATH',APP_PATH.DS.THEMES_DIR.DS);
define('PLUGIN_PATH', APP_PATH.DS.PLUGIN_DIR.DS);
define('COMMON_PATH',   APP_PATH.DS.COMMON_DIR.DS); 
define('LIB_PATH',         APP_PATH.DS.LIB_DIR.DS);
define('CONTROLLER_PATH',  APP_PATH.DS.CONTROLLER_DIR.DS);
define('MODEL_PATH',  APP_PATH.DS.MODEL_DIR.DS);
define('CONFIG_PATH',  APP_PATH.DS.CONFIG_DIR.DS);
define('LANG_PATH',     APP_PATH.DS.LANG_DIR.DS);

define('LOG_PATH',       DATA_PATH.DS.LOG_DIR.DS);
define('HTML_PATH',DATA_PATH.DS.HTML_DIR.DS);
define('CACHE_PATH',   DATA_PATH.DS.CACHE_DIR.DS);
define('RUNTIME_PATH',      DATA_PATH.DS.RUNTIME_DIR.DS);
define('UPLOAD_PATH', DATA_PATH.DS.UPLOAD_DIR.DS);

define('MODEL_DATA_PATH', DATA_PATH.DS.MODEL_DATA_DIR.DS);

define('DATA_TYPE_OBJ',1);
define('DATA_TYPE_ARRAY',0);
define('VENDOR_PATH',CORE_PATH.DS.VENDOR_DIR.DS);
// 为了方便导入第三方类库 设置Vendor目录到include_path
set_include_path(get_include_path() . PATH_SEPARATOR . VENDOR_PATH);

//  版本信息
define('THINK_VERSION', '1.5.1beta r1086');
?>