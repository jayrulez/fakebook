<?php

define('PAGE_NAME', 'signin');

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'global.php');

require_login(false);

$tpl->display('signin.tpl');

?>
