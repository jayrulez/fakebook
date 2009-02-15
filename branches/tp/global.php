<?php

if(!defined('IN_APP'))
{
	exit();
}

define('INC_PATH',CORE_PATH.DS.'Inc'.DS);
define('API_PATH',CORE_PATH.DS.'Api'.DS);

$GLOBALS['_beginTime'] = microtime(TRUE);

require_once(INC_PATH.'define.php');
require_once(INC_PATH.'common.php');
require_once(INC_PATH.'compat.php');
require_once(INC_PATH.'runtime.php');

if(is_file(RUNTIME_PATH.'~runtime.php') && filemtime(RUNTIME_PATH.'~runtime.php')>filemtime(ROOT_PATH.DS.'global.php')) {
    // 加载框架核心缓存文件
    // 如果有修改核心文件请删除该缓存
    require RUNTIME_PATH.'~runtime.php';
}else{
    if(!is_dir(RUNTIME_PATH)) {
        // 创建项目目录结构
        buildAppDir();
    }

    //加载ThinkPHP基类
    import("Think.Core.Base");
    //加载异常处理类
    import("Think.Exception.ThinkException");
    // 加载日志类
    import("Think.Util.Log");
    //加载Think核心类
    import("Think.Core.App");
    import("Think.Core.Action");
    import("Think.Core.Model");
    import("Think.Core.View");
    // 是否生成核心缓存
    $cache  =   ( !defined('CACHE_RUNTIME') || CACHE_RUNTIME == true );
    if($cache) {
        if(defined('STRIP_RUNTIME_SPACE') && STRIP_RUNTIME_SPACE == false ) {
            $fun    =   'file_get_contents';
        }else{
            $fun    =   'php_strip_whitespace';
        }
        // 生成核心文件的缓存 去掉文件空白以减少大小
        $content    =   $fun(CORE_PATH.'/Lib/Think/Core/Base.class.php');
        $content    .=   $fun(CORE_PATH.'/Lib/Think/Exception/ThinkException.class.php');
        $content    .=   $fun(CORE_PATH.'/Lib/Think/Util/Log.class.php');
        $content    .=   $fun(CORE_PATH.'/Lib/Think/Core/App.class.php');
        $content    .=   $fun(CORE_PATH.'/Lib/Think/Core/Action.class.php');
        $content    .=   $fun(CORE_PATH.'/Lib/Think/Core/Model.class.php');
        $content    .=   $fun(CORE_PATH.'/Lib/Think/Core/View.class.php');

        file_put_contents(RUNTIME_PATH.'~runtime.php',$content);
        unset($content);
    }
}
// 记录加载文件时间
$GLOBALS['_loadTime'] = microtime(TRUE);
?>