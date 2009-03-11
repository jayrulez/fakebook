<?php

define('IN_APP',true);

define('DS',DIRECTORY_SEPARATOR);

define('ROOT_PATH',dirname(__FILE__).DS);
define('CORE_PATH',dirname(__FILE__).DS.'Core'.DS);
define('DATA_PATH',dirname(__FILE__).DS.'Data'.DS);

define('APP_NAME','fakebook');
define('APP_PATH',ROOT_PATH.DS);

require(ROOT_PATH.'global.php');

$App = new App();

$App->run();

?>