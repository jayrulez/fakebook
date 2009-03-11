<?php 

define('THINK_PATH', './ThinkPHP');

define('APP_NAME', 'Fakebook');
define('APP_PATH', '.');

require(THINK_PATH."/ThinkPHP.php");

$App = new App();

$App->run();

?>