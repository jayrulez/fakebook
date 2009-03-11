<?php

function LParse($string,$var_array=array())
{
	$_vars = count($var_array);
	for($i=0; $i < $_vars; $i++)
	{
		$string = str_replace('\\'.$i,$var_array[$i],$string);
	}
	
	return $string;
}

function get_client_browser()
{
	$browser = isset($_SERVER) ? $_SERVER['HTTP_USER_AGENT'] : getenv('HTTP_USER_AGENT');
	return $browser;
}

function get_client_ip(){
   if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
       $ip = getenv("HTTP_CLIENT_IP");
   else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
       $ip = getenv("HTTP_X_FORWARDED_FOR");
   else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
       $ip = getenv("REMOTE_ADDR");
   else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
       $ip = $_SERVER['REMOTE_ADDR'];
   else
       $ip = "unknown";
   return($ip);
}

function url($action=ACTION_NAME,$module=MODULE_NAME,$route='',$app=APP_NAME,$params=array()) {
    if(C('DISPATCH_ON') && C('URL_MODEL')>0) {
        $depr = C('PATH_MODEL')==2?C('PATH_DEPR'):'/';
        if(!empty($route)) {
            $url    =   str_replace(APP_NAME,$app,__APP__).'/'.$route.$depr.implode($depr,$params);
        }else{
            $str    =   $depr;
            foreach ($params as $var=>$val)
                $str .= $var.$depr.$val.$depr;
            $url    =   str_replace(APP_NAME,$app,__APP__).'/'.$module.$depr.$action.substr($str,0,-1);
        }
        if(C('HTML_URL_SUFFIX')) {
            $url .= C('HTML_URL_SUFFIX');
        }
    }else{
        $params =   http_build_query($params);
        if(!empty($route)) {
            $url    =   str_replace(APP_NAME,$app,__APP__).'?'.C('VAR_ROUTER').'='.$route.'&'.$params;
        }else{
            $url    =   str_replace(APP_NAME,$app,__APP__).'?'.C('VAR_MODULE').'='.$module.'&'.C('VAR_ACTION').'='.$action.'&'.$params;
        }
    }
    return $url;
}

/**
 +----------------------------------------------------------
 * URL
 * url('action?a=1&b=2');
 * url('module/action');
 * url('route@?a=1&b=2'); 
 * url('app://module/action');
 * url('app://module/action?a=1&b=2'); 
 +----------------------------------------------------------
 */
function U($url,$params=array(),$redirect=false) {
    if(0===strpos($url,'/')) {
        $url   =  substr($url,1);
    }
    if(!strpos($url,'://')) {
        $url   =  APP_NAME.'://'.$url;
    }
    if(stripos($url,'@?')) {
        $url   =  str_replace('@?','@think?',$url);
    }elseif(stripos($url,'@')) {
        $url   =  $url.MODULE_NAME;
    }

    $array   =  parse_url($url);
    $app      =  isset($array['scheme'])?   $array['scheme']  :APP_NAME;
    $route    =  isset($array['user'])?$array['user']:'';
    if(isset($array['path'])) {
        $action  =  substr($array['path'],1);
        if(!isset($array['host'])) {

            $module = MODULE_NAME;
        }else{
            $module = $array['host'];
        }
    }else{
        $module = MODULE_NAME;
        $action   =  $array['host'];
    }
    if(isset($array['query'])) {
        parse_str($array['query'],$query);
        $params = array_merge($query,$params);
    }
    $url   =  url($action,$module,$route,$app,$params);
    if($redirect) {
        redirect($url);
    }else{
        return $url;
    }
}

