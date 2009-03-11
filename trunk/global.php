<?php

$GLOBALS['_beginTime'] = microtime(TRUE);

$GLOBALS['import_file'] =  array();

if(!defined('CORE_PATH')) define('CORE_PATH', dirname(__FILE__).'Core'.DS);

define('INC_PATH',CORE_PATH.'Inc'.DS);

require(INC_PATH.'defines.php');
require(INC_PATH.'common.php');
require(INC_PATH.'compat.php');
require(INC_PATH.'runtime.php');

if(is_file(RUNTIME_PATH.'~runtime.php') && filemtime(RUNTIME_PATH.'~runtime.php')>filemtime(ROOT_PATH.'global.php'))
{
    require RUNTIME_PATH.'~runtime.php';
}else{

    if(!is_dir(RUNTIME_PATH)) {
        buildAppDir();
    }

    import("Think.Core.Base");
    import("Think.Exception.ThinkException");
    import("Think.Util.Log");
    import("Think.Core.App");
    import("Think.Core.Action");
    import("Think.Core.Model");
    import("Think.Core.View");

    $cache  =   ( !defined('CACHE_RUNTIME') || CACHE_RUNTIME == true );
    if($cache) {
        if(defined('STRIP_RUNTIME_SPACE') && STRIP_RUNTIME_SPACE == false ) {
            $do    =   'file_get_contents';
        }else{
            $do    =   'php_strip_whitespace';
        }
		
        $content     =   $do(CORE_PATH.'Lib/Think/Core/Base.class.php');
        $content    .=   $do(CORE_PATH.'Lib/Think/Exception/ThinkException.class.php');
        $content    .=   $do(CORE_PATH.'Lib/Think/Util/Log.class.php');
        $content    .=   $do(CORE_PATH.'Lib/Think/Core/App.class.php');
        $content    .=   $do(CORE_PATH.'Lib/Think/Core/Action.class.php');
        $content    .=   $do(CORE_PATH.'Lib/Think/Core/Model.class.php');
        $content    .=   $do(CORE_PATH.'Lib/Think/Core/View.class.php');

        file_put_contents(RUNTIME_PATH.'~runtime.php',$content);
        unset($content);
    }
}

$GLOBALS['_loadTime'] = microtime(true);

?>