<?php

define('PAGE_NAME', 'inbox');

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'global.php');

require_login();

$tpl->display('inbox.tpl');

?>