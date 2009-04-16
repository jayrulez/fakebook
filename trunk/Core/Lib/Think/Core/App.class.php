<?php

class App extends Base
{
    public static function  getInstance()
    {
        return get_instance_of(__CLASS__);
    }

    public function init()
    {
        set_error_handler(array(&$this,"appError"));
        set_exception_handler(array(&$this,"appException"));

        if(is_file(RUNTIME_PATH.'~app.php') && (!is_file(CONFIG_PATH.'config.php') || filemtime(RUNTIME_PATH.'~app.php')>filemtime(CONFIG_PATH.'config.php')))
		{
            C(include RUNTIME_PATH.'~app.php');
        }else{
            $this->build();
        }

        if(IS_CLI)
		{
            define('MODULE_NAME',   $this->getModule());      
            define('ACTION_NAME',   $this->getAction());  
            L(include CORE_PATH.'Lang/'.C('DEFAULT_LANGUAGE').'.php');
        }else{
            if(function_exists('date_default_timezone_set'))
                date_default_timezone_set(C('TIME_ZONE'));

            if('FILE' != strtoupper(C('SESSION_TYPE'))) {
                import("Think.Util.Filter");
                Filter::load(ucwords(strtolower(C('SESSION_TYPE'))).'Session');
            }
            session_start();
            if($plugInOn =  C('THINK_PLUGIN_ON')) {
                $this->loadPlugIn();
            }

            if(C('DISPATCH_ON')) {
                if( 'Think'== C('DISPATCH_NAME') ) {
                    import('Think.Util.Dispatcher');
                    Dispatcher::dispatch();
                }elseif($plugInOn) {
                    apply_filter('app_dispatch');
                }
            }

            if(!defined('PHP_FILE')) {
                define('PHP_FILE',_PHP_FILE_);
            }

            if(!defined('MODULE_NAME')) define('MODULE_NAME',   $this->getModule());     
            if(!defined('ACTION_NAME')) define('ACTION_NAME',   $this->getAction());  

            if(is_file(CONFIG_PATH.MODULE_NAME.'_config.php')) {
                C(include CONFIG_PATH.MODULE_NAME.'_config.php');
            }

            if(C('LIMIT_RESFLESH_ON') && (!isset($_REQUEST[C('VAR_RESFLESH')]) || $_REQUEST[C('VAR_RESFLESH')]!="1")) {

                $guid	=	md5($_SERVER['PHP_SELF']);

                if(Cookie::is_set('last_visit_time_'.$guid) && Cookie::get('last_visit_time_'.$guid)>time()-C('LIMIT_REFLESH_TIMES')) {

                    header('HTTP/1.1 304 Not Modified');
                    exit;
                }else{
                    Cookie::set('last_visit_time_'.$guid,$_SERVER['REQUEST_TIME'],C('COOKIE_EXPIRE'));
                    header('Last-Modified:'.(date('D,d M Y H:i:s',$_SERVER['REQUEST_TIME']-C('LIMIT_REFLESH_TIMES'))).' GMT');
                }
            }
			
            $this->checkLanguage();    
            $this->checkTemplate();   

            if(C('HTML_CACHE_ON')) {
                import('Think.Util.HtmlCache');
                HtmlCache::readHTMLCache();
            }

            if($plugInOn) {
                apply_filter('app_init');
            }
        }
        if(C('SHOW_RUN_TIME')){
            $GLOBALS['_initTime'] = microtime(TRUE);
        }

        return ;
    }

    private function build()
    {
        C(include(INC_PATH.'system.php'));

        if(file_exists_case(CONFIG_PATH.'config.php')) {
            C(include CONFIG_PATH.'config.php');
        }

        if(file_exists_case(COMMON_PATH.'common.php')) {
            include COMMON_PATH.'common.php';
            if(!C('DEBUG_MODE')) {
                if(defined('STRIP_RUNTIME_SPACE') && STRIP_RUNTIME_SPACE == false ) {
                    $common	= file_get_contents(COMMON_PATH.'common.php');
                }else{
                    $common	= php_strip_whitespace(COMMON_PATH.'common.php');
                }
                if('?>' != substr(trim($common),-2)) {
                    $common .= ' ?>';
                }
            }
        }
        if(C('DEBUG_MODE')) {
            C(include(INC_PATH.'debug'));
            if(file_exists_case(CONFIG_PATH.'debug.php')) {
                C(include CONFIG_PATH.'debug.php');
            }
        }else{
            $content  = $common."<?php\nreturn ".var_export(C(),true).";\n?>";
            file_put_contents(RUNTIME_PATH.'~app.php',$content);
        }
        if(C('APP_AUTO_SETUP')) {
            if(file_exists_case(COMMON_PATH.'setup.php') && !is_file(APP_PATH.'install.ok')) {
                include COMMON_PATH.'setup.php';
                file_put_contents(APP_PATH.'install.ok','install ok');
            }
        }
        return ;
    }

