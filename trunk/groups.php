<?php

define('PAGE_NAME', 'groups');

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'global.php');

require_login();

$tpl->display('groups.tpl');

?>