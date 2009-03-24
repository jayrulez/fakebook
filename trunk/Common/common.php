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
		$CssBrowserId = 'chrome';

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

/**
 * format date
 * 
 */
function formatDate($time,$timezone=0){
  $interval = time() - $time;
  $time -= $timezone*60*60;
  if($interval <= 60){
    return $interval.L('_TIME_MINUTE_AGO_');//'1 minute ago';
    exit();
  } else if($interval > 60 && $interval <= 60*60){
    return (int)($interval / 60).L('_TIME_MINUTES_AGO_');//' minutes ago';
    exit();
  } else if($interval > 60*60 && $interval <= 12*60*60){
    return (int)($interval / (60*60)).L('_TIME_HOURS_AGO_');//' hours ago';
    exit();
  } else if($interval > 12*60*60 && $interval <= 24*60*60){
    return L('_TIME_TODAY_');//'Today';
    exit();
  } else if($interval > 24*60*60 && $interval <= 2*24*60*60){
    return L('_TIME_YESTODAY_');//'Yestoday';
    exit();
  } else if($interval > 2*24*60*60 && $interval <= 7*24*60*60){
    return (int)($interval / (24*60*60)).L('_TIME_DAYS_AGO_');//' days ago';
    exit();
  } else {
    return date(L('_TIME_DATE_SHORT_'),$time);
    exit();
  }
}

/* get user info by id */
function getUserInfo($uid){
	$userinfo = D('User')->find($uid);
	return $userinfo;
}
function getUserName($uid){
	$info = getUserInfo($uid);
	return $info['truename']';
}
?>