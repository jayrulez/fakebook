<?php

class TemplateThink extends Base
{
    public function fetch($templateFile,$var,$charset,$varPrefix) {
        if(!$this->checkCache($templateFile)) {
            import('Think.Template.ThinkTemplate');
            $tpl = ThinkTemplate::getInstance();
            $tpl->load($templateFile,$charset,$var,$varPrefix);
        }else{
            extract($var, empty($varPrefix)? EXTR_OVERWRITE : EXTR_PREFIX_ALL,$varPrefix);
            include CACHE_PATH.md5($templateFile).C('CACHFILE_SUFFIX');
        }
    }

    protected function checkCache($tmplTemplateFile)
    {
        $tmplCacheFile = CACHE_PATH.md5($tmplTemplateFile).C('CACHFILE_SUFFIX');
        if(!is_file($tmplCacheFile)){
            return false;
        }
        elseif (!C('TMPL_CACHE_ON')){
            return false;
        }elseif (filemtime($tmplTemplateFile) > filemtime($tmplCacheFile)) {
            return false;
        } elseif (C('TMPL_CACHE_TIME') != -1 && time() > filemtime($tmplCacheFile)+C('TMPL_CACHE_TIME')) {
            return false;
        }
        return true;
    }
}
?>