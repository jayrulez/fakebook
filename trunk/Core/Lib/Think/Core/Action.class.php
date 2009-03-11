<?php

abstract class Action extends Base
{
    protected $name =  '';

    protected $view   =  null;

    protected $_cacheAction = array();

    protected $error  =  '';

    public function __construct()
    {
        $this->view       = View::getInstance();
        $this->name     =   $this->getActionName();

        if($this->isGet()) {
            if(C('ACTION_CACHE_ON') && in_array(ACTION_NAME,$this->_cacheAction,true)) {
                $content    =   S(md5(__SELF__));
                if($content) {
                    echo $content;
                    exit;
                }
            }
        }
		$this->_initialize();
    }

	protected function _initialize() {}
	
    protected function getActionName() {
        if(empty($this->name)) {
            $prefix     =   C('CONTR_CLASS_PREFIX');
            $suffix     =   C('CONTR_CLASS_SUFFIX');
            if($suffix) {
                $this->name =   substr(substr(get_class($this),strlen($prefix)),0,-strlen($suffix));
            }else{
                $this->name =   substr(get_class($this),strlen($prefix));
            }
        }
        return ucfirst($this->name);
    }

    protected function getParam($type,$name='',$filter='',$default='') {
            $Input   = Input::getInstance();
            $value   =  $Input->{$type}($name,$filter,$default);
            return $value;
    }

