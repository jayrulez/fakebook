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
 * ThinkPHP 视图输出
 * 支持缓存和页面压缩
 +------------------------------------------------------------------------------
 * @category   Think
 * @package  Think
 * @subpackage  Core
 * @author liu21st <liu21st@gmail.com>
 * @version  $Id$
 +------------------------------------------------------------------------------
 */
class View extends Base
{
    /**
     +----------------------------------------------------------
     * 模板页面显示变量，未经定义的变量不会显示在页面中
     +----------------------------------------------------------
     * @var array
     * @access protected
     +----------------------------------------------------------
     */
    protected $tVar        =  array();

    protected $trace       = array();

    // 模板引擎实例
    protected $template =  null;

   /**
     +----------------------------------------------------------
     * 取得模板对象实例
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @return View
     +----------------------------------------------------------
     */
    static function getInstance() {
        return get_instance_of(__CLASS__);
    }

    // 构造函数
    public function __construct() {
        $this->template   =  Template::getInstance();
    }

    /**
     +----------------------------------------------------------
     * 模板变量赋值
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param mixed $name
     * @param mixed $value
     +----------------------------------------------------------
     */
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

    /**
     +----------------------------------------------------------
     * Trace变量赋值
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param mixed $name
     * @param mixed $value
     +----------------------------------------------------------
     */
    public function trace($title,$value='') {
        if(is_array($title)) {
            $this->trace   =  array_merge($this->trace,$title);
        }else {
            $this->trace[$title] = $value;
        }
    }

