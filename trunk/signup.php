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

$tpl->display('signup.tpl');

?>
