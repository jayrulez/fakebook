<?php

class View extends Base
{
    protected $tVar        =  array();

    protected $trace       = array();

    protected $template =  null;

    static function getInstance() {
        return get_instance_of(__CLASS__);
    }

    public function __construct() {
        $this->template   =  Template::getInstance();
    }

    public function assign($name,$value=''){
        if(is_array($name)) {
            $this->tVar   =  array_merge($this->tVar,$name);
        }elseif(is_object($name)){
            foreach($name as $key =>$val)
            {
                $this->tVar[$key] = $val;
            }
        }else {
            $this->tVar[$name] = $value;
        }
    }

    public function trace($title,$value='') {
        if(is_array($title)) {
            $this->trace   =  array_merge($this->trace,$title);
        }else {
            $this->trace[$title] = $value;
        }
    }

    public function get($name){
        if(isset($this->tVar[$name])) {
            return $this->tVar[$name];
        }else {
            return false;
        }
    }

    public function __set($name,$value) {
        $this->assign($name,$value);
    }

    public function __get($name) {
        return $this->get($name);
    }

    protected function _init() {
        $GLOBALS['_viewStartTime'] = microtime(TRUE);
    }

    public function display($templateFile='',$charset='',$contentType='text/html',$varPrefix='')
    {
        $this->fetch($templateFile,$charset,$contentType,$varPrefix,true);
    }

    public function fetch($templateFile='',$charset='',$contentType='text/html',$varPrefix='',$display=false,$htmlCache=true)
    {
        $this->_init();
        if(null===$templateFile) {
            return ;
        }
        if('layout::'==substr($templateFile,0,8)) {
            $this->layout(substr($templateFile,8));
            return ;
        }
        if(empty($charset)) {
            $charset = C('OUTPUT_CHARSET');
        }
        header("Content-Type:".$contentType."; charset=".$charset);
        header("Cache-control: private");  

        ini_set('output_buffering',4096);
        $zlibCompress   =  ini_get('zlib.output_compression');
        if(empty($zlibCompress) && function_exists('ini_set')) {
            ini_set( 'zlib.output_compression', 1 );
        }
        $pluginOn   =  C('THINK_PLUGIN_ON');
        if($pluginOn) {
            apply_filter('ob_init');
        }
        ob_start();
        ob_implicit_flush(0);
        if($pluginOn) {
            apply_filter('ob_start');
            $templateFile = apply_filter('template_file',$templateFile);
        }

        if(!file_exists_case($templateFile)){
            $templateFile   = $this->parseTemplateFile($templateFile);
        }

        if($pluginOn) {
            $this->tVar = apply_filter('template_var',$this->tVar);
        }
        $this->template->fetch($templateFile,$this->tVar,$charset,$varPrefix);

        $content = ob_get_clean();

        $content = $this->parseTemplatePath($content);

        $content = auto_charset($content,C('TEMPLATE_CHARSET'),$charset);
        if($pluginOn) {
            $content = apply_filter('ob_content',$content);
        }
        return $this->output($content,$display,$charset,$htmlCache);
    }

    public function layout($layoutFile='',$charset='',$contentType='text/html',$varPrefix='',$display=true)
    {
        $this->_init();
        if(empty($layoutFile)) {
            $layoutFile  =  C('DEFAULT_LAYOUT');
        }
        if(false === strpos($layoutFile,':')) {
            $layoutFile  =  'Layout:'.$layoutFile;
        }
        $content    =   $this->fetch($layoutFile,$charset,$contentType,$varPrefix,false,false);
        $find = preg_match_all('/<!-- layout::(.+?)::(.+?) -->/is',$content,$matches);
        if($find) {
            for ($i=0; $i< $find; $i++) {
                if(0===strpos($matches[1][$i],'$')){
                    $matches[1][$i]  =  $this->get(substr($matches[1][$i],1));
                }
                if(0 != $matches[2][$i] ) {
                    $guid =  md5($matches[1][$i]);
                    $cache  =  S($guid);
                    if($cache) {
                        $layoutContent = $cache;
                    }else{
                        $layoutContent = $this->fetch($matches[1][$i],$charset,$contentType,$varPrefix,false,false);
                        S($guid,$layoutContent,$matches[2][$i]);
                    }
                }else{
                    $layoutContent = $this->fetch($matches[1][$i],$charset,$contentType,$varPrefix,false,false);
                }
                $content    =   str_replace($matches[0][$i],$layoutContent,$content);
            }
        }

        return $this->output($content,$display,$charset);
    }

    protected function output($content,$display,$charset,$htmlCache=true) {
        if(C('HTML_CACHE_ON') && $htmlCache) {
            HtmlCache::writeHTMLCache($content);
        }

        if($display) {
            $showTime   =   $this->showTime();
            echo $content;
            if(C('SHOW_RUN_TIME')) {
                echo '<div  id="think_run_time" class="think_run_time">'.$showTime.'</div>';
            }
            $this->showTrace($showTime,$charset);
            return null;
        }else {
            return $content;
        }
    }

    private function parseTemplatePath($content) {
        $content = str_replace(
            array('../Public',   '__PUBLIC__',  '__TMPL__', '__ROOT__',  '__APP__',  '__URL__',   '__ACTION__', '__SELF__'),
            array(APP_PUBLIC_URL,WEB_PUBLIC_URL,APP_TMPL_URL,__ROOT__,__APP__,__URL__,__ACTION__,__SELF__),
            $content);
        if(C('THINK_PLUGIN_ON')) {
            $content =  apply_filter('tmpl_replace',$content);
        }
        return $content;
    }

    private function parseTemplateFile($templateFile) {
        if(''==$templateFile) {
            $templateFile = C('TMPL_FILE_NAME');
        }elseif(strpos($templateFile,'#')){
            $templateFile   =   LIB_PATH.str_replace(array('#',':'),array('/'.THEMES_DIR.'/'.TEMPLATE_NAME.'/','/'),$templateFile).C('TEMPLATE_SUFFIX');
        }elseif(strpos($templateFile,'@')){
            $templateFile   =   TMPL_PATH.str_replace(array('@',':'),'/',$templateFile).C('TEMPLATE_SUFFIX');
        }elseif(strpos($templateFile,':')){
            $templateFile   =   TEMPLATE_PATH.'/'.str_replace(':','/',$templateFile).C('TEMPLATE_SUFFIX');
        }elseif(!is_file($templateFile))
		{
            $templateFile =  dirname(C('TMPL_FILE_NAME')).'/'.$templateFile.C('TEMPLATE_SUFFIX');
        }
        if(!file_exists_case($templateFile)){
            throw_exception(L('_TEMPLATE_NOT_EXIST_').'['.$templateFile.']');
        }
        return $templateFile;
    }

    protected function showTime() {
        if(C('SHOW_RUN_TIME')) {
            $startTime =  $GLOBALS['_viewStartTime'];
            $endTime = microtime(TRUE);
            $total_run_time =   number_format(($endTime - $GLOBALS['_beginTime']), 3);
            $showTime   =   'Process: '.$total_run_time.'s ';
            if(C('SHOW_ADV_TIME')) {
                $_load_time =   number_format(($GLOBALS['_loadTime'] -$GLOBALS['_beginTime'] ), 3);
                $_init_time =   number_format(($GLOBALS['_initTime'] -$GLOBALS['_loadTime'] ), 3);
                $_exec_time =   number_format(($startTime  -$GLOBALS['_initTime'] ), 3);
                $_parse_time    =   number_format(($endTime - $startTime), 3);
                $showTime .= '( Load:'.$_load_time.'s Init:'.$_init_time.'s Exec:'.$_exec_time.'s Template:'.$_parse_time.'s )';
            }
            if(C('SHOW_DB_TIMES') && class_exists('Db',false) ) {
                $db =   Db::getInstance();
                $showTime .= ' | DB :'.$db->Q().' queries '.$db->W().' writes ';
            }
            if(C('SHOW_CACHE_TIMES') && class_exists('Cache',false)) {
                $cache  =   Cache::getInstance();
                $showTime .= ' | Cache :'.$cache->Q().' gets '.$cache->W().' writes ';
            }
            if(MEMORY_LIMIT_ON && C('SHOW_USE_MEM')) {
                $startMem    =  array_sum(explode(' ', $GLOBALS['_startUseMems']));
                $endMem     =  array_sum(explode(' ', memory_get_usage()));
                $showTime .= ' | UseMem:'. number_format(($endMem - $startMem)/1024).' kb';
            }
            return $showTime;
        }
    }

    protected function showTrace($showTime,$charset,$compiler=true){
        if(C('SHOW_PAGE_TRACE')) {
            $traceFile  =   CONFIG_PATH.'trace.php';
             if(file_exists_case($traceFile)) {
                $_trace =   include $traceFile;
             }else{
                $_trace =   array();
             }
            $this->trace(L('_TRACE_CURRENT_PAGE_'),    $_SERVER['PHP_SELF']);
            $this->trace(L('_TRACE_REQUEST_METHOD_'),    $_SERVER['REQUEST_METHOD']);
            $this->trace(L('_TRACE_SERVER_PROTOCOL_'),    $_SERVER['SERVER_PROTOCOL']);
            $this->trace(L('_TRACE_REQUEST_TIME_'),    date('Y-m-d H:i:s',$_SERVER['REQUEST_TIME']));
            $this->trace(L('_TRACE_USER_AGENT_'),    $_SERVER['HTTP_USER_AGENT']);
            $this->trace(L('_TRACE_SESSION_ID_')   ,   session_id());
            $this->trace(L('_TRACE_OPERATION_DATA_'),    $showTime);
            $this->trace(L('_TRACE_OUTPUT_ENCODING_'),    $charset);
            $this->trace(L('_TRACE_IMPORTED_FILES_'),    count($GLOBALS['import_file']));
            $log    =   Log::$log;
            $this->trace(L('_TRACE_LOG_INFORMATION_'),count($log)?count($log).L('_TRACE_ARTICLE_LOG_').'<br/>'.implode('<br/>',$log):L('_TRACE_LOG_NO_INFORMATION_'));
            $_trace =   array_merge($_trace,$this->trace);
            $_trace = auto_charset($_trace,'utf-8');
            $_title =   auto_charset(L('_TRACE_TITLE_'),'utf-8');
            include CORE_PATH.'Tpl/PageTrace.tpl.php';
        }
    }
}

?>