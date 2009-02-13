<?php

error_reporting(E_ALL);

define('IN_APP', true);

define('DS', DIRECTORY_SEPARATOR);

define('ROOT_PATH', dirname(__FILE__).DS);
define('CORE_PATH', dirname(__FILE__).DS.'core'.DS);
define('DATA_PATH', dirname(__FILE__).DS.'data'.DS);

define('INC_PATH', CORE_PATH.'inc'.DS);
define('LIB_PATH', CORE_PATH.'lib'.DS);
define('API_PATH', CORE_PATH.'api'.DS);

require_once(INC_PATH.'define.php');
require_once(INC_PATH.'common.php');

$GLOBALS['import_file'] = array();

import('lib.exception.myException');
import('lib.util.log');
import('lib.util.input');
import('lib.db.db');
import('lib.template.template');

import('fakebook.user');

start_app();

$tpl   = new template();
$db    = new db();
$user  = new user();

if(!C('SITE_OPEN'))
{
	$tpl->assign('message',L('_SITE_CLOSED_'));
	$tpl->display('common.tpl');
	exit();
}

/*pages that both members and guests can view*/
$global_pages = array(); 

$islogged = $user->islogged();

$tpl->assign('islogged',$islogged);

if(REQUIRE_USER == true)
{
	if(!$islogged)
	{
		redirect('signin.php');
	}
}else{
	if($islogged && !in_array($global_pages,PAGE_NAME))
	{
		redirect('home.php');
	}
}

?>
