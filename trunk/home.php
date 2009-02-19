<?php

define('PAGE_NAME', 'home');

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'global.php');

require_login(false);

$tpl->display('home.tpl');

?>
