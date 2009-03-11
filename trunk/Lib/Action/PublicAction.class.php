<?php

class PublicAction extends Action
{
	public function _empty()
	{
		$this->redirect('','Index');
	}

	public function signup()
	{
		if(Session::get(C('USER_AUTH_KEY')))
		{
			$this->redirect('index','Home');
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

	public function signin()
	{
		if(Session::get(C('USER_AUTH_KEY')))
		{
			$this->redirect('index','Home');
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

					$this->redirect('index','Home');
				}else{
					$this->assign('error',L('signin_incorrect_password'));
				}
			}
			$this->display();
		}
	}

	public function signout()
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
		$resource = $_REQUEST['item'];
		$resource = str_replace('-','/',$resource);
		$split    = explode('.',$resource);
		$parts    = count($split);
		$valid    = array('js','css');

		if(!in_array($split[$parts-1],$valid))
		{
			echo 'Invalid Resource';
		}

		if($split[$parts-1]=='js')
		{
			header('Content-Type: text/javascript');
		}else{
			header('Content-Type: text/css');
		}
		
		$file  = ROOT_PATH.WEB_PUBLIC_URL.'/';
		$file .= $resource;
		$cache = CACHE_PATH.md5($file).'.'.$split[$parts-1];
		echo $file;

		if(is_file($cache)&&filemtime($cache)>filemtime($file))
		{
			$content = file_get_contents($cache);
			echo $content;
		}else{
			if(is_file($file))
			{
				$content = file_get_contents($file);
				file_put_contents($cache,$content);
				echo $content;
			}else{
				echo LParse(L('_RESOURCE_NOT_EXIST_'),$file);
			}
		}
	}
}

?>