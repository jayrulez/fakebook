<?php

class UploadFile extends Base
{
    public $maxSize = -1;

    public $supportMulti = true;

    public $allowExts = array();

    public $allowTypes = array();

    public $thumb   =  false;

    public $thumbPath = '';

    public $thumbMaxWidth;

    public $thumbMaxHeight;

    public $thumbPrefix   =  '';
    public $thumbSuffix   =  '_thumb';

    public $zipImages = false;

    public $autoSub   =  false;

    public $subType   = 'hash';

    public $dateFormat = 'Ymd';

    public $savePath = '';
    public $autoCheck = true;

    public $uploadReplace = false;

    public $saveRule = '';

    public $hashType = 'md5_file';

    private $error = '';

    private $uploadFileInfo ;

    public function __construct($maxSize='',$allowExts='',$allowTypes='',$savePath=UPLOAD_PATH,$saveRule='')
    {
        if(!empty($maxSize) && is_numeric($maxSize)) {
            $this->maxSize = $maxSize;
        }
        if(!empty($allowExts)) {
            if(is_array($allowExts)) {
                $this->allowExts = array_map('strtolower',$allowExts);
            }else {
                $this->allowExts = explode(',',strtolower($allowExts));
            }
        }
        if(!empty($allowTypes)) {
            if(is_array($allowTypes)) {
                $this->allowTypes = array_map('strtolower',$allowTypes);
            }else {
                $this->allowTypes = explode(',',strtolower($allowTypes));
            }
        }
        if(!empty($saveRule)) {
            $this->saveRule = $saveRule;
        }else{
            $this->saveRule =   C('UPLOAD_FILE_RULE');
        }
        $this->savePath = $savePath;
    }

    private function save($file)
    {
        $filename = $file['savepath'].$file['savename'];
        if(!$this->uploadReplace && is_file($filename)) {

            $this->error    =   LParse(L('_FILE_REPLACE_ERROR_'),array($filename));
            return false;
        }
        if(!move_uploaded_file($file['tmp_name'], auto_charset($filename,'utf-8','gbk'))) {
            $this->error = L('_FILE_MOVE_ERROR_');
            return false;
        }
        if($this->thumb) {

            import("ORG.Util.Image");
            $image =  Image::getImageInfo($filename);
            if(false !== $image) {

                $thumbWidth = explode(',',$this->thumbMaxWidth);
                $thumbHeight   =  explode(',',$this->thumbMaxHeight);
                $thumbPrefix		=	explode(',',$this->thumbPrefix);
                $thumbSuffix = explode(',',$this->thumbSuffix);
                $thumbPath    =  $this->thumbPath?$this->thumbPath:$file['savepath'];
                for($i=0,$len=count($thumbWidth); $i<$len; $i++) {
                    $thumbname = $thumbPath.$thumbPrefix[$i].substr($file['savename'],0,strrpos($file['savename'], '.')).$thumbSuffix[$i].'.'.$file['extension'];
                    Image::thumb($filename,'',$thumbname,$thumbWidth[$i],$thumbHeight[$i],true);
                }
            }
        }
        if($this->zipImages) {
            // TODO Image compression package on-line decompression

        }
        return true;
    }

    public function upload($savePath ='')
    {
        if(empty($savepath)) {
            $savePath = $this->savePath;
        }

        if(!is_dir($savePath)) {
            if(is_dir(base64_decode($savePath))) {
                $savePath   =   base64_decode($savePath);
            }else{
                $this->error  =  '上传目录'.$savePath.'不存在';
                return false;
            }
        }else {
            if(!is_writeable($savePath)) {
                $this->error  =  '上传目录'.$savePath.'不可写';
                return false;
            }
        }
        $fileInfo = array();
        $isUpload   = false;

        $files   =   $this->dealFiles($_FILES);
        foreach($files as $key => $file) {
            if(!empty($file['name'])) {
                $file['key']          =  $key;
                $file['extension']  = $this->getExt($file['name']);
                $file['savepath']   = $savePath;
                $file['savename']   = $this->getSaveName($file);

                if($this->autoCheck) {
                    if(!$this->check($file))
                        return false;
                }

                if(!$this->save($file)) {
                    return false;
                }
                if(function_exists($this->hashType)) {
                    $fun =  $this->hashType;
                    $file['hash']   =  $fun(auto_charset($file['savepath'].$file['savename'],'utf-8','gbk'));
                }
                unset($file['tmp_name'],$file['error']);
                $fileInfo[] = $file;
                $isUpload   = true;
            }
        }
        if($isUpload) {
            $this->uploadFileInfo = $fileInfo;
            return true;
        }else {
            $this->error  =  '没有选择上传文件';
            return false;
        }
    }

