<?php

import('Think.Template.TagLib');
class TagLibHtml extends TagLib
{
    public function _link($attr)
    {
        $tag        = $this->parseXmlAttr($attr,'link');
        $file       = $tag['href'];
        $type       = isset($tag['type'])?
                    strtolower($tag['type']):
                    strtolower(substr(strrchr($file, '.'),1));
        if($type=='js') {
            $parseStr = '<script type="text/javascript" src="'.$file.'"></script>';
        }elseif($type=='css') {
            $parseStr = '<link rel="stylesheet" type="text/css" href="'.$file.'"/>';
        }elseif($type=='ico') {
			$parseStr = '<link rel="shortcut icon" type="image/vnd.microsoft.icon" href="'.$file.'"/>';
		}
        return $parseStr;
    }

    public function _import($attr)
    {
        $tag        = $this->parseXmlAttr($attr,'import');
        $file       = $tag['file'];
        $basepath   = !empty($tag['basepath'])?$tag['basepath']:WEB_PUBLIC_URL;
        $type       = !empty($tag['type'])?  strtolower($tag['type']):'js';
        if($type=='js') {
            $parseStr = "<script type='text/javascript' src='".$basepath.'/'.str_replace(array('.','#'), array('/','.'),$file).'.js'."'></script> ";
        }elseif($type=='css') {
            $parseStr = "<link rel='stylesheet' type='text/css' href='".$basepath.'/'.str_replace(array('.','#'), array('/','.'),$file).'.css'."' />";
        }
        return $parseStr;
    }
}

?>