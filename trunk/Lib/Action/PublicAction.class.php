<?php

class PublicAction extends Action
{
	public function _empty()
	{
		$this->redirect('','index');
	}

	public function signup()
	{
		if(Session::get(C('USER_AUTH_KEY')))
		{
			$this->redirect('index','home');
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
			$this->redirect('index','home');
		}else{
			if(isset($_POST['signin']))
			{
				if(C('TOKEN_ON') && isset($_POST[C('TOKEN_NAME')]))
				{
					$secure_code = $_POST[C('TOKEN_NAME')];
				}else{
					$secure_code = '';
				}
				
				$signinId   = isset($_POST['signinId'])   ? $_POST['signinId'] : '';
				$password   = isset($_POST['password'])   ? $secure_code.$_POST['password'] : '';
				$autosignin = isset($_POST['autosignin']) ? true               : false;
						
				import('ORG.Text.Validation');
						
				$isEmail = I('Validation')->check($signinId,'email');
						
				if(!$isEmail)
				{
					$map['account'] = $signinId;
				}else{
					$map['email']   = $signinId;
				}

				$userDao = D('User');
				$user    = $userDao->find($map,'*');
				
				if(!$user)
				{
					$this->assign('error',L('signin_invalid_signinId'));
				}else if($secure_code.$user['password'] == md5($password))
				{
					Session::set(C('USER_AUTH_KEY'),$user['id']);
					Session::set('userInfo',$user);

					$userDao->setField('signinTime',time(),'id='.$user['id']);

					if($autosignin)
					{
						Cookie::set('signinId',$signinId,C('COOKIE_EXPIRE'));
						Cookie::set('password',$password,C('COOKIE_EXPIRE'));
					}

					$this->redirect('index','home');
				}else{
					$this->assign('error',L('signin_incorrect_password'));
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