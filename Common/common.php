<?php

function css_browser_id()
{
	$_browser      = get_client_browser();
	$CssBrowserId  = 'general';
	
	if(is_file(CONFIG_PATH.'cssBrowsers.php'))
	{
		$_agentArray = include(CONFIG_PATH.'cssBrowsers.php'); 
	}
	
	foreach($_agentArray as $agent => $id)
	{
		if(stristr($_browser,$agent) !== false)
		{
			$cssBrowserId = $id;
			break;
		}
	}

	return $cssBrowserId;
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
	$info = D('User')->find($uid);
	return $info;
}

function getUserName($uid){
	$info = getUserInfo($uid);
	return $info['display_name'];
}

/* get user info by id */
function getGroupInfo($gid){
	$info = D('Group')->find($gid);
	return $info;
}

function getGroupName($gid){
	$info = getGroupInfo($gid);
	return $info['name'];
}

function getTypeById($id){
	if(getUserInfo($id)){
		return 'user';
	}
	
	if(getGroupInfo($id)){
		return 'group';
	}
	
	return false;
}

function isWallOwner($id,$userId,$wid=0,$fromid=0)
{
	if(!$wid)
	{
		if(!$post = D('Wall')->find($id))
			return false;
		$wid = $post['wid'];
		$fromid = $post['fromid'];
	}

	if($userId == $fromid)
		return true;
	
	if($userId == $wid)
		return true;
		
}

function Pager($count,$page,$listRows,$wid)
{
  $totalPages = (int)($count / $listRows) + 1;
  $page = ($page > $totalPages) ? $totalPages : $page;
  
  //show First button
  if($page > 3 && $totalPages > 5)
    $output = "<li><a href=\"".url('','','wall','app',array('id'=>$wid,'page'=>1))."\">First</a></li>";
  //show Prev button
  if($page > 1 && $totalPages > 1)
    $output .= "<li><a href=\"".url('','','wall','app',array('id'=>$wid,'page'=>$page-1))."\">Prev</a></li>";
  //show page-4
  if($page > 4 && $totalPages > 5 && $totalPages < $page + 2)
    $output .= "<li><a href=\"".url('','','wall','app',array('id'=>$wid,'page'=>$page-4))."\">".($page - 4)."</a></li>";
  //show page-3
  if($page > 3 && $totalPages > 4 && $totalPages < $page + 1)
    $output .= "<li><a href=\"".url('','','wall','app',array('id'=>$wid,'page'=>$page-3))."\">".($page - 3)."</a></li>";
  //show page-2
  if($page > 2 && $totalPages > 5)
    $output .= "<li><a href=\"".url('','','wall','app',array('id'=>$wid,'page'=>$page-2))."\">".($page - 2)."</a></li>";
  //show page-1
  if($page > 1 && $totalPages > 2)
    $output .= "<li><a href=\"".url('','','wall','app',array('id'=>$wid,'page'=>$page-1))."\">".($page - 1)."</a></li>";
  //show current page
  if($totalPages > 1)
    $output .= "<li class=\"current\"><a href=\"".url('','','wall','app',array('id'=>$wid,'page'=>$page))."\">".$page."</a></li>";
  //show page+1
  if($totalPages > $page)
    $output .= "<li><a href=\"".url('','','wall','app',array('id'=>$wid,'page'=>$page+1))."\">".($page + 1)."</a></li>";
  //show page+2
  if($totalPages > $page + 1)
    $output .= "<li><a href=\"".url('','','wall','app',array('id'=>$wid,'page'=>$page+2))."\">".($page + 2)."</a></li>";
  //show page+3
  if($totalPages > $page + 2 && $page < 3)
    $output .= "<li><a href=\"".url('','','wall','app',array('id'=>$wid,'page'=>$page+3))."\">".($page + 3)."</a></li>";
  //show page+4
  if($totalPages > $page + 3 && $page < 2)
    $output .= "<li><a href=\"".url('','','wall','app',array('id'=>$wid,'page'=>$page+4))."\">".($page + 4)."</a></li>";
  //show Next button
  if($totalPages > $page)
    $output .= "<li><a href=\"".url('','','wall','app',array('id'=>$wid,'page'=>$page+1))."\">Next</a></li>";
  //show Last button
  if($totalPages > $page + 2)
    $output .= "<li><a href=\"".url('','','wall','app',array('id'=>$wid,'page'=>$totalPages))."\">Last</a></li>";
  
  return $output;
}
?>