    private function dealFiles($files) {
       $fileArray = array();
       foreach ($files as $file){
           if(is_array($file['name'])) {
               $keys = array_keys($file);
               $count    =   count($file['name']);
               for ($i=0; $i<$count; $i++) {
                   foreach ($keys as $key) {
                       $fileArray[$i][$key] = $file[$key][$i];
                   }
               }
           }else{
               $fileArray   =   $files;
           }
           break;
       }
       return $fileArray;
    }

    protected function error($errorNo)
    {
         switch($errorNo) {
            case 1:
                $this->error = '上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值';
                break;
            case 2:
                $this->error = '上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值';
                break;
            case 3:
                $this->error = '文件只有部分被上传';
                break;
            case 4:
                $this->error = '没有文件被上传';
                break;
            case 6:
                $this->error = '找不到临时文件夹';
                break;
            case 7:
                $this->error = '文件写入失败';
                break;
            default:
                $this->error = '未知上传错误！';
        }
        return ;
    }

    private function getSaveName($filename)
    {
        $rule = $this->saveRule;
        if(empty($rule)) {
            $saveName = $filename['name'];
        }else {
            if(function_exists($rule)) {
                $saveName = $rule().".".$filename['extension'];
            }else {
                $saveName = $rule.".".$filename['extension'];
            }
        }
        if($this->autoSub) {
            $saveName   =  $this->getSubName($filename).'/'.$saveName;
        }
        return $saveName;
    }

    private function getSubName($file)
    {
        switch($this->subType) {
            case 'date':
                $dir   =  date($this->dateFormat,time());
                break;
            case 'hash':
            default:
                $name = md5($file['savename']);
                $dir	=	$name{0};
                break;
        }
        if(!is_dir($file['savepath'].$dir)) {
            mkdir($file['savepath'].$dir);
        }
        return $dir;
    }

    private function check($file) {
        if($file['error']!== 0) {
            $this->error($file['error']);
            return false;
        }
        if(!$this->checkSize($file['size'])) {
            $this->error = '上传文件大小不符！';
            return false;
        }

        if(!$this->checkType($file['type'])) {
            $this->error = '上传文件MIME类型不允许！';
            return false;
        }

        if(!$this->checkExt($file['extension'])) {
            $this->error ='上传文件类型不允许';
            return false;
        }

        if(!$this->checkUpload($file['tmp_name'])) {
            $this->error = '非法上传文件！';
            return false;
        }
        return true;
    }

    private function checkType($type)
    {
        if(!empty($this->allowTypes)) {
            return in_array(strtolower($type),$this->allowTypes);
        }
        return true;
    }

    private function checkExt($ext)
    {
        if(!empty($this->allowExts)) {
            return in_array(strtolower($ext),$this->allowExts);
        }
        return true;
    }

    private function checkSize($size)
    {
        return !($size > $this->maxSize) || (-1 == $this->maxSize);
    }

    private function checkUpload($filename)
    {
        return is_uploaded_file($filename);
    }

    private function getExt($filename)
    {
        $pathinfo = pathinfo($filename);
        return $pathinfo['extension'];
    }

    public function getUploadFileInfo()
    {
        return $this->uploadFileInfo;
    }

    public function getErrorMsg()
    {
        return $this->error;
    }

}

?>