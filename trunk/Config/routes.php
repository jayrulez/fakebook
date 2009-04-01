<?php

return array
(
	'static'	=>	array('Public','_static','type,item'),
	'jsLang'	=>	array('Public','_jsLang','item'),

	'home'		=>	array('Home','index'),
	'profile'	=>	array('Profile','index','id'),
	'people'	=>	array('Profile','people','username,id'),
	'group'		=>	array('Group','index','id'),
	'groups'	=>	array('Groups','index'),
	'inbox'		=>	array('Inbox','index'),
	'friends'	=>	array('Friends','index','id'),

	'wall@'		=>	array(
						array('/^\/(g|u)\/(\d+)\/(\d+)/','Wall','index','type,wid,page'),
						array('/^\/(g|u)\/(\d+)/','Wall','index','type,wid'),
					),

	'login'		=>	array('Public','login'),
	'logout'	=>	array('Public','logout'),
	'ERROR'		=>	array('Public','error'),
)

?>