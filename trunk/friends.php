<?php

define('PAGE_NAME', 'friends');

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'global.php');

require_login();

$tpl->display('friends.tpl');

?>