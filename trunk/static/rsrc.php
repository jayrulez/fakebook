<?php

define('PAGE_NAME', 'rsrc');
define('REQUIRE_USER', false);

require_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'global.php');

$resource = $_REQUEST['get'];

$split = explode('.',$resource);
$parts = count($split);

$_valid = array('js','css');

if(!in_array($split[$parts-1],$_valid))
{
	exit();
}

$file  = THEME_PATH.TMPL_NAME;
$file .= $resource;
$cache = COMPILE_PATH.md5($file);

if(is_file($cache)&&filemtime($cache)>filemtime($file))
{
	$content = file_get_contents($cache);
	echo $content;
}else{
	if(is_file($file))
	{
		$content = file_get_contents($file);
		$content = str_replace(array('[:theme]'),array('http://'.URL.'/themes/'.TMPL_NAME),$content);
		$content = nl2br($content);
		file_put_contents($cache,$content);
		echo $content;
	}else{
		echo $file;
	}

}

?>
