<?php

define('PAGE_NAME', 'account');

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'global.php');

require_login();

$tpl->display('account.tpl');

?>