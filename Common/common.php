<?php

function css_browser_id()
{
	$_browser      = get_client_browser();
	$CssBrowserId = null;
	
	if(stristr($_browser, 'msie 6') !== false)
		$CssBrowserId = 'ie6';

	if(stristr($_browser, 'msie 7') !== false)
		$CssBrowserId = 'ie7';

	if(stristr($_browser, 'opera') !== false)
		$CssBrowserId = 'opera';

	if(stristr($_browser, 'firefox/2') !== false)
		$CssBrowserId = 'ff2';

	if(stristr($_browser, 'firefox/3') !== false)
		$CssBrowserId = 'ff3';

	if(stristr($_browser, 'Chrome') !== false)
		$CssBrowserId = 'gc';

	if($CssBrowserId==null)
		$CssBrowserId = 'general';
		
	return $CssBrowserId;
}

function _static()
{
	$resource = $_REQUEST['item'];
	$resource = str_replace('-','/',$resource);
	$split    = explode('.',$resource);
	$parts    = count($split);
	$valid    = array('js','css');
	$ext      = $split[$parts-1];

	if(!in_array($ext,$valid))
	{
		echo LParse(L('_RESOURCE_NOT_VALID_'),$resource);
	}

	if($ext=='js')
	{
		header('Content-Type: text/javascript');
	}else if($ext=='css')
	{
		header('Content-Type: text/css');
	}

	$file  = ROOT_PATH.THEMES_DIR.'/Public/';
	$file .= $resource;

	$cache = CACHE_PATH.md5($file).'.'.$ext;

	if(is_file($cache)&&filemtime($cache)>filemtime($file))
	{
		$content = file_get_contents($cache);
		echo $content;
	}else{
		if(is_file($file))
		{
			$content = file_get_contents($file);
			$content = str_replace('[theme_url]',C('SITE_URL').APP_PUBLIC_URL,$content);
			file_put_contents($cache,$content);
			echo $content;
		}else{
			echo LParse(L('_RESOURCE_NOT_EXIST_'),$file);
		}
	}
}

function _jsLang()
{
	header('Content-Type: text/javascript');
	$resource = $_REQUEST['item'];
	$lang     = explode('_',$resource);
	$lang     = $lang[0];
	if(LANG_SET==$lang)
	{
		$split    = explode('.',$resource);
		$parts    = count($split);
		$ext      = $split[$parts-1];
		$cache    = CACHE_PATH.md5($lang).'_'.MODULE_NAME.'.'.$ext;

		if(is_file($cache))
		{
			$content = file_get_contents($cache);
			echo $content;
		}else{
			if(is_file(RUNTIME_PATH.MODULE_NAME.'_'.$lang.'_lang.php'))
			{
				$content = 'var _string_table = {';
				$items = include(RUNTIME_PATH.MODULE_NAME.'_'.$lang.'_lang.php');
			
				foreach($items as $var => $val)
				{
					$content .= '"'.$var.'":"'.$val.'",';
				}
				$content .= '};';
			
				file_put_contents($cache,$content);
				echo $content;
			}
		}
	}else{
		//lang file not exist
	}  
}

?>