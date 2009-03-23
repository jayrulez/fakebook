<?php

class UserModel extends Model
{
    
/*	protected $_validate = array
	(
		array('account','require',L('account_required'),1),
		array('email','email',L('email_required'),2),
		array('password','require',L('password_required')),
		array('truename','require',L('truename_required')),
		array('account','',L('account_exists'),0,'unique'),
		array('email','',L('email_exists'),0,'unique','add'),
		array('verify','checkVerify',L('verify_invalid'),0,'callback'),
	);
*/

	protected $_auto     = array
	(
		array('password','md5','ADD','function'),
		array('status','1','ADD'),
		array('signupTime','time','ADD','function'),
		array('signinTime','time','ADD','function'),
		array('updateTime','time','ADD','function'),
		array('ip_address','get_client_ip','ADD','function'),
		array('user_agent','get_client_browser','ADD','function'),
	);

	public function checkVerify()
	{
		return md5($_POST['verify']) == $_SESSION['verify'];
	}

	public function birthday()
	{

	}
}

?>