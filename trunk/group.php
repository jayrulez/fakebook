<?php

define('PAGE_NAME', 'group');

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'global.php');

require_login();

$tpl->display('group.tpl');

?>