<?php

class PublicAction extends BaseAction
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

				$email   = isset($_POST['email']) ? $_POST['email'] : '';
				$password   = isset($_POST['pass']) ? $secure_code.$_POST['pass'] : '';
				$autosignin = isset($_POST['persistent']) ? true : false;

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
						
						//get friends
						$userFriend = getFriend($user['id']);
		
						foreach($userFriend as &$key)
						{
							$key = current(array_diff($key,array($user['id'])));
						}
						
						Session::set('userFriend',$userFriend);
						
						$userDao->setField('update_time',time(),'id='.$user['id']);

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
				$this->redirect('','login');
			}else{

			}	
		}else{
			$this->redirect('','login');
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
	
	public function report()
	{
		$type = $_GET['type'];
		$id = $_GET['id'];
		
		if(!empty($type) && !empty($id))
		{
			$dao = D('Report');
			$dao->type = $type;
			$dao->xid = $id;
			$dao->uid = $this->userId;
			$dao->status = 1;
			$dao->time = time();
			$dao->add();
		}
		
		redirect($_SERVER["HTTP_REFERER"]);
	}
	
	public function locale()
	{
		$id = $_GET['id'];
		
		if($id == 'en' || $id == 'zh')
		{
			$lang = ($id == 'en') ? 'en-US' : 'zh-CN';
			
			if(empty($this->userId))
			{
				Cookie::set('language',$lang,0);
			}
			else
			{
				$dao = D('User');
				$dao->find($this->userId);
				$dao->language = $lang;
				$dao->save();
			
				$user = $this->userInfo;
				$user['language'] = $lang;
				Session::set('userInfo',$user);
			}
		}
		
		redirect($_SERVER["HTTP_REFERER"]);
	}
}

?>