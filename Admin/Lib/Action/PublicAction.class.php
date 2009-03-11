<?php

class PublicAction extends Action
{
	public function _empty()
	{
		$this->redirect('','Index');
	}

	public function verify()
	{
		import('ORG.Util.Image');
		Image::buildImageVerify();
	}
}

?>