    private function getModule()
    {
        if(IS_CLI) {
            $module = isset($_SERVER['argv'][1])?$_SERVER['argv'][1]:C('DEFAULT_MODULE');
        }else{
            $module = !empty($_POST[C('VAR_MODULE')]) ?
                $_POST[C('VAR_MODULE')] :
                (!empty($_GET[C('VAR_MODULE')])? $_GET[C('VAR_MODULE')]:C('DEFAULT_MODULE'));
           
            if(strpos($module,C('COMPONENT_DEPR'))) {
               
                define('C_MODULE_NAME',$module);
                $array	=	explode(C('COMPONENT_DEPR'),$module);
                
                $module	=	array_pop($array);
                
                if(1==count($array)) {
                   define('COMPONENT_NAME',$array[0]);
                }else{
                    define('COMPONENT_NAME',implode('/',$array));
                }
            }
            if(C('MODULE_REDIRECT')) {
                $res = preg_replace('@(\w+):([^,\/]+)@e', '$modules[\'\\1\']="\\2";', C('MODULE_REDIRECT'));
                if(array_key_exists($module,$modules)) {
                    define('P_MODULE_NAME',$module);
                    $module	=	$modules[$module];
                }
            }
            if(C('URL_CASE_INSENSITIVE')) {
                define('P_MODULE_NAME',strtolower($module));
                if(C('AUTO_NAME_IDENTIFY')) {
                    // Smart identification index.php/user_type/index/ identification to UserTypeAction module
                    $module = ucfirst($this->parseName(strtolower($module),1));
                }else{
                    $module = ucwords(strtolower($module));
                }
            }

            unset($_POST[C('VAR_MODULE')],$_GET[C('VAR_MODULE')]);
        }
        return $module;
    }

    private function getAction()
    {
        if(IS_CLI) {
            $action  =  isset($_SERVER['argv'][2])?$_SERVER['argv'][2]:C('DEFAULT_ACTION');
        }else{
            $action   = !empty($_POST[C('VAR_ACTION')]) ?
                $_POST[C('VAR_ACTION')] :
                (!empty($_GET[C('VAR_ACTION')])?$_GET[C('VAR_ACTION')]:C('DEFAULT_ACTION'));
            if(strpos($action,C('COMPONENT_DEPR'))) {
                define('C_ACTION_NAME',$action);
                $array	=	explode(C('COMPONENT_DEPR'),$action);
                $action	=	array_pop($array);
            }

            if(C('ACTION_REDIRECT')) {
                $res = preg_replace('@(\w+):([^,\/]+)@e', '$actions[\'\\1\']="\\2";', C('ACTION_REDIRECT'));
                if(array_key_exists($action,$actions)) {
                    define('P_ACTION_NAME',$action);
                    $action	=	$actions[$action];
                }
            }
            unset($_POST[C('VAR_ACTION')],$_GET[C('VAR_ACTION')]);
        }
        return $action;
    }

    private function checkLanguage()
    {
		$defaultLang = C('DEFAULT_LANGUAGE');
        if(C('LANG_SWITCH_ON')) {
            if(C('AUTO_DETECT_LANG')){
                /*if(isset($_GET[C('VAR_LANGUAGE')])) {
                    $langSet = $_GET[C('VAR_LANGUAGE')];*/
            	if(Session::get('userInfo') != NULL){
            		$userInfo = Session::get('userInfo');
            		$langSet = $userInfo['language'];
                }elseif ( Cookie::is_set('language') ) {
                    $langSet = Cookie::get('language');
                }else if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])){
                    preg_match('/^([a-z\-]+)/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches);
                    $langSet = $matches[1];
                }else{
                    $langSet = $defaultLang;
                }
				if(!is_dir(LANG_PATH.$langSet)){
					$langSet = $defaultLang;
				}
			}else{
				$langSet = $defaultLang;
			}
			//Cookie::set('language',$langSet,C('COOKIE_EXPIRE'));
			
