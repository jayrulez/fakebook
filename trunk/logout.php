<?php

define('PAGE_NAME', 'logout');

require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'global.php');

session::destroy();
redirect('/');

?>