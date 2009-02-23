<?php

define('PAGE_NAME', 'login');

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'global.php');

require_login(false);
$error = null;

if(isset($_POST['login']))
{
	unset($_POST['login']);
	
	$loginId    = input::sanitize_login_data($_POST['email']);
	$loginPwd   = input::sanitize_login_data($_POST['pass']);
	$autosignin = isset($_POST['persistent']) ? true : false;
	$loginPwd   = md5($loginPwd);
	
	$loginRes = $user->login($loginId,$loginPwd,$autosignin);
	
	if($loginRes==1)
	{
		$error = L('incorrect_login_id');
	}else if($loginRes==2)
	{
		$error = L('incorrect_login_pwd');
	}else if($loginRes==3)
	{
		$error = L('unknown_login_error');
	}else{
		redirect('home.php');
	}
}

$tpl->assign('error',$error);
$tpl->display('login.tpl');

?>