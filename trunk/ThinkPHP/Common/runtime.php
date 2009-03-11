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

function mkdirs($dirs,$mode=0777) {
    if(is_string($dirs)) {
        $dirs  = explode(',',$dirs);
    }
    foreach ($dirs as $dir){
        if(!is_dir($dir))  mkdir($dir,$mode);
    }
}

// 创建项目目录结构
function buildAppDir() {
    // 没有创建项目目录的话自动创建
    if(!is_dir(APP_PATH)){
        mk_dir(APP_PATH,0777);
    }
    if(is_writeable(APP_PATH)) {
        mkdirs(array(
            LIB_PATH,
            CONFIG_PATH,
            COMMON_PATH,
            LANG_PATH,
            CACHE_PATH,
            TMPL_PATH,
            TMPL_PATH.'default/',
            LOG_PATH,
            TEMP_PATH,
            DATA_PATH,
            LIB_PATH.'Model/',
            LIB_PATH.'Action/',
            ));
        // 目录安全写入
        if(!defined('BUILD_DIR_SECURE')) define('BUILD_DIR_SECURE',false);
        if(BUILD_DIR_SECURE) {
            if(!defined('DIR_SECURE_FILENAME')) define('DIR_SECURE_FILENAME','index.html');
            if(!defined('DIR_SECURE_CONTENT')) define('DIR_SECURE_CONTENT',' ');
            // 自动写入目录安全文件
            $content        =   DIR_SECURE_CONTENT;
            $a = explode(',', DIR_SECURE_FILENAME);
            foreach ($a as $filename){
                file_put_contents(LIB_PATH.$filename,$content);
                file_put_contents(LIB_PATH.'Action/'.$filename,$content);
                file_put_contents(LIB_PATH.'Model/'.$filename,$content);
                file_put_contents(CACHE_PATH.$filename,$content);
                file_put_contents(LANG_PATH.$filename,$content);
                file_put_contents(TEMP_PATH.$filename,$content);
                file_put_contents(TMPL_PATH.$filename,$content);
                file_put_contents(TMPL_PATH.'default/'.$filename,$content);
                file_put_contents(DATA_PATH.$filename,$content);
                file_put_contents(COMMON_PATH.$filename,$content);
                file_put_contents(CONFIG_PATH.$filename,$content);
                file_put_contents(LOG_PATH.$filename,$content);
            }
        }
        // 写入测试Action
        if(!is_file(LIB_PATH.'Action/IndexAction.class.php')) {
            $content     =
'<?php
// 本类由系统自动生成，仅供测试用途
class IndexAction extends Action{
    public function index(){
        header("Content-Type:text/html; charset=utf-8");
        echo "<div>Hello,ThinkPHP</div>";
    }
}
?>';
            file_put_contents(LIB_PATH.'Action/IndexAction.class.php',$content);
        }
    }else{
        header("Content-Type:text/html; charset=utf-8");
        exit('<div>Create Folder Failed</div>');
    }
}
?>