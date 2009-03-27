<?php

return array
(
	'static'	=>	array('Public','_static','item'),
	'jsLang'	=>	array('Public','_jsLang','item'),

	'profile'	=>	array('Profile','index','id'),
	'group'		=>	array('Group','index','id'),
	'wall@'		=>	array(
						array('/^\/(g|u)\/(\d+)\/(\d+)/','wall','index','type,wid,page'),
						array('/^\/(g|u)\/(\d+)/','wall','index','type,wid'),
					),

	'login'		=>	array('Public','login'),
	'logout'	=>	array('Public','logout'),
	'ERROR'		=>	array('Public','error'),

)

?>