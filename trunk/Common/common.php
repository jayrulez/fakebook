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
	$item = $_REQUEST['item'];
	$type = $_REQUEST['type'];

	if($type=='css')
	{
		header('Content-type: text/css');
	}else if($type=='js')
	{
		header('Content-type: text/javascript');
	}

	$resource_url = str_replace('-',DS,$item).'.'.$type;
	$file_path    = TMPL_PATH.'Public'.DS;
	$file         = $file_path.$resource_url;
	$cache        = RESOURCE_DATA_PATH.md5($resource_url).'.'.$type;

	$content      = '';

	if(is_file($cache)&&filemtime($cache)>filemtime($file))
	{
		$content .= file_get_contents($cache);
	}else{
		if(is_file($file))
		{
			$content .= file_get_contents($file);

			if(C(RESOURCE_CACHE_ON))
			{
				file_put_contents($cache,$content);
			}
		}else{
			$content .= L('_RESOURCE_NOT_EXIST_');
		}
	}

	$content = str_replace('[theme_url]',C('THEME_URL'),$content);

	echo $content;
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
    return date(L('_TIME_TODAY_'),$time);//'Today';
    exit();
  } else if($interval > 24*60*60 && $interval <= 2*24*60*60){
    return date(L('_TIME_YESTODAY_'),$time);//'Yestoday';
    exit();
  } else {
    return date(L('_TIME_FORMAT_'),$time);
    exit();
  }
}

/* get user info by id */
function getUserInfo($uid)
{
	$info = D('User')->find($uid);
	return $info;
}

function getUserName($uid)
{
	$info = getUserInfo($uid);
	return $info['name'];
}

function getProfilePicture($uid,$size='L')
{
	$info = getUserInfo($uid);
	return 'Data'.DS.APP_NAME.DS.UPLOAD_DIR.DS.$info['pic'];
}

/* get group info by id */
function getGroupInfo($gid)
{
	$info = D('Group')->find($gid);
	return $info;
}

function getGroupName($gid)
{
	$info = getGroupInfo($gid);
	return $info['name'];
}

function getGroupMember($gid)
{
	$map['gid'] = $gid;
	$groupMember = D('GroupMember')->findAll($map);
	return $groupMember;
}

/*
 *   show page controller 
 *   (need redesign)
 *
 */
function Pager($wall,$type)
{
  $count = $wall['count'];
  $page = $wall['page'];
  $listRows = $wall['listRows'];
  $wid = $wall['wid'];
  $url = url('','','wall','',array('type'=>$type,'id'=>$wid));
  
  $totalPages = ($count%$listRows)>0?((int)($count/$listRows)+1):($count/$listRows);

  $page = ($page > $totalPages) ? $totalPages : $page;
  
  //show First button
  if($page > 3 && $totalPages > 5)
    $output = "<li><a href=\"".$url."\">".L('_PAGE_FIRST_')."</a></li>";
  //show Prev button
  if($page > 1 && $totalPages > 1)
    $output .= "<li><a href=\"".$url."/".($page-1)."\">".L('_PAGE_PREV_')."</a></li>";
  //show page-4
  if($page > 4 && $totalPages > 4 && $totalPages < $page + 3)
    $output .= "<li><a href=\"".$url."/".($page-4)."\">".($page - 4)."</a></li>";
  //show page-3
  if($page > 3 && $totalPages > 3 && $totalPages < $page + 2)
    $output .= "<li><a href=\"".$url."/".($page-3)."\">".($page - 3)."</a></li>";
  //show page-2
  if($page > 2 && $totalPages > 2)
    $output .= "<li><a href=\"".$url."/".($page-2)."\">".($page - 2)."</a></li>";
  //show page-1
  if($page > 1 && $totalPages > 1)
    $output .= "<li><a href=\"".$url."/".($page-1)."\">".($page - 1)."</a></li>";
  //show current page
  if($totalPages > 1)
    $output .= "<li class=\"current\"><a href=\"".$url."/".($page)."\">".$page."</a></li>";
  //show page+1
  if($totalPages > $page)
    $output .= "<li><a href=\"".$url."/".($page+1)."\">".($page + 1)."</a></li>";
  //show page+2
  if($totalPages > $page + 1)
    $output .= "<li><a href=\"".$url."/".($page+2)."\">".($page + 2)."</a></li>";
  //show page+3
  if($totalPages > $page + 2 && $page < 3)
    $output .= "<li><a href=\"".$url."/".($page+3)."\">".($page + 3)."</a></li>";
  //show page+4
  if($totalPages > $page + 3 && $page < 2)
    $output .= "<li><a href=\"".$url."/".($page+4)."\">".($page + 4)."</a></li>";
  //show Next button
  if($totalPages > $page)
    $output .= "<li><a href=\"".$url."/".($page+1)."\">".L('_PAGE_NEXT_')."</a></li>";
  //show Last button
  if($totalPages > $page + 2 && $totalPages > 5)
    $output .= "<li><a href=\"".$url."/".($totalPages)."\">".L('_PAGE_LAST_')."</a></li>";
  
  return $output;
}
?>