function halt($error) {
    if(IS_CLI)   exit($error);
    $e = array();
    if(C('DEBUG_MODE') && strtoupper(C( 'TMPL_ENGINE_TYPE' )) == 'THINK'){
        if(!is_array($error)) {
            $trace = debug_backtrace();
            $e['message'] = $error;
            $e['file'] = $trace[0]['file'];
            $e['class'] = $trace[0]['class'];
            $e['function'] = $trace[0]['function'];
            $e['line'] = $trace[0]['line'];
            $traceInfo='';
            $time = date("y-m-d H:i:m");
            foreach($trace as $t)
            {
                $traceInfo .= '['.$time.'] '.$t['file'].' ('.$t['line'].') ';
                $traceInfo .= $t['class'].$t['type'].$t['function'].'(';
                $traceInfo .= implode(', ', $t['args']);
                $traceInfo .=")<br/>";
            }
            $e['trace']  = $traceInfo;
        }else {
            $e = $error;
        }
        if(C('EXCEPTION_TMPL_FILE')) {
            include C('EXCEPTION_TMPL_FILE');
        }else{
            include CORE_PATH.'Tpl/ThinkException.tpl.php';
        }
    }
    else
    {
        $error_page =   C('ERROR_PAGE');
        if(!empty($error_page)){
            redirect($error_page);
        }else {
            if(C('SHOW_ERROR_MSG')) {
                $e['message'] =  is_array($error)?$error['message']:$error;
            }else{
                $e['message'] = C('ERROR_MESSAGE');
            }
            if(C('EXCEPTION_TMPL_FILE')) {
                include C('EXCEPTION_TMPL_FILE');
            }else{
                include CORE_PATH.'Tpl/ThinkException.tpl.php';
            }
        }
    }
    exit;
}

function redirect($url,$time=0,$msg='')
{
    $url = str_replace(array("\n", "\r"), '', $url);
    if(empty($msg)) {
        $msg    =   LParse(L('_REDIRECT_MSG_'),array($time,$url));
    }
    if (!headers_sent()) {
        // redirect
        header("Content-Type:text/html; charset=".C('OUTPUT_CHARSET'));
        if(0===$time) {
            header("Location: ".$url);
        }else {
            header("refresh:{$time};url={$url}");
            echo($msg);
        }
        exit();
    }else {
        $str    = "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
        if($time!=0) {
            $str   .=   $msg;
        }
        exit($str);
    }
}

function throw_exception($msg,$type='ThinkException',$code=0)
{
    if(IS_CLI)   exit($msg);
    if(isset($_REQUEST[C('VAR_AJAX_SUBMIT')])) {
        header("Content-Type:text/html; charset=utf-8");
        exit($msg);
    }
    if(class_exists($type,false)){
        throw new $type($msg,$code,true);
    }else {
        halt($msg);
    }
}

function debug_start($label='')
{
    $GLOBALS[$label]['_beginTime'] = microtime(TRUE);
    if ( MEMORY_LIMIT_ON )  $GLOBALS[$label]['memoryUseStartTime'] = memory_get_usage();
}

function debug_end($label='')
{
    $GLOBALS[$label]['_endTime'] = microtime(TRUE);
    echo '<div style="text-align:center;width:100%">Process '.$label.': Times '.number_format($GLOBALS[$label]['_endTime']-$GLOBALS[$label]['_beginTime'],6).'s ';
    if ( MEMORY_LIMIT_ON )  {
        $GLOBALS[$label]['memoryUseEndTime'] = memory_get_usage();
        echo ' Memories '.number_format(($GLOBALS[$label]['memoryUseEndTime']-$GLOBALS[$label]['memoryUseStartTime'])/1024).' k';
    }
    echo '</div>';
}

function system_out($msg)
{
    if(!empty($msg))
        Log::record($msg,Log::DEBUG);
}

function dump($var, $echo=true,$label=null, $strict=true)
{
    $label = ($label===null) ? '' : rtrim($label) . ' ';
    if(!$strict) {
        if (ini_get('html_errors')) {
            $output = print_r($var, true);
            $output = "<pre>".$label.htmlspecialchars($output,ENT_QUOTES,C('OUTPUT_CHARSET'))."</pre>";
        } else {
            $output = $label . " : " . print_r($var, true);
        }
    }else {
        ob_start();
        var_dump($var);
        $output = ob_get_clean();
        if(!extension_loaded('xdebug')) {
            $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
            $output = '<pre>'
                    . $label
                    . htmlspecialchars($output, ENT_QUOTES,C('OUTPUT_CHARSET'))
                    . '</pre>';
        }
    }
    if ($echo) {
        echo($output);
        return null;
    }else {
        return $output;
    }
}

