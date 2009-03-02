<?php

define('PAGE_NAME', 'profile');

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'global.php');

require_login();

$tpl->display('profile.tpl');

?>