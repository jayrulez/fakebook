<?php

class Dispatcher extends Base
{
    private static $useRoute = false;

    static function dispatch()
    {
        $urlMode  =  C('URL_MODEL');
        if($urlMode == URL_REWRITE ) {
            $url    =   dirname(_PHP_FILE_);
            if($url == '/' || $url == '\\') {
                $url    =   '';
            }
            define('PHP_FILE',$url);
        }elseif($urlMode == URL_COMPAT){
            define('PHP_FILE',_PHP_FILE_.'?'.C('VAR_PATHINFO').'=');
        }else {
            define('PHP_FILE',_PHP_FILE_);
        }
        if($urlMode == URL_PATHINFO || $urlMode == URL_REWRITE || $urlMode == URL_COMPAT) {
			self::parsePathInfo();
			if (!empty($_GET) && C('URL_AUTO_REDIRECT') && !isset($_GET[C('VAR_ROUTER')])) {
                $_GET  =  array_merge (self :: getPathInfo(),$_GET);
                $_varModule =   C('VAR_MODULE');
                $_varAction =   C('VAR_ACTION');
                $_depr  =   C('PATH_DEPR');
                $_pathModel =   C('PATH_MODEL');
                if(empty($_GET[$_varModule])) $_GET[$_varModule] = C('DEFAULT_MODULE');
                if(empty($_GET[$_varAction])) $_GET[$_varAction] = C('DEFAULT_ACTION');
                $_URL = '/';
                if($_pathModel==2) {
                    $_URL .= $_GET[$_varModule].$_depr.$_GET[$_varAction].$_depr;
                    unset($_GET[$_varModule],$_GET[$_varAction]);
                }
                foreach ($_GET as $_VAR => $_VAL) {
                    if('' != trim($_GET[$_VAR])) {
                        if($_pathModel==2) {
                            $_URL .= $_VAR.$_depr.rawurlencode($_VAL).$_depr;
                        }else{
                            $_URL .= $_VAR.'/'.rawurlencode($_VAL).'/';
                        }
                    }
                }
                if($_depr==',') $_URL = substr($_URL, 0, -1).'/';
                redirect(PHP_FILE.$_URL);
            }
            if(C('ROUTER_ON')) {
                self::routerCheck();
            }
            $_GET = array_merge(self :: getPathInfo(),$_GET);
            $_REQUEST = array_merge($_POST,$_GET);

        }else {
            if(!empty($_SERVER['PATH_INFO']) ) {
                $pathinfo = self :: getPathInfo();
                $_GET = array_merge($_GET,$pathinfo);
                if(!empty($_POST)) {
                    $_POST = array_merge($_POST,$pathinfo);
                }elseif(C('URL_AUTO_REDIRECT')) {
                    $jumpUrl = PHP_FILE.'?'.http_build_query($_GET);
                    redirect($jumpUrl);
                }
            }else {
                if(C('URL_AUTO_REDIRECT')) {
                    $query  = explode('&',trim($_SERVER['QUERY_STRING'],'&'));
                    if(count($query) != count($_GET) && count($_GET)>0) {
                        $_URL  =  '';
                        foreach ($_GET as $_VAR => $_VAL) {
                            $_URL .= $_VAR.'='.rawurlencode($_VAL).'&';
                        }
                        $jumpUrl = PHP_FILE.'?'.substr($_URL,0,-1);
                        redirect($jumpUrl);
                    }
                }
                if(isset($_GET[C('VAR_ROUTER')])) {
                    self::routerCheck();
                }
            }
        }
    }

    private static function MagicQuote()
    {
        if ( get_magic_quotes_gpc() ) {
           $_POST = stripslashes_deep($_POST);
           $_GET = stripslashes_deep($_GET);
           $_COOKIE = stripslashes_deep($_COOKIE);
           $_REQUEST= stripslashes_deep($_REQUEST);
        }
    }