    protected function isAjax() {
        if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) ) {
            if(strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])=='xmlhttprequest')
                return true;
        }
        if(!empty($_POST[C('VAR_AJAX_SUBMIT')]) || !empty($_GET[C('VAR_AJAX_SUBMIT')])) {
            return true;
        }
        return false;
    }

    protected function isPost()
    {
        return strtolower($_SERVER['REQUEST_METHOD']) == 'post';
    }

    protected function isGet()
    {
        return strtolower($_SERVER['REQUEST_METHOD']) == 'get';
    }

    protected function isHead()
    {
        return strtolower($_SERVER['REQUEST_METHOD']) == 'head';
    }

    protected function isPut()
    {
        return strtolower($_SERVER['REQUEST_METHOD']) == 'put';
    }

    protected function isDelete()
    {
        return strtolower($_SERVER['REQUEST_METHOD']) == 'delete';
    }

    protected function cacheLockVersion($data) {
        $model  =   D($this->name);
        if($model->optimLock) {
            if(is_object($data))    $data   =   get_object_vars($data);
            if(isset($data[$model->optimLock]) && isset($data[$model->getPk()])) {
                $_SESSION[$model->getModelName().'_'.$data[$model->getPk()].'_lock_version']    =   $data[$model->optimLock];
            }
        }
    }

    protected function getModelClass()
    {
        $model  = D($this->name);
        return $model;
    }

    protected function getReturnUrl()
    {
        return url(C('DEFAULT_ACTION'));
    }

    public function display($templateFile='',$charset='',$contentType='text/html',$varPrefix='')
    {
        if(C('ACTION_CACHE_ON') && in_array(ACTION_NAME,$this->_cacheAction,true))
		{
            $content    =   $this->fetch($templateFile,$charset,$contentType,$varPrefix);
            S(md5(__SELF__),$content);
            echo $content;
        }else{
            $this->view->display($templateFile,$charset,$contentType,$varPrefix);
        }
    }

    public function fetch($templateFile='',$charset='',$contentType='text/html',$varPrefix='')
    {
        return $this->view->fetch($templateFile,$charset,$contentType,$varPrefix,false);
    }

    public function layout($templateFile='',$charset='',$contentType='text/html',$varPrefix='',$display=true)
    {
        return $this->view->layout($templateFile,$charset,$contentType,$varPrefix,$display);
    }

    public function assign($name,$value='')
    {
        $this->view->assign($name,$value);
    }

    public function trace($name,$value='')
    {
        $this->view->trace($name,$value);
    }

    public function get($name)
    {
        return $this->view->get($name);
    }

    public function __set($name,$value) {
        $this->assign($name,$value);
    }

    public function __get($name) {
        return $this->get($name);
    }

    public function __call($method,$parms) {
        if(strtolower($method) == strtolower(ACTION_NAME.C('ACTION_SUFFIX')))
		{
            if(method_exists($this,'_empty')) {
                $this->_empty($method,$parms);
            }else {
                if(file_exists_case(C('TMPL_FILE_NAME'))) {
                    $this->display();
                }else{
                    throw_exception(L('_ERROR_ACTION_').ACTION_NAME);
                }
            }
        }else{
            throw_exception(__CLASS__.':'.$method.L('_METHOD_NOT_EXIST_'));
        }
    }

    public function error($errorMsg,$ajax=false)
    {
        if($ajax || $this->isAjax()) {
            $this->ajaxReturn('',$errorMsg,0);
        }else {
            $this->assign('error',$errorMsg);
            $this->forward();
        }
    }

    public function success($message,$ajax=false)
    {
        if($ajax || $this->isAjax()) {
            $this->ajaxReturn('',$message,1);
        }else {
            $this->assign('message',$message);
            $this->forward();
        }
    }

    public function ajaxReturn($data='',$info='',$status='',$type='')
    {
        if(C('WEB_LOG_RECORD') || C('SQL_DEBUG_LOG')) Log::save();

        $result  =  array();
        if($status === '') {
            $status  = $this->get('error')?0:1;
        }
        if($info=='') {
            if($this->get('error')) {
                $info =   $this->get('error');
            }elseif($this->get('message')) {
                $info =   $this->get('message');
            }
        }
        $result['status']  =  $status;
        $result['info'] =  $info;
        $result['data'] = $data;
        if(empty($type)) $type  =   C('AJAX_RETURN_TYPE');
        if(strtoupper($type)=='JSON') {
            header("Content-Type:text/html; charset=".C('OUTPUT_CHARSET'));
            exit(json_encode($result));
        }elseif(strtoupper($type)=='XML'){
            header("Content-Type:text/xml; charset=".C('OUTPUT_CHARSET'));
            exit(xml_encode($result));
        }elseif(strtoupper($type)=='EVAL'){
            header("Content-Type:text/html; charset=".C('OUTPUT_CHARSET'));
            exit($data);
        }else{
            // TODO Increase in other formats
        }
    }

    public function forward($action='_dispatch_jump',$module='',$app='@',$exit=false,$delay=0)
    {
        if(!empty($delay)) {
            sleep(intval($delay));
        }
        if(is_array($action)) {
            call_user_func($action);
        }else {
            if(empty($module)) {
                call_user_func(array(&$this,$action));
            }else{
                $class =     A($module,$app);
                call_user_func(array(&$class,$action));
            }
        }
        if($exit) {
            exit();
        }else {
            return ;
        }
    }

    public function redirect($action,$module='',$route='',$app=APP_NAME,$params=array(),$delay=0,$msg='') {
        if(empty($module)) {
            $module = defined('C_MODULE_NAME')?C_MODULE_NAME:MODULE_NAME;
        }
        $url    =   url($action,$module,$route,$app,$params);
        redirect($url,$delay,$msg);
    }

    private function _dispatch_jump()
    {
        if($this->isAjax() ) {
            if($this->get('_ajax_upload_')) {
                header("Content-Type:text/html; charset=".C('OUTPUT_CHARSET'));
                exit($this->get('_ajax_upload_'));
            }else {
                $this->ajaxReturn();
            }
        }
        if($this->get('error') ) {
            $msgTitle    =   L('_OPERATION_FAIL_');
        }else {
            $msgTitle    =   L('_OPERATION_SUCCESS_');
        }
        $this->assign('msgTitle',$msgTitle);
        if($this->get('message')) {
            if(!$this->get('waitSecond'))
                $this->assign('waitSecond',"1");
            if(!$this->get('jumpUrl'))
                $this->assign("jumpUrl",$_SERVER["HTTP_REFERER"]);
        }
        if($this->get('error')) {
            if(!$this->get('waitSecond'))
                $this->assign('waitSecond',"3");
            if(!$this->get('jumpUrl'))
                $this->assign('jumpUrl',"javascript:history.back(-1);");
        }
        if($this->get('closeWin')) {
            $this->assign('jumpUrl','javascript:window.close();');
        }
        $this->display(C('ACTION_JUMP_TMPL'));
        exit ;
    }

    protected function _404($message='',$jumpUrl='',$waitSecond=3) {
        $this->assign('msg',$message);
        if(!empty($jumpUrl)) {
            $this->assign('jumpUrl',$jumpUrl);
            $this->assign('waitSecond',$waitSecond);
        }
        $this->display(C('ACTION_404_TMPL'));
    }

    protected function saveToken(){
        $tokenType = C('TOKEN_TYPE');
        $token = $tokenType(microtime(TRUE));
        Session::set(C('TOKEN_NAME'),$token);
    }

    protected function isValidToken($reset=false){
        $tokenName   =  C('TOKEN_NAME');
        if($_REQUEST[$tokenName]==Session::get($tokenName)){
            $valid=true;
            $this->saveToken();
        }else {
            $valid=false;
            if($reset)    $this->saveToken();
        }
        return $valid;
    }

}

?>