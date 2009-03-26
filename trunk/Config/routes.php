<?php

return array
(
	'static'	=>	array('Public','_static','item'),
	'jsLang'	=>	array('Public','_jsLang','item'),

	'profile'	=>	array('Profile','index','id'),
	'wall@'		=>	array(
						array('/^\/(\d+)/','wall','index','wid,page'),
					),

	'login'		=>	array('Public','login'),
	'logout'	=>	array('Public','logout'),
	'ERROR'		=>	array('Public','error'),

)

?>