    private static function routerCheck() {
        if(file_exists_case(CONFIG_PATH.'routes.php')) {
            $routes = include CONFIG_PATH.'routes.php';
            if(!is_array($routes)) {
                $routes =   $_routes;
            }
            if(isset($_GET[C('VAR_ROUTER')])) {
                $routeName  =   $_GET[C('VAR_ROUTER')];
                unset($_GET[C('VAR_ROUTER')]);
            }else{
                $paths = explode(C('PATH_DEPR'),trim($_SERVER['PATH_INFO'],'/'));
                $routeName  =   array_shift($paths);
            }
            if(isset($routes[$routeName])) {
                $route = $routes[$routeName];
                $_GET[C('VAR_MODULE')]  =   $route[0];
                $_GET[C('VAR_ACTION')]  =   $route[1];
                if(!isset($_GET[C('VAR_ROUTER')])) {
                    $vars    =   explode(',',$route[2]);
                    for($i=0;$i<count($vars);$i++) {
                        $_GET[$vars[$i]]     =   array_shift($paths);
                    }
                    $res = preg_replace('@(\w+)\/([^,\/]+)@e', '$_GET[\'\\1\']="\\2";', implode('/',$paths));
                }
                if(isset($route[3])) {
                    parse_str($route[3],$params);
                    $_GET   =   array_merge($_GET,$params);
                }
                unset($_SERVER['PATH_INFO']);
            }elseif(isset($routes[$routeName.'@'])){
                $routeItem = $routes[$routeName.'@'];
                $regx = str_replace($routeName,'',trim($_SERVER['PATH_INFO'],'/'));
                foreach ($routeItem as $route){
                    $rule    =   $route[0];        
                    if(preg_match($rule,$regx,$matches)) {
                        $_GET[C('VAR_MODULE')]  =   $route[1];
                        $_GET[C('VAR_ACTION')]  =   $route[2];
                        if(!isset($_GET[C('VAR_ROUTER')])) {
                            $vars    =   explode(',',$route[3]);
                            for($i=0;$i<count($vars);$i++) {
                                $_GET[$vars[$i]]     =   $matches[$i+1];
                            }
                            $res = preg_replace('@(\w+)\/([^,\/]+)@e', '$_GET[\'\\1\']="\\2";', str_replace($matches[0],'',$regx));
                        }
                        if(isset($route[4])) {
                            parse_str($route[4],$params);
                            $_GET   =   array_merge($_GET,$params);
                        }
                        //unset($_SERVER['PATH_INFO']);
                        self::$useRoute = true;
                        break;
                    }
                }
            }
        }
    }

    private static function getPathInfo()
    {
        $pathInfo = array();
        if(!empty($_SERVER['PATH_INFO'])) {
            if(C('PATH_MODEL')==2){
                $paths = explode(C('PATH_DEPR'),trim($_SERVER['PATH_INFO'],'/'));
                $pathInfo[C('VAR_MODULE')] = array_shift($paths);
                $pathInfo[C('VAR_ACTION')] = array_shift($paths);
                for($i = 0, $cnt = count($paths); $i <$cnt; $i++){
                    if(isset($paths[$i+1])) {
                        $pathInfo[$paths[$i]] = (string)$paths[++$i];
                    }elseif($i==0) {
                        $pathInfo[$pathInfo[C('VAR_ACTION')]] = (string)$paths[$i];
                    }
                }
            }else{
                $res = preg_replace('@(\w+)'.C('PATH_DEPR').'([^,\/]+)@e', '$pathInfo[\'\\1\']="\\2";', $_SERVER['PATH_INFO']);
            }
        }
        return $pathInfo;
    }

    private static function parsePathInfo()
    {
        if(!empty($_GET[C('VAR_PATHINFO')])) {
            $path = $_GET[C('VAR_PATHINFO')];
            unset($_GET[C('VAR_PATHINFO')]);
        }
        elseif(!empty($_SERVER['PATH_INFO']))
        {
            $pathInfo = $_SERVER['PATH_INFO'];
            if(0 === strpos($pathInfo,$_SERVER['SCRIPT_NAME']))
            {
                $path = substr($pathInfo, strlen($_SERVER['SCRIPT_NAME']));
            }
            else
            {
                $path = $pathInfo;
            }
        }
        else if(!empty($_SERVER['ORIG_PATH_INFO']))
        {
            $pathInfo = $_SERVER['ORIG_PATH_INFO'];
            if(0 === strpos($pathInfo, $_SERVER['SCRIPT_NAME']))
            {
                $path = substr($pathInfo, strlen($_SERVER['SCRIPT_NAME']));
            }
            else
            {
                $path = $pathInfo;
            }
        }
        elseif (!empty($_SERVER['REDIRECT_PATH_INFO'])){
            $path = $_SERVER['REDIRECT_PATH_INFO'];
        }else if(!empty($_SERVER["REDIRECT_Url"]))
        {
            $path = $_SERVER["REDIRECT_Url"];

            if(empty($_SERVER['QUERY_STRING']) || $_SERVER['QUERY_STRING'] == $_SERVER["REDIRECT_QUERY_STRING"])
            {
                $parsedUrl = parse_url($_SERVER["REQUEST_URI"]);
                if(!empty($parsedUrl['query']))
                {
                    $_SERVER['QUERY_STRING'] = $parsedUrl['query'];
                    parse_str($parsedUrl['query'], $GET);
                    $_GET = array_merge($_GET, $GET);
                    reset($_GET);
                }
                else
                {
                    unset($_SERVER['QUERY_STRING']);
                }

                reset($_SERVER);
            }
        }
        if(C('HTML_URL_SUFFIX') && !empty($path)) {
            $suffix =   substr(C('HTML_URL_SUFFIX'),1);
            $path   =   preg_replace('/\.'.$suffix.'$/','',$path);
        }
        $_SERVER['PATH_INFO'] = empty($path) ? '/' : $path;
    }
	
}

?>