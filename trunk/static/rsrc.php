<?php

define('PAGE_NAME', 'rsrc');
define('REQUIRE_USER', false);

require_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'global.php');

$resource = $_REQUEST['get'];

$split = explode('.',$resource);
$parts = count($split);

$_valid = array('js','css');

if(!in_array($_valid,$split[$parts-1]))
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
		$content = str_replace('[:static]','http://'.DOMAIN_NAME.'themes'.TMPL_NAME,$content);
		echo $content;
		file_put_contents($cache,$content);
	}else{
		echo '/*no file*/';
	}

}

?>
