<?php

function mkdirs($dirs,$mode=0777) {
    if(is_string($dirs)) {
        $dirs  = explode(',',$dirs);
    }
    foreach ($dirs as $dir){
        if(!is_dir($dir))  mkdir($dir,$mode);
    }
}

function buildAppDir() {
    if(!is_dir(APP_PATH)){
        mk_dir(APP_PATH,0777);
    }
    if(is_writeable(APP_PATH)) {
        mkdirs(array(
            LIB_PATH,
            LIB_PATH.'Model/',
            LIB_PATH.'Action/',
            CONFIG_PATH,
            COMMON_PATH,
            LANG_PATH,
            TMPL_PATH,
            //TMPL_PATH.'default/',
			DATA_PATH,
			APP_DATA_PATH,
            CACHE_PATH,
            LOG_PATH,
            RUNTIME_PATH,
            MODEL_DATA_PATH,
			HTML_PATH,
			UPLOAD_PATH,
		RESOURCE_DATA_PATH,
		));
	
        if(!defined('BUILD_DIR_SECURE')) define('BUILD_DIR_SECURE',false);
        if(BUILD_DIR_SECURE) {
            if(!defined('DIR_SECURE_FILENAME')) define('DIR_SECURE_FILENAME','index.html');
            if(!defined('DIR_SECURE_CONTENT')) define('DIR_SECURE_CONTENT',' ');

            $content        =   DIR_SECURE_CONTENT;
            $a = explode(',', DIR_SECURE_FILENAME);
            foreach ($a as $filename){
                file_put_contents(LIB_PATH.$filename,$content);
                file_put_contents(LIB_PATH.'Action/'.$filename,$content);
                file_put_contents(LIB_PATH.'Model/'.$filename,$content);
                file_put_contents(CACHE_PATH.$filename,$content);
                file_put_contents(LANG_PATH.$filename,$content);
                file_put_contents(RUNTIME_PATH.$filename,$content);
                file_put_contents(TMPL_PATH.$filename,$content);
                file_put_contents(TMPL_PATH.'default/'.$filename,$content);
                file_put_contents(MODEL_DATA_PATH.$filename,$content);
                file_put_contents(COMMON_PATH.$filename,$content);
                file_put_contents(CONFIG_PATH.$filename,$content);
                file_put_contents(LOG_PATH.$filename,$content);
            }
        }

        if(!is_file(LIB_PATH.'Action/IndexAction.class.php')) {
            $content     =
'<?php

class IndexAction extends Action
{
    public function index(){
	
	}
}

?>';
            file_put_contents(LIB_PATH.'Action/IndexAction.class.php',$content);
        }
    }else{
        header("Content-Type:text/html; charset=utf-8");
        exit('<div style=\'font-weight:bold;float:left;width:345px;text-align:center;border:1px solid silver;background:#E8EFFF;padding:8px;color:red;font-size:14px;font-family:Tahoma\'>Project directory not writable, the directory can not be generated automatically! Please use the item <br/> generator or manually generated project directory</div>');
    }
}

?>