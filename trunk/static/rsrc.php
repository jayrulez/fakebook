<?php

define('PAGE_NAME', 'rsrc');
require_once(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'global.php');

$resource = $_REQUEST['get'];

$split = explode('.',$resource);
$parts = count($split);

$_valid = array('js','css');

if(!in_array($split[$parts-1],$_valid))
{
	exit();
}

if($split[$parts-1]=='js')
{
	header('Content-Type: text/javascript');
}else{
	header('Content-Type: text/css');
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
		$content = str_replace(array('[:theme]'),array(NON_SECURE_PROTOCOL.URL.'/themes/'.TMPL_NAME),$content);
		$subdir_array = array('static');
		for($i=0;$i<count($subdir_array);$i++)
		{
			if(stristr($content,'/'.$subdir_array[$i]))
			{
				$content = str_replace(array('/'.$subdir_array[$i]),array(''),$content);
			}
		}
		file_put_contents($cache,$content);
		echo $content;
	}else{
		echo $file;
	}
}

?>
