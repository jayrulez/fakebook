<?php

function require_login($flag=true)
{
	if($flag == true)
	{
		if(!$GLOBALS['islogged'])
		{
			redirect('signin.php');
		}
	}else{
		if($GLOBALS['islogged'])
		{
			redirect('home.php');
		}
	}
}

function cssBrowserId()
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
	
	if($CssBrowserId==null)
		$CssBrowserId = 'general';
		
	return $CssBrowserId;
}

?>