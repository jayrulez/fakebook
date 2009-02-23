<?php

define('PAGE_NAME', 'signup');

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'global.php');

require_login(false);

if(!C('SIGNUP_OPEN'))
{
	$tpl->assign('message',L('_SIGNUP_CLOSED_'));
	$tpl->display('common.tpl');
	exit();
}

/*$error = null;

$signupRes = $user->create();

if($signupRes==1)
{
	$error = '';
}else if($signupRes==2)
{
	$error = '';
}else{
	
}

if(!empty($_POST[C('VAR_AJAX_SUBMIT')];))
{
	return $error
}else{
	$this->assign('error',$error);
}*/

$tpl->display('signup.tpl');

?>