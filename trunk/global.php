<?php

error_reporting(E_ALL);

define('IN_APP', true);

define('APP_NAME','fakebook');

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
import('lib.db.db');
import('lib.template.template');

import('fakebook.user');
import('fakebook.profile');

start_app();

$tpl     = new template();
$db      = new db();
$user    = new user();
$profile = new profile();

if(!C('SITE_OPEN'))
{
	$tpl->assign('message',L('_SITE_CLOSED_'));
	$tpl->display('common.tpl');
	exit();
}

if(cookie::is_set('loginId')&&cookie::is_set('loginPwd'))
{
	$tpl->assign('loginId_val',cookie::get('loginId'));
	$tpl->assign('loginPwd_val',cookie::get('loginPwd'));
}else{
	$tpl->assign('loginId_val','');
	$tpl->assign('loginPwd_val','');
}

$islogged = $user->islogged();
$tpl->assign('islogged',$islogged);

?>
