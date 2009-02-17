<?php

define('PAGE_NAME', 'signin');

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'global.php');

require_login(false);
$error = false;


if(isset($_POST['submit']))
{
	unset($_POST['submit']);
	
	$loginId    = input::sanitize_login_data($_POST['loginId']);
	$loginPwd   = input::sanitize_login_data($_POST['loginPwd']);
	$autosignin = isset($_POST['autosignin']) ? true : false;
	$loginPwd = md5($loginPwd);
	
	if($user->login($loginId,$loginPwd,$autosignin)==1)
	{
		$error = L('incorrect_login_id');
	}else if($user->login($loginId,$loginPwd,$autosignin)==2)
	{
		$error = L('incorrect_login_pwd');
	}else if($user->login($loginId,$loginPwd,$autosignin)==3)
	{
		$error = L('unknown_login_error');
	}else{
		redirect('home.php');
	}
}

$tpl->assign('error',$error);
$tpl->display('signin.tpl');

?>
