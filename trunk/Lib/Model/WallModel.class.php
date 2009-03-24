<?php

class WallModel extends Model
{

	var $_link	=	array(
		'user'=>array(
			'mapping_type'=>BELONGS_TO,
			'class_name'=>'User',
			'foreign_key'=>'fromid',
			'mapping_name'=>'User',
		),
	);

}

?>