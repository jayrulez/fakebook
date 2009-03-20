<?php

class PublicAction extends Action
{
	public function error()
	{
		$this->display('error');
	}

	public function _empty()
	{
		$this->redirect('','ERROR');
	}
	
	public function signup()
	{
		if(Session::get(C('USER_AUTH_KEY')))
		{
			$this->redirect('','home');
		}else{
			if(isset($_POST['signup']))
			{
				$userDao = D('User');
				$user    = $userDao->create();

				if($userId = $userDao->add($user))
				{

				}else{
					$this->assign('error',$userDao->getError());
				}
			}
			$this->display();
		}
	}

	public function login()
	{
		if(Session::get(C('USER_AUTH_KEY')))
		{
			$this->redirect('','home');
		}else{
			if(isset($_POST['login']))
			{
				if(C('TOKEN_ON') && isset($_POST[C('TOKEN_NAME')]))
				{
					$secure_code = $_POST[C('TOKEN_NAME')];
				}else{
					$secure_code = '';
				}
				
				$email   = isset($_POST['email'])   ? $_POST['email'] : '';
				$password   = isset($_POST['pass'])   ? $secure_code.$_POST['pass'] : '';
				$autosignin = isset($_POST['persistent']) ? true               : false;
						
				import('ORG.Text.Validation');
				
				$isEmail = I('Validation')->check($email,'email');

				if(!$isEmail)
				{
					$this->assign('error',L('login_invalid_email'));
				}else{
					$map['email']   = $email;
					$userDao = D('User');
					$user    = $userDao->find($map,'*');
				
					if(!$user)
					{
						$this->assign('error',L('login_incorrect_email'));
					}else if($secure_code.$user['password'] == md5($password))
					{
						Session::set(C('USER_AUTH_KEY'),$user['id']);
						Session::set('userInfo',$user);

						$userDao->setField('signinTime',time(),'id='.$user['id']);

						if($autosignin)
						{
							Cookie::set('signinId',$email,C('COOKIE_EXPIRE'));
							Cookie::set('password',$password,C('COOKIE_EXPIRE'));
						}

						$this->redirect('','home');
					}else{
						$this->assign('error',L('signin_incorrect_password'));
					}
				}
			}
			$this->display();
		}
	}

	public function logout()
	{
		if(Session::get(C('USER_AUTH_KEY')))
		{
			if(Session::destroy())
			{
				$this->display();
			}else{

			}	
		}else{

		}
	}

	public function verify()
	{
		import('ORG.Util.Image');
		Image::buildImageVerify();
	}

	public function _static()
	{
		_static();
	}

	public function _jsLang()
	{
		_jsLang();
	}
}

?>