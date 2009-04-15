<?php

return array(

	/* Dispatch Config */
	'DISPATCH_ON'			=>	true,
	'DISPATCH_NAME'			=>	'Think',
	'URL_MODEL'				=>	1,
	'PATH_MODEL'			=>	2,
	'PATH_DEPR'				=>	'/',
	'ROUTER_ON'				=>	true,
	'COMPONENT_DEPR'		=>	'@',
	'COMPONENT_TYPE'		=>	1,
	'URL_CASE_INSENSITIVE'	=>   false,
	'URL_AUTO_REDIRECT'		=>   false,
	'CHECK_FILE_CASE'		=>   false,

	/* Log Config */
	'WEB_LOG_RECORD'		=>	false,
	'LOG_RECORD_LEVEL'		=>	array('EMERG','ALERT','CRIT','ERR'),
	'LOG_FILE_SIZE'			=>	2097152,

	/* PlugIn Config */
	'THINK_PLUGIN_ON'		=>	false,
	'APP_AUTO_SETUP'		=>	false,

	'LIMIT_RESFLESH_ON'		=>	false,
	'LIMIT_REFLESH_TIMES'	=>	3,

	/* Debug Config */
	'DEBUG_MODE'			=>	false,	 
	'ERROR_MESSAGE'			=>	L('_ERROR_MESSAGE_'),	
	'ERROR_PAGE'			=>	'',
	'SHOW_ERROR_MSG'		=>	true,

	/* Var Config */
	'VAR_PATHINFO'			=>	's',
	'VAR_MODULE'			=>	'm',
	'VAR_ACTION'			=>	'a',
	'VAR_ROUTER'			=>	'r',		
	'VAR_FILE'				=>	'f',
	'VAR_PAGE'				=>	'p',
	'VAR_LANGUAGE'			=>	'l',
	'VAR_TEMPLATE'			=>	't',
	'VAR_AJAX_SUBMIT'		=>	'ajax',
	'VAR_RESFLESH'			=>	'h',

	'DEFAULT_MODULE'		=>	'Index',
	'DEFAULT_ACTION'		=>	'index',
	'MODULE_REDIRECT'		=>	'',
	'ACTION_REDIRECT'		=>	'',

	/* Template Config */
	'TMPL_CACHE_ON'			=>	true,
	'TMPL_CACHE_TIME'		=>	-1,
	'TMPL_SWITCH_ON'		=>	false,
	'DEFAULT_TEMPLATE'		=>	'default',
	'TEMPLATE_SUFFIX'		=>	'.html',
	'CACHFILE_SUFFIX'		=>	'.php',
	'TEMPLATE_CHARSET'		=>	'utf-8',
	'OUTPUT_CHARSET'		=>	'utf-8',
	'OUTPUT_CONTENT_TYPE'	=>	'utf-8',
	'XML_ENCODING'			=>	'utf-8',
	'DEFAULT_LAYOUT'		=> 	'Layout:index',
	'AUTO_DETECT_THEME'		=>	false,
	'TMPL_VAR_IDENTIFY'		=>	'',

	'CONTR_CLASS_PREFIX'	=>	'',
	'CONTR_CLASS_SUFFIX'	=>	'Action',
	'ACTION_PREFIX'			=>	'',
	'ACTION_SUFFIX'			=>	'',
	'MODEL_CLASS_PREFIX'	=>	'',
	'MODEL_CLASS_SUFFIX'	=>	'Model',
	'AUTO_NAME_IDENTIFY'	=>	true,
	'DEFAULT_MODEL_APP'		=>	'@',

	/* Html Config */
	'HTML_FILE_SUFFIX'		=>	'.shtml',
	'HTML_CACHE_ON'			=>	false,
	'HTML_CACHE_TIME'		=>	60,
	'HTML_READ_TYPE'		=>	1,
	'HTML_URL_SUFFIX'		=>	'',

	/* Lang Config */
	'LANG_SWITCH_ON'		=>	true,
	'AUTO_DETECT_LANG'      =>	true,
	'LANG_CACHE_ON'			=>	true,
	'DEFAULT_LANGUAGE'		=>	'en-US',
	'LANG_ID'				=>	'en',
	'HTM_LANG_ID'			=>	'en',
	'XML_LANG_ID'			=>	'en',
	'TIME_ZONE'				=>	'UTC',

	/* Auth Config */		
	'USER_AUTH_KEY'			=>	'authId',
	'ADMIN_AUTH_KEY'		=>	'administrator',
	'AUTH_PWD_ENCODER'		=>	'md5',

	/* SESSION Config */
	'SESSION_NAME'			=>	'ThinkID',
	'SESSION_PATH'			=>	'',
	'SESSION_TYPE'			=>	'File',
	'SESSION_EXPIRE'		=>	'300000',
	'SESSION_TABLE'			=>	'session',
	'SESSION_CALLBACK'		=>	'',

	/* DB Config */
	'DB_CHARSET'			=>	'utf8',
	'DB_DEPLOY_TYPE'		=>	0,
	'DB_RW_SEPARATE'		=>	false,
	'SQL_DEBUG_LOG'			=>	false,
	'DB_FIELDS_CACHE'		=>	true,
	'SQL_MODE'				=>	'',
	'FIELDS_DEPR'			=>	',',
	'TABLE_DESCRIBE_SQL'	=>	'',
	'FETCH_TABLES_SQL'		=>  '',
	'DB_TRIGGER_PREFIX'		=>	'tr_',
	'DB_SEQUENCE_PREFIX'	=>	'seq_',
	'DB_CASE_LOWER'			=>	true, 

	/* DATA CACHE Config */
	'DATA_CACHE_TIME'		=>	-1,
	'DATA_CACHE_COMPRESS'	=>	false,
	'DATA_CACHE_CHECK'		=>	false,
	'DATA_CACHE_TYPE'		=>	'File',
	'DATA_CACHE_SUBDIR'		=>	false,
	'DATA_CACHE_TABLE'		=>	'cache',
	'CACHE_SERIAL_HEADER'	=>	"<?php\r\n//",
	'CACHE_SERIAL_FOOTER'	=>	"\r\n?".">",
	'SHARE_MEM_SIZE'		=>	1048576,
	'ACTION_CACHE_ON'		=>	false,

	/* trace config */
	'SHOW_RUN_TIME'			=>	true,
	'SHOW_ADV_TIME'			=>	true,
	'SHOW_DB_TIMES'			=>	true,
	'SHOW_CACHE_TIMES'		=>	false,
	'SHOW_USE_MEM'			=>	false,
	'SHOW_PAGE_TRACE'		=>	false,

	/* Template engine config */
	'TMPL_ENGINE_TYPE'		=>	'Think',
	'TMPL_DENY_FUNC_LIST'	=>	'echo,exit',
	'TMPL_L_DELIM'			=>	'{',
	'TMPL_R_DELIM'			=>	'}',
	'TAGLIB_BEGIN'			=>	'<',
	'TAGLIB_END'			=>	'>',
	'TAG_NESTED_LEVEL'		=>	3,

	/* Cookie Config */
	'COOKIE_EXPIRE'			=>	3600,
	'COOKIE_DOMAIN'			=>	'',
	'COOKIE_PATH'			=>	'/',
	'COOKIE_PREFIX'			=>	'fb_',
	'COOKIE_SECRET_KEY'     =>	'',

	/* Page List Config */
	'PAGE_NUMBERS'			=>	5,
	'LIST_NUMBERS'			=>	20,

	'AJAX_RETURN_TYPE'		=>	'JSON',
	'DATA_RESULT_TYPE'		=>	0,

	'AUTO_LOAD_PATH'		=>	'Think.Util.',
	'AUTO_LOAD_CLASS'		=>	'',
	'CALLBACK_LOAD_PATH'	=>	'',
	'UPLOAD_FILE_RULE'		=>	'uniqid',
	'LIKE_MATCH_FIELDS'		=>	'',
	'ACTION_JUMP_TMPL'		=>	'Public:error',
	'ACTION_404_TMPL'		=>	'Public:error',
	'TOKEN_ON'				=>	true,
	'TOKEN_NAME'			=>	'fb_html_token',
	'TOKEN_TYPE'			=>	'md5',
	'APP_DOMAIN_DEPLOY'		=>	false,
);
?>