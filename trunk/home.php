<?php

define('PAGE_NAME', 'index');

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'global.php');

require_login();

$tpl->display('home.tpl');

?>