function auto_charset($fContents,$from='',$to=''){
    if(empty($from)) $from = C('TEMPLATE_CHARSET');
    if(empty($to))  $to =   C('OUTPUT_CHARSET');
    $from   =  strtoupper($from)=='UTF8'? 'utf-8':$from;
    $to       =  strtoupper($to)=='UTF8'? 'utf-8':$to;
    if( strtoupper($from) === strtoupper($to) || empty($fContents) || (is_scalar($fContents) && !is_string($fContents)) ){
        return $fContents;
    }
    if(is_string($fContents) ) {
        if(function_exists('mb_convert_encoding')){
            return mb_convert_encoding ($fContents, $to, $from);
        }elseif(function_exists('iconv')){
            return iconv($from,$to,$fContents);
        }else{
            halt(L('_NO_AUTO_CHARSET_'));
            return $fContents;
        }
    }
    elseif(is_array($fContents)){
        foreach ( $fContents as $key => $val ) {
            $_key =     auto_charset($key,$from,$to);
            $fContents[$_key] = auto_charset($val,$from,$to);
            if($key != $_key ) {
                unset($fContents[$key]);
            }
        }
        return $fContents;
    }
    elseif(is_object($fContents)) {
        $vars = get_object_vars($fContents);
        foreach($vars as $key=>$val) {
            $fContents->$key = auto_charset($val,$from,$to);
        }
        return $fContents;
    }
    else{
        return $fContents;
    }
}

function get_instance_of($className,$method='',$args=array())
{
    static $_instance = array();
    if(empty($args)) {
        $identify   =   $className.$method;
    }else{
        $identify   =   $className.$method.to_guid_string($args);
    }
    if (!isset($_instance[$identify])) {
        if(class_exists($className)){
            $o = new $className();
            if(method_exists($o,$method)){
                if(!empty($args)) {
                    $_instance[$identify] = call_user_func_array(array(&$o, $method), $args);
                }else {
                    $_instance[$identify] = $o->$method();
                }
            }
            else
                $_instance[$identify] = $o;
        }
        else
            halt(L('_CLASS_NOT_EXIST_').':'.$className);
    }
    return $_instance[$identify];
}

function __autoload($classname)
{
    if(substr($classname,-5)=="Model") {
        if(!import('@.Model.'.$classname)){
            import("@.*.Model.".$classname);
        }
    }elseif(substr($classname,-6)=="Action"){
        if(!import('@.Action.'.$classname)) {
            import("@.*.Action.".$classname);
        }
    }else {
        if(C('AUTO_LOAD_PATH')) {
            $paths  =   explode(',',C('AUTO_LOAD_PATH'));
            foreach ($paths as $path){
                if(import($path.$classname)) {
                    return ;
                }
            }
        }
    }
    return ;
}

function unserialize_callback($classname)
{
    if(C('CALLBACK_LOAD_PATH')) {
        $paths  =   explode(',',C('CALLBACK_LOAD_PATH'));
        foreach ($paths as $path){
            if(import($path.$classname)) {
                return ;
            }
        }
    }
}

function include_cache($filename)
{
    if (!isset($GLOBALS['import_file'][$filename])) {
        if(file_exists_case($filename)){
            include $filename;
            $GLOBALS['import_file'][$filename] = true;
        }
        else
        {
            $GLOBALS['import_file'][$filename] = false;
        }
    }
    return $GLOBALS['import_file'][$filename];
}

function require_cache($filename)
{
    if (!isset($GLOBALS['import_file'][$filename])) {
        if(file_exists_case($filename)){
            require $filename;
            $GLOBALS['import_file'][$filename] = true;
        }
        else
        {
            $GLOBALS['import_file'][$filename] = false;
        }
    }
    return $GLOBALS['import_file'][$filename];
}

function file_exists_case($filename) {
    if(is_file($filename)) {
        if(IS_WIN && C('CHECK_FILE_CASE')) {
            if(basename(realpath($filename)) != basename($filename)) {
                return false;
            }
        }
        return true;
    }
    return false;
}