			define('LANG_SET',$langSet);
			if(C('LANG_CACHE_ON') && is_file(RUNTIME_PATH.MODULE_NAME.'_'.LANG_SET.'_lang.php')) {
				L(include RUNTIME_PATH.MODULE_NAME.'_'.LANG_SET.'_lang.php');
			}else{
                if (file_exists_case(CORE_PATH.'Lang'.DS.LANG_SET.'.php')){
                    L(include CORE_PATH.'Lang'.DS.LANG_SET.'.php');
                }else{
                    L(include CORE_PATH.'Lang'.DS.$defaultLang.'.php');
                }

                if (file_exists_case(LANG_PATH.LANG_SET.DS.'common.php'))
                    L(include LANG_PATH.LANG_SET.DS.'common.php');

                if (file_exists_case(LANG_PATH.LANG_SET.DS.MODULE_NAME.'.php'))
                    L(include LANG_PATH.LANG_SET.DS.MODULE_NAME.'.php');

                if(C('LANG_CACHE_ON')) {

                    $content  = "<?php\r\nreturn ".var_export(L(),true).";\r\n?>";
                    file_put_contents(RUNTIME_PATH.MODULE_NAME.'_'.LANG_SET.'_lang.php',$content);
                }
            }
        }else{
			$langSet = $defaultLang;
			define('LANG_SET',$langSet);
            L(include CORE_PATH.'Lang'.DS.$langSet.'.php');
        }
		$langId = substr($langSet, 0,2);
		C('LANG_ID',$langId);
		C('XML_LANG_ID',$langId);
		C('HTM_LANG_ID',$langId);
        return ;
    }

    private function checkTemplate()
    {
		$defaultTplSet = C('DEFAULT_TEMPLATE');
        if(C('TMPL_SWITCH_ON'))
		{
            if(C('AUTO_DETECT_THEME'))
			{
                if(isset($_GET[C('VAR_TEMPLATE')]))
				{
                    $templateSet = $_GET[C('VAR_TEMPLATE')];
                }else if(Cookie::is_set('template'))
				{
                    $templateSet = Cookie::get('template');
                }else{
                    $templateSet = $defaultTplSet;
                }
                if(!is_dir(TMPL_PATH.$templateSet))
				{
                    $templateSet = $defaultTplSet;
                }
				Cookie::set('template',$templateSet,C('COOKIE_EXPIRE'));
            }else{
                $templateSet = $defaultTplSet;
            }
            define('TEMPLATE_NAME',$templateSet);
            define('TEMPLATE_PATH',TMPL_PATH.TEMPLATE_NAME);
            $tmplDir	=	THEMES_DIR.'/'.TEMPLATE_NAME.'/';
        }else{
            define('TEMPLATE_PATH',TMPL_PATH);
            $tmplDir	=	THEMES_DIR.'/';
        }

        define('__ROOT__',WEB_URL);

        define('__APP__',PHP_FILE);

        $module	=	defined('P_MODULE_NAME')?P_MODULE_NAME:MODULE_NAME;
        $action		=	defined('P_ACTION_NAME')?P_ACTION_NAME:ACTION_NAME;

        define('__SELF__',$_SERVER['PHP_SELF']);

        if(C('APP_DOMAIN_DEPLOY')) {
            $appRoot   =  '/';
        }else{
            $appRoot   =  WEB_URL.'/';
        }

        if(defined('C_MODULE_NAME')) {

            define('__URL__',PHP_FILE.'/'.C_MODULE_NAME);

            define('__ACTION__',__URL__.C('PATH_DEPR').$action);
            C('TMPL_FILE_NAME',LIB_PATH.COMPONENT_NAME.'/'.THEMES_DIR.'/'.TEMPLATE_NAME.'/'.MODULE_NAME.'/'.ACTION_NAME.C('TEMPLATE_SUFFIX'));

            define('APP_TMPL_URL', $appRoot.LIB_DIR.'/'.COMPONENT_NAME.'/'.THEMES_DIR.'/'.TEMPLATE_NAME.'/');
            define('__CURRENT__', WEB_URL.'/'.LIB_DIR.'/'.$tmplDir.str_replace(C('COMPONENT_DEPR'),'/',C_MODULE_NAME));
        }else{

            define('__URL__',PHP_FILE.'/'.$module);

            define('__ACTION__',__URL__.C('PATH_DEPR').$action);
            C('TMPL_FILE_NAME',TEMPLATE_PATH.'/'.MODULE_NAME.'/'.ACTION_NAME.C('TEMPLATE_SUFFIX'));
            define('__CURRENT__', WEB_URL.'/'.$tmplDir.MODULE_NAME);

            define('APP_TMPL_URL', $appRoot.$tmplDir);
        }

        define('WEB_PUBLIC_URL', WEB_URL.'/Public');

        define('APP_PUBLIC_URL', APP_TMPL_URL.'Public');

        return ;
    }

    private function loadPlugIn()
    {
        include(INC_PATH.'plugin.php');

        if(is_file(RUNTIME_PATH.'~plugins.php')) {
            include RUNTIME_PATH.'~plugins.php';
        }else{

            $common_plugins = get_plugins(CORE_PATH.'PlugIns','Think');
            $app_plugins = get_plugins();

            $plugins    = array_merge($common_plugins,$app_plugins);

            $content	=	'';
            foreach($plugins as $key=>$file) {
                include $file;
                $content	.=	php_strip_whitespace($file);
            }
            file_put_contents(RUNTIME_PATH.'~plugins.php',$content);
        }
        return ;
    }

    public function exec()
    {
        if(IS_CLI) {

            R(MODULE_NAME,ACTION_NAME);
        }else{

            $_autoload	=	C('AUTO_LOAD_CLASS');
            if(!empty($_autoload)) {
                $import	=	explode(',',$_autoload);
                foreach ($import as $key=>$class){
                    import($class);
                }
            }

            if(defined('C_MODULE_NAME')) {
                $this->initComponent();

                $module  =  A(C_MODULE_NAME);
            }else{
                $module  =  A(MODULE_NAME);
            }
            if(!$module) {

                $module	=	A("Empty");
                if(!$module) {
                    throw_exception(L('_MODULE_NOT_EXIST_').MODULE_NAME);
                }
            }

            $action = ACTION_NAME.C('ACTION_SUFFIX');
            if(defined('C_ACTION_NAME')) {

                $actionList	=	explode(C('COMPONENT_DEPR'),C_ACTION_NAME);
                foreach ($actionList as $action){
                    $module->$action();
                }
            }else{

                if(file_exists_case(CONFIG_PATH.'behaviors.php')) {
                    $behaviors = include CONFIG_PATH.'behaviors.php';

                    if(isset($behaviors[MODULE_NAME.':'.ACTION_NAME])) {

                        //'module:action'=>array('before'=>array(),'after'=>array());
                        $behavior1   =   $behaviors[MODULE_NAME.':'.ACTION_NAME];
                    }
                    if(isset($behaviors[ACTION_NAME])){

                        // 'action'=>array('before'=>array(),'after'=>array());
                        $behavior2   =   $behaviors[ACTION_NAME];
                    }
                    if(isset($behaviors['*'])){

                        // '*'=>array('before'=>array(),'after'=>array());
                        $behavior3   =   $behaviors['*'];
                    }

                    if(isset($behavior3['before'])) {
                        foreach ($behavior3['before'] as $key=>$call){
                            call_user_func($call);
                        }
                    }
                    if(isset($behavior2['before'])) {
                        foreach ($behavior2['before'] as $key=>$call){
                            call_user_func($call);
                        }
                    }
                    if(isset($behavior1['before'])) {
                        foreach ($behavior1['before'] as $key=>$call){
                            call_user_func($call);
                        }
                    }

                    $module->{$action}();

                    if(isset($behavior1['after'])) {
                        foreach ($behavior1['after'] as $key=>$call){
                            call_user_func($call);
                        }
                    }
                    if(isset($behavior2['after'])) {
                        foreach ($behavior2['after'] as $key=>$call){
                            call_user_func($call);
                        }
                    }
                    if(isset($behavior3['after'])) {
                        foreach ($behavior3['after'] as $key=>$call){
                            call_user_func($call);
                        }
                    }
                }else{

                    if (method_exists($module,'_before_'.$action)) {
                        $module->{'_before_'.$action}();
                    }

                    $module->{$action}();
                    if (method_exists($module,'_after_'.$action)) {
                        $module->{'_after_'.$action}();
                    }
                }
            }
            if(C('THINK_PLUGIN_ON')) {
                apply_filter('app_end');
            }
        }
        return ;
    }

    private function initComponent() {
        if(is_file(LIB_PATH.COMPONENT_NAME.'/Config/config.php'))
            C(include LIB_PATH.COMPONENT_NAME.'/Config/config.php');
        if(is_file(LIB_PATH.COMPONENT_NAME.'/Common/common.php'))
            include LIB_PATH.COMPONENT_NAME.'/Common/common.php';
        if (is_file(LIB_PATH.COMPONENT_NAME.'/Lang/'.LANG_SET.'.php'))
            L(include LIB_PATH.COMPONENT_NAME.'/Lang/'.LANG_SET.'.php');
    }

    public function run() {
        $this->init();
        $this->exec();
        return ;
    }

    public function appException($e)
    {
        halt($e->__toString());
    }

    public function appError($errno, $errstr, $errfile, $errline)
    {
      switch ($errno) {
          case E_ERROR:
          case E_USER_ERROR:
              $errorStr = LParse(L('_APP_EXCEPTION_ERROR_STRING_'),array($errno,$errstr,basename($errfile),$errline));
              if(C('WEB_LOG_RECORD')){
                 Log::write($errorStr,Log::ERR);
              }
              halt($errorStr);
              break;
          case E_STRICT:
          case E_USER_WARNING:
          case E_USER_NOTICE:
          default:
            $errorStr = LParse(L('_APP_EXCEPTION_ERROR_STRING_'),array($errno,$errstr,basename($errfile),$errline));
            Log::record($errorStr,Log::NOTICE);
             break;
      }
    }

    public function __destruct()
    {
        if(C('WEB_LOG_RECORD')) Log::save();
    }
}

?>