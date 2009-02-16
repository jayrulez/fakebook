<?php

return array
(
        'WEB_LOG_RECORD'                        =>      false,
    	'LOG_RECORD_LEVEL'       =>   array('EMERG','ALERT','CRIT','ERR'),
        'LOG_FILE_SIZE'                         =>      2097152, 

	'SITE_OPEN'	=>	true,
	'SIGNUP_OPEN'	=>	true,
	'DBHOST'	=>	'localhost',
	'DBNAME'	=>	'fakebook',
	'DBUSER'	=>	'root',
	'DBPASS'	=>	'',
	'DB_PREFIX'	=>	'fb_',

	'CHECK_FILE_CASE'  	=>  	false,

	'TMPL_CACHE_ON'		=>	true,
	'TMPL_SWITCH_ON'	=>	false,
	'AUTO_DETECT_TMPL'	=>	false,
	'DEFAULT_TMPL'		=>	'default',
	'VAR_TMPL'		=>	't',

	'LANG_CACHE_ON'		=> 	true,
	'LANG_SWITCH_ON'	=>	true,
	'AUTO_DETECT_LANG'	=>	true,
	'DEFAULT_LANG'		=>	'en-us',
	'VAR_LANG'		=>	'l',
	'DEFAULT_LANG_ID'	=>	'en',

	'XML_ENCODING'		=>	'utf-8',
	'OUTPUT_CONTENT_TYPE'	=>	'text/html',
	'OUTPUT_CHARSET'	=>	'utf-8',

	'GLOBAL_COOKIE_ON'	=>	true,
        'COOKIE_EXPIRE'		=>      3600,      
        'COOKIE_DOMAIN'		=>      get_cookie_domain(),   
        'COOKIE_PATH'		=>      COOKIE_PATH,                  
        'COOKIE_PREFIX'		=>      'fb_',
	'COOKIE_SECRET_KEY'     =>   	'',

	'SESSION_NAME'				=>	'sid',	
	'SESSION_PATH'				=>	SESSION_PATH,		
	'SESSION_TYPE'				=>	'File',	
	'SESSION_EXPIRE'			=>	'300000',	
	'SESSION_TABLE'				=>	C('DB_PREFIX').'session',
	'SESSION_CALLBACK'		=>	'',
	
	'USER_AUTH_KEY'		=>	'userId',
);

?>
