<?php

define('PAGE_NAME', 'profile');

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'global.php');

require_login();

$profileId = !empty($_REQUEST['id']) ? $_REQUEST['id'] : 0;

$tpl->display('profile.tpl');

?>