<?php

define('PAGE_NAME', 'index');
define('REQUIRE_USER', false);

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'global.php');

$tpl->display('index.tpl');

?>
