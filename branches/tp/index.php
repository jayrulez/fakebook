<?php

define('IN_APP',true);

define('DS',DIRECTORY_SEPARATOR);

define('ROOT_PATH',dirname(__FILE__));
define('CORE_PATH',dirname(__FILE__).DS.'Core');
define('DATA_PATH',dirname(__FILE__).DS.'Data');

require_once(ROOT_PATH.DS.'global.php');

$App = new App();

$App->run();

?>