function import($class,$baseUrl = '',$ext='.class.php',$subdir=false)
{
      static $_file = array();
      static $_class = array();
      $class    =   str_replace(array('.','#'), array('/','.'), $class);
      if(isset($_file[strtolower($class.$baseUrl)]))
            return true;
      else
            $_file[strtolower($class.$baseUrl)] = true;
      if( 0 === strpos($class,'@'))     $class =  str_replace('@',APP_NAME,$class);
      if(empty($baseUrl)) {
            $baseUrl   =  dirname(LIB_PATH);
      }else {
            $isPath =  true;
      }
      $class_strut = explode("/",$class);
      if('*' == $class_strut[0] || isset($isPath) ) {
      }
      elseif(APP_NAME == $class_strut[0]) {
          $class =  str_replace(APP_NAME.'/',LIB_DIR.'/',$class);
      }
      elseif(in_array(strtolower($class_strut[0]),array('think','org','com'))) {
          $baseUrl =  CORE_PATH.DS.LIB_DIR.'/';
      }else {
          $class    =   substr_replace($class, '', 0,strlen($class_strut[0])+1);
          $baseUrl =  APP_PATH.'/../'.$class_strut[0].'/'.LIB_DIR.'/';
      }
      if(substr($baseUrl, -1) != "/")    $baseUrl .= "/";
      $classfile = $baseUrl . $class . $ext;
      if(false !== strpos($classfile,'*') || false !== strpos($classfile,'?') ) {
            $match  =   glob($classfile);
            if($match) {
               foreach($match as $key=>$val) {
                   if(is_dir($val)) {
                       if($subdir) import('*',$val.'/',$ext,$subdir);
                   }else{
                       if($ext == '.class.php') {
                            $class = basename($val,$ext);
                            if(isset($_class[$class])) {
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
          if($ext == '.class.php' && is_file($classfile)) {
                $class = basename($classfile,$ext);
                if(isset($_class[strtolower($class)])) {
                    throw_exception(L('_CLASS_CONFLICT_').':'.$_class[strtolower($class)].' '.$classfile);
                }
                $_class[strtolower($class)] = $classfile;
          }
            return require_cache($classfile);
      }
}

function using($class,$baseUrl = LIB_PATH,$ext='.class.php',$subdir=false)
{
    return import($class,$baseUrl,$ext,$subdir);
}

function vendor($class,$baseUrl = '',$ext='.php',$subdir=false)
{
    if(empty($baseUrl)) {
        $baseUrl    =   VENDOR_PATH;
    }
    return import($class,$baseUrl,$ext,$subdir);
}

function to_guid_string($mix)
{
    if(is_object($mix) && function_exists('spl_object_hash')) {
        return spl_object_hash($mix);
    }elseif(is_resource($mix)){
        $mix = get_resource_type($mix).strval($mix);
    }else{
        $mix = serialize($mix);
    }
    return md5($mix);
}

function is_instance_of($object, $className)
{
	if (!is_object($object) && !is_string($object)) {
		return false;
	}
    return $object instanceof $className;
}

function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true)
{
	if($suffix)
		$suffixStr = "…";
	else
		$suffixStr = "";

    if(function_exists("mb_substr"))
        return mb_substr($str, $start, $length, $charset).$suffixStr;
    elseif(function_exists('iconv_substr')) {
        return iconv_substr($str,$start,$length,$charset).$suffixStr;
    }
    $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
    $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
    $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
    $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
    preg_match_all($re[$charset], $str, $match);
    $slice = join("",array_slice($match[0], $start, $length));
    return $slice.$suffixStr;
}

function rand_string($len=6,$type='',$addChars='') {
    $str ='';
    switch($type) {
        case 0:
            $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.$addChars;
            break;
        case 1:
            $chars= str_repeat('0123456789',3);
            break;
        case 2:
            $chars='ABCDEFGHIJKLMNOPQRSTUVWXYZ'.$addChars;
            break;
        case 3:
            $chars='abcdefghijklmnopqrstuvwxyz'.$addChars;
            break;
        case 4:
            $chars = L('_RAND_STRING_CHARS_').$addChars;
            break;
        default :
            $chars='ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789'.$addChars;
            break;
    }
    if($len>10 ) {
        $chars= $type==1? str_repeat($chars,$len) : str_repeat($chars,5);
    }
    if($type!=4) {
        $chars   =   str_shuffle($chars);
        $str     =   substr($chars,0,$len);
    }else{
        for($i=0;$i<$len;$i++){
          $str.= msubstr($chars, floor(mt_rand(0,mb_strlen($chars,'utf-8')-1)),1);
        }
    }
    return $str;
}

function build_verify ($length=4,$mode=1) {
    return rand_string($length,$mode);
}

if(!function_exists('stripslashes_deep')) {
    function stripslashes_deep($value) {
       $value = is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
       return $value;
    }
}

function D($className='',$appName='')
{
	$className = ucfirst($className);
    static $_model = array();
    if(empty($className)) {
        return new  Model();
    }
    if(empty($appName)) {
        $appName =  C('DEFAULT_MODEL_APP');
    }
    if(isset($_model[$appName.$className])) {
        return $_model[$appName.$className];
    }
    $OriClassName = $className;
    if(strpos($className,C('COMPONENT_DEPR'))) {
        $array  =   explode(C('COMPONENT_DEPR'),$className);
        $className = array_pop($array);
        $className =  C('MODEL_CLASS_PREFIX').$className.C('MODEL_CLASS_SUFFIX');
        if(C('COMPONENT_TYPE')==1) {
            import($appName.'.'.implode('.',$array).'.Model.'.$className);
        }else{
            import($appName.'.Model.'.implode('.',$array).'.'.$className);
        }
    }else{
        $className =  C('MODEL_CLASS_PREFIX').$className.C('MODEL_CLASS_SUFFIX');
        if(!import($appName.'.Model.'.$className)) {
            if(C('COMPONENT_TYPE')==1) {
                import($appName.'.*.Model.'.$className);
            }else{
                import($appName.'.Model.*.'.$className);
            }
        }
    }
    if(class_exists($className)) {
        $model = new $className();
        $_model[$appName.$OriClassName] =  $model;
        return $model;
    }else {
        throw_exception($className.L('_MODEL_NOT_EXIST_'));
        return false;
    }
}

function A($className,$appName='@')
{
    static $_action = array();
    if(isset($_action[$appName.$className])) {
        return $_action[$appName.$className];
    }
    $OriClassName = $className;
    if(strpos($className,C('COMPONENT_DEPR'))) {
        $array  =   explode(C('COMPONENT_DEPR'),$className);
        $className = array_pop($array);
        $className =  C('CONTR_CLASS_PREFIX').$className.C('CONTR_CLASS_SUFFIX');
        if(C('COMPONENT_TYPE')==1) {
            import($appName.'.'.implode('.',$array).'.Action.'.$className);
        }else{
            import($appName.'.Action.'.implode('.',$array).'.'.$className);
        }
    }else{
        $className =  C('CONTR_CLASS_PREFIX').$className.C('CONTR_CLASS_SUFFIX');
        if(!import($appName.'.Action.'.$className)) {
            if(C('COMPONENT_TYPE')==1) {
                import($appName.'.*.Action.'.$className);
            }else{
                import($appName.'.Action.*.'.$className);
            }
        }
    }
    if(class_exists($className)) {
        $action = new $className();
        $_action[$appName.$OriClassName] = $action;
        return $action;
    }else {
        return false;
    }
}

function R($module,$action,$app='@') {
    $class = A($module,$app);
    if($class) {
        return $class->$action();
    }else{
        return false;
    }
}

function SParse($str)
{
	if(is_file(CONFIG_PATH.'SParse.php'))
	{
		$_array = include(CONFIG_PATH.'SParse.php');
	}
	
	foreach($_array as $var => $val)
	{
		$str = str_replace($var,$val,$str);
	}
	return $str;
}

function L($name='',$value=null) {
    static $_lang = array();
    if(!is_null($value)) {
		$_lang[strtolower($name)]   =   $value;
        return;
    }
    if(empty($name)) {
        return $_lang;
    }
    if(is_array($name)) {
        $_lang = array_merge($_lang,array_change_key_case($name));
        return;
    }
    if(isset($_lang[strtolower($name)])) {
        return SParse($_lang[strtolower($name)]);
    }else{
        return false;
    }
}

function C($name='',$value=null) {
    static $_config = array();
    if(!is_null($value)) {
        if(strpos($name,'.')) {
            $array   =  explode('.',strtolower($name));
            $_config[$array[0]][$array[1]] =   $value;
        }else{
            $_config[strtolower($name)] =   $value;
        }
        return ;
    }
    if(empty($name)) {
        return $_config;
    }
    if(is_array($name)) { 
        $_config = array_merge($_config,array_change_key_case($name));
        return $_config;
    }elseif(0===strpos($name,'?')){ 
        $name   = strtolower(substr($name,1));
        if(strpos($name,'.')) { 
            $array   =  explode('.',$name);
            return isset($_config[$array[0]][$array[1]]);
        }else{
            return isset($_config[$name]);
        }
    }elseif(strpos($name,'.')) { 
        $array   =  explode('.',strtolower($name));
        return $_config[$array[0]][$array[1]];
    }elseif(isset($_config[strtolower($name)])) { 
        return $_config[strtolower($name)];
    }else{
        return NULL;
    }
}

function S($name,$value='',$expire='',$type='') {
    static $_cache = array();
    import('Think.Util.Cache');

    $cache  = Cache::getInstance($type);
    if('' !== $value) {
        if(is_null($value)) {

            $result =   $cache->rm($name);
            if($result) {
                unset($_cache[$type.'_'.$name]);
            }
            return $result;
        }else{

            $cache->set($name,$value,$expire);
            $_cache[$type.'_'.$name]     =   $value;
        }
        return ;
    }
    if(isset($_cache[$type.'_'.$name])) {
        return $_cache[$type.'_'.$name];
    }

    $value      =  $cache->get($name);
    $_cache[$type.'_'.$name]     =   $value;
    return $value;
}

function F($name,$value='',$expire=-1,$path=MODEL_DATA_PATH) {
    static $_cache = array();
    $filename   =   $path.$name.'.php';
    if('' !== $value) {
        if(is_null($value)) {
            $result =   unlink($filename);
            if($result) {
                unset($_cache[$name]);
            }
            return $result;
        }else{
            $content   =   "<?php\n\n//".sprintf('%012d',$expire)."\nreturn ".var_export($value,true).";\n?>";
            $result  =   file_put_contents($filename,$content);
            $_cache[$name]   =   $value;
        }
        return ;
    }
    if(isset($_cache[$name])) {
        return $_cache[$name];
    }

    if(is_file($filename) && false !== $content = file_get_contents($filename)) {
        $expire  =  (int)substr($content,44, 12);
        if($expire != -1 && time() > filemtime($filename) + $expire) {
            unlink($filename);
            return false;
        }
        $str       = substr($content,57,-2);
        $value    = eval($str);
        $_cache[$name]   =   $value;
    }else{
        $value  =   false;
    }
    return $value;
}

function I($class,$baseUrl = '',$ext='.class.php') {
    static $_class = array();
    if(isset($_class[$baseUrl.$class])) {
        return $_class[$baseUrl.$class];
    }
    $class_strut = explode(".",$class);
    $className  =   array_pop($class_strut);
    if($className != '*') {
        import($class,$baseUrl,$ext,false);
        if(class_exists($className)) {
            $_class[$baseUrl.$class] = new $className();
            return $_class[$baseUrl.$class];
        }else{
            return false;
        }
    }else {
        return false;
    }
}

function xml_encode($data,$encoding='utf-8',$root="think") {
    $xml = '<?xml version="1.0" encoding="'.$encoding.'"?>';
    $xml.= '<'.$root.'>';
    $xml.= data_to_xml($data);
    $xml.= '</'.$root.'>';
    return $xml;
}

function data_to_xml($data) {
    if(is_object($data)) {
        $data = get_object_vars($data);
    }
    $xml = '';
    foreach($data as $key=>$val) {
        is_numeric($key) && $key="item id=\"$key\"";
        $xml.="<$key>";
        $xml.=(is_array($val)||is_object($val))?data_to_xml($val):$val;
        list($key,)=explode(' ',$key);
        $xml.="</$key>";
    }
    return $xml;
}

function mk_dir($dir, $mode = 0755)
{
  if (is_dir($dir) || @mkdir($dir,$mode)) return true;
  if (!mk_dir(dirname($dir),$mode)) return false;
  return @mkdir($dir,$mode);
}

function clearCache($type=0,$path=NULL) {
        if(is_null($path)) {
            switch($type) {
            case 0:
                $path = CACHE_PATH;
                break;
            case 1:
                $path   =   RUNTIME_PATH;
                break;
            case 2:
                $path   =   LOG_PATH;
                break;
            case 3:
                $path   =   MODEL_DATA_PATH;
            }
        }
        import("ORG.Io.Dir");
        Dir::del($path);
}

?>