<?php

define('IN_APP',true);

define('DS',DIRECTORY_SEPARATOR);

define('ROOT_PATH',dirname(__FILE__));
define('CORE_PATH',dirname(__FILE__).'Core');

//define app_path and app_name

require_once(ROOT_PATH.'global.php');

$App = new App();

$App->run();

?>