    /**
     +----------------------------------------------------------
     * 取得模板变量的值
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $name
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
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

    /**
     +----------------------------------------------------------
     * 加载模板和页面输出 可以返回输出内容
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $templateFile 模板文件名 留空为自动获取
     * @param string $charset 模板输出字符集
     * @param string $contentType 输出类型
     * @param string $varPrefix 模板变量前缀
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    public function display($templateFile='',$charset='',$contentType='text/html',$varPrefix='')
    {
        $this->fetch($templateFile,$charset,$contentType,$varPrefix,true);
    }

    /**
     +----------------------------------------------------------
     * 加载模板和页面输出
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $templateFile 模板文件名 留空为自动获取
     * @param string $charset 模板输出字符集
     * @param string $contentType 输出类型
     * @param string $varPrefix 模板变量前缀
     * @param integer $mode 0 返回 1 输出 2 下载
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
    public function fetch($templateFile='',$charset='',$contentType='text/html',$varPrefix='',$display=false,$htmlCache=true)
    {
        $this->_init();
        if(null===$templateFile) {
            // 使用null参数作为模版名直接返回不做任何输出
            return ;
        }
        if('layout::'==substr($templateFile,0,8)) {
            $this->layout(substr($templateFile,8));
            return ;
        }
        if(empty($charset)) {
            $charset = C('OUTPUT_CHARSET');
        }
        // 网页字符编码
        header("Content-Type:".$contentType."; charset=".$charset);
        header("Cache-control: private");  //支持页面回跳

        // 设置输出缓存
        ini_set('output_buffering',4096);
        $zlibCompress   =  ini_get('zlib.output_compression');
        if(empty($zlibCompress) && function_exists('ini_set')) {
            ini_set( 'zlib.output_compression', 1 );
        }
        $pluginOn   =  C('THINK_PLUGIN_ON');
        if($pluginOn) {
            // 缓存初始化过滤
            apply_filter('ob_init');
        }
        //页面缓存
        ob_start();
        ob_implicit_flush(0);
        if($pluginOn) {
            // 缓存开启后执行的过滤
            apply_filter('ob_start');
            // 模版文件名过滤
            $templateFile = apply_filter('template_file',$templateFile);
        }

        if(!file_exists_case($templateFile)){
            // 自动定位模板文件
            $templateFile   = $this->parseTemplateFile($templateFile);
        }

        if($pluginOn) {
            // 模版变量过滤
            $this->tVar = apply_filter('template_var',$this->tVar);
        }
        // 模板引擎解析和输出
        $this->template->fetch($templateFile,$this->tVar,$charset,$varPrefix);
        // 获取并清空缓存
        $content = ob_get_clean();
        // 解析特殊路径变量
        $content = $this->parseTemplatePath($content);
        // 输出编码转换
        $content = auto_charset($content,C('TEMPLATE_CHARSET'),$charset);
        if($pluginOn) {
            // 输出过滤
            $content = apply_filter('ob_content',$content);
        }
        // 输出模板文件
        return $this->output($content,$display,$charset,$htmlCache);
    }

    /**
     +----------------------------------------------------------
     * 输出布局模板
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $layout 指定要输出的布局模板
     * @param string $charset 输出编码
     * @param string $contentType 输出类型
     * @param string $varPrefix 模板变量前缀
     * @param string $display 是否直接显示
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
    public function layout($layoutFile='',$charset='',$contentType='text/html',$varPrefix='',$display=true)
    {
        $this->_init();
        if(empty($layoutFile)) {
            // 留空获取默认配置的布局模板文件
            $layoutFile  =  C('DEFAULT_LAYOUT');
        }
        if(false === strpos($layoutFile,':')) {
            // 默认获取layout下面的布局文件
            // 如果不是需要另外指定 例如 Public:index
            $layoutFile  =  'Layout:'.$layoutFile;
        }
        // 获取布局模板文件
        $content    =   $this->fetch($layoutFile,$charset,$contentType,$varPrefix,false,false);
        // 查找布局包含的页面
        $find = preg_match_all('/<!-- layout::(.+?)::(.+?) -->/is',$content,$matches);
        if($find) {
            for ($i=0; $i< $find; $i++) {
                // 读取相关的页面模板替换布局单元
                if(0===strpos($matches[1][$i],'$')){
                    // 动态布局
                    $matches[1][$i]  =  $this->get(substr($matches[1][$i],1));
                }
                if(0 != $matches[2][$i] ) {
                    // 设置了布局缓存
                    // 检查布局缓存是否有效
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

        // 输出模板文件
        return $this->output($content,$display,$charset);
    }

    /**
     +----------------------------------------------------------
     * 输出模板
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param string $content 模板内容
     * @param boolean $display 是否直接显示
     * @param string $charset 输出编码
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
     */
    protected function output($content,$display,$charset,$htmlCache=true) {
        if(C('HTML_CACHE_ON') && $htmlCache) {
            // 写入静态文件
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

    /**
     +----------------------------------------------------------
     * 解析模板文件里面的特殊路径字符串
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @param string $content 模板内容
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    private function parseTemplatePath($content) {
        // 特殊变量替换
        //项目公共目录
        $content = str_replace(
            array('../Public',   '__PUBLIC__',  '__TMPL__', '__ROOT__',  '__APP__',  '__URL__',   '__ACTION__', '__SELF__'),
            array(APP_PUBLIC_URL,WEB_PUBLIC_URL,APP_TMPL_URL,__ROOT__,__APP__,__URL__,__ACTION__,__SELF__),
            $content);
        if(C('THINK_PLUGIN_ON')) {
            // 模版过滤插件调用
            $content =  apply_filter('tmpl_replace',$content);
        }
        return $content;
    }

    /**
     +----------------------------------------------------------
     * 自动定位模板文件
     +----------------------------------------------------------
     * @access private
     +----------------------------------------------------------
     * @param string $templateFile 文件名
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     * @throws ThinkExecption
     +----------------------------------------------------------
     */
    private function parseTemplateFile($templateFile) {
        if(''==$templateFile) {
            // 如果模板文件名为空 按照默认规则定位
            $templateFile = C('TMPL_FILE_NAME');
        }elseif(strpos($templateFile,'#')){
            // 引入组件的其他模块的操作模板 例如 User#Info:add
            $templateFile   =   LIB_PATH.str_replace(array('#',':'),array('/'.THEMES_DIR.'/'.TEMPLATE_NAME.'/','/'),$templateFile).C('TEMPLATE_SUFFIX');
        }elseif(strpos($templateFile,'@')){
            // 引入其它主题的操作模板 必须带上模块名称 例如 blue@User:add
            $templateFile   =   TMPL_PATH.str_replace(array('@',':'),'/',$templateFile).C('TEMPLATE_SUFFIX');
        }elseif(strpos($templateFile,':')){
            // 引入其它模块的操作模板
            $templateFile   =   TEMPLATE_PATH.'/'.str_replace(':','/',$templateFile).C('TEMPLATE_SUFFIX');
        }elseif(!is_file($templateFile))    {
            // 引入当前模块的其它操作模板
            $templateFile =  dirname(C('TMPL_FILE_NAME')).'/'.$templateFile.C('TEMPLATE_SUFFIX');
        }
        if(!file_exists_case($templateFile)){
            throw_exception(L('_TEMPLATE_NOT_EXIST_').'['.$templateFile.']');
        }
        return $templateFile;
    }

    /**
     +----------------------------------------------------------
     * 显示运行时间、数据库操作、缓存次数、内存使用信息
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @return string
     +----------------------------------------------------------
     */
    protected function showTime() {
        if(C('SHOW_RUN_TIME')) {
            // 显示运行时间
            $startTime =  $GLOBALS['_viewStartTime'];
            $endTime = microtime(TRUE);
            $total_run_time =   number_format(($endTime - $GLOBALS['_beginTime']), 3);
            $showTime   =   'Process: '.$total_run_time.'s ';
            if(C('SHOW_ADV_TIME')) {
                // 显示详细运行时间
                $_load_time =   number_format(($GLOBALS['_loadTime'] -$GLOBALS['_beginTime'] ), 3);
                $_init_time =   number_format(($GLOBALS['_initTime'] -$GLOBALS['_loadTime'] ), 3);
                $_exec_time =   number_format(($startTime  -$GLOBALS['_initTime'] ), 3);
                $_parse_time    =   number_format(($endTime - $startTime), 3);
                $showTime .= '( Load:'.$_load_time.'s Init:'.$_init_time.'s Exec:'.$_exec_time.'s Template:'.$_parse_time.'s )';
            }
            if(C('SHOW_DB_TIMES') && class_exists('Db',false) ) {
                // 显示数据库操作次数
                $db =   Db::getInstance();
                $showTime .= ' | DB :'.$db->Q().' queries '.$db->W().' writes ';
            }
            if(C('SHOW_CACHE_TIMES') && class_exists('Cache',false)) {
                // 显示缓存读写次数
                $cache  =   Cache::getInstance();
                $showTime .= ' | Cache :'.$cache->Q().' gets '.$cache->W().' writes ';
            }
            if(MEMORY_LIMIT_ON && C('SHOW_USE_MEM')) {
                // 显示内存开销
                $startMem    =  array_sum(explode(' ', $GLOBALS['_startUseMems']));
                $endMem     =  array_sum(explode(' ', memory_get_usage()));
                $showTime .= ' | UseMem:'. number_format(($endMem - $startMem)/1024).' kb';
            }
            return $showTime;
        }
    }

    /**
     +----------------------------------------------------------
     * 显示页面Trace信息
     +----------------------------------------------------------
     * @access protected
     +----------------------------------------------------------
     * @param string $showTime 运行时间信息
     * @param string $charset 模板输出字符集
     * @param boolean $compiler 是否重新编译
     +----------------------------------------------------------
     */
    protected function showTrace($showTime,$charset,$compiler=true){
        if(C('SHOW_PAGE_TRACE')) {
            // 显示页面Trace信息 读取Trace定义文件
            // 定义格式 return array('当前页面'=>$_SERVER['PHP_SELF'],'通信协议'=>$_SERVER['SERVER_PROTOCOL'],...);
            $traceFile  =   CONFIG_PATH.'trace.php';
             if(file_exists_case($traceFile)) {
                $_trace =   include $traceFile;
             }else{
                $_trace =   array();
             }
             // 系统默认显示信息
            $this->trace('当前页面',    $_SERVER['PHP_SELF']);
            $this->trace('请求方法',    $_SERVER['REQUEST_METHOD']);
            $this->trace('通信协议',    $_SERVER['SERVER_PROTOCOL']);
            $this->trace('请求时间',    date('Y-m-d H:i:s',$_SERVER['REQUEST_TIME']));
            $this->trace('用户代理',    $_SERVER['HTTP_USER_AGENT']);
            $this->trace('会话ID'   ,   session_id());
            $this->trace('运行数据',    $showTime);
            $this->trace('输出编码',    $charset);
            $this->trace('加载类库',    count($GLOBALS['import_file']));
            $log    =   Log::$log;
            $this->trace('日志信息',count($log)?count($log).'条日志<br/>'.implode('<br/>',$log):'无日志记录');
            $_trace =   array_merge($_trace,$this->trace);
            $_trace = auto_charset($_trace,'utf-8');
            $_title =   auto_charset('页面Trace信息','utf-8');
            // 调用Trace页面模板
            include CORE_PATH.'/Tpl/PageTrace.tpl.php';
        }
    }
}//
?>