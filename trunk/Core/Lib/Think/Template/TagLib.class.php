<?php

class TagLib extends Base
{
    protected $xml = '';

    protected $tagLib ='';

    protected $tagList = array();

    protected $parse = array();

    protected $valid = false;

    protected $tpl;

    protected $comparison = array(' nheq '=>' !== ',' heq '=>' === ',' neq '=>' != ',' eq '=>' == ',' egt '=>' >= ',' gt '=>' > ',' elt '=>' <= ',' lt '=>' < ');

    public function __construct($tagLib='',$filename='')
    {
        if(empty($tagLib)) {
            $tagLib =   strtolower(substr(get_class($this),6));
        }
        $this->tagLib  = $tagLib;
        $this->tpl       = ThinkTemplate::getInstance();
        if(!empty($filename)) {
            $this->xml = $filename;
        }else {
            $this->xml = dirname(__FILE__).'/Tags/'.$tagLib.'.xml';
        }
        if(method_exists($this,'_initialize')) {
            $this->_initialize();
        }
        $this->load();
    }

    public function load() {
        $array = (array)(simplexml_load_file($this->xml));
        if($array !== false) {
            $this->parse = $array;
            $this->valid = true;
        }else{
            $this->valid = false;
        }
    }

    public function valid()
    {
        return $this->valid;
    }

    public function getTagLib()
    {
        return $this->tagLib;
    }

    public function getTagList()
    {
        if(empty($this->tagList)) {
            $tags = $this->parse['tag'];
            $list = array();
            if(is_object($tags)) {
                $list[] =  array(
                    'name'=>$tags->name,
                    'content'=>$tags->bodycontent,
                    'nested'=>isset($tags->nested)?$tags->nested:0,
                    'attribute'=>isset($tags->attribute)?$tags->attribute:'',
                    );
                if(isset($tags->alias)) {
                    $alias  =   explode(',',$tag->alias);
                    foreach ($alias as $tag){
                        $list[] =  array(
                            'name'=>$tag,
                            'content'=>$tags->bodycontent,
                            'nested'=>isset($tags->nested)?$tags->nested:0,
                            'attribute'=>isset($tags->attribute)?$tags->attribute:'',
                            );
                    }
                }
            }else{
                foreach($tags as $tag) {
                    $tag = (array)$tag;
                    $list[] =  array(
                        'name'=>$tag['name'],
                        'content'=>$tag['bodycontent'],
                        'nested'=>isset($tag['nested'])?$tag['nested']:0,
                        'attribute'=>isset($tag['attribute'])?$tag['attribute']:'',
                        );
                    if(isset($tag['alias'])) {
                        $alias  =   explode(',',$tag['alias']);
                        foreach ($alias as $tag1){
                            $list[] =  array(
                                'name'=>$tag1,
                                'content'=>$tag['bodycontent'],
                                'nested'=>isset($tag['nested'])?$tag['nested']:0,
                                'attribute'=>isset($tag['attribute'])?$tag['attribute']:'',
                                );
                        }
                    }
                }
            }
            $this->tagList = $list;
        }
        return $this->tagList;
    }

    public function getTagAttrList($tagName)
    {
        static $_tagCache   = array();
        $_tagCacheId        =   md5($this->tagLib.$tagName);
        if(isset($_tagCache[$_tagCacheId])) {
            return $_tagCache[$_tagCacheId];
        }
        $list = array();
        $tags = $this->parse['tag'];
        foreach($tags as $tag) {
            $tag = (array)$tag;
            if( strtolower($tag['name']) == strtolower($tagName)) {
                if(isset($tag['attribute'])) {
                    if(is_object($tag['attribute'])) {
                        $attr = $tag['attribute'];
                        $list[] = array(
                            'name'=>$attr->name,
                            'required'=>$attr->required
                            );
                    }else{
                        foreach($tag['attribute'] as $attr) {
                            $attr = (array)$attr;
                            $list[] = array(
                                'name'=>$attr['name'],
                                'required'=>$attr['required']
                                );
                        }
                    }
                }
            }
        }
        $_tagCache[$_tagCacheId]    =   $list;
        return $list;
    }

    public function parseXmlAttr($attr,$tag)
    {
        $attr = str_replace("<","&lt;", $attr);
        $attr = str_replace(">","&gt;", $attr);
        $xml =  '<tpl><tag '.$attr.' /></tpl>';
        $xml = simplexml_load_string($xml);
        if(!$xml) {
            throw_exception(L('_XML_TAG_ERROR_').' : '.$attr);
        }
        $xml = (array)($xml->tag->attributes());
        $array = array_change_key_case($xml['@attributes']);
        $attrs  = $this->getTagAttrList($tag);
        foreach($attrs as $val) {
            if( !isset($array[strtolower($val['name'])])) {
                $array[strtolower($val['name'])] = '';
            }
        }
        return $array;
    }
	
    public function parseCondition($condition) {
        $condition = str_ireplace(array_keys($this->comparison),array_values($this->comparison),$condition);
        $condition = preg_replace('/\$(\w+):(\w+)\s/is','$\\1->\\2 ',$condition);
        $condition = preg_replace('/\$(\w+)\.(\w+)\s/is','(is_array($\\1)?$\\1["\\2"]:$\\1->\\2) ',$condition);
        return $condition;
    }

    public function dateFormat($var,$format,$true=false)
    {
        if($true) {
            $tmplContent = 'date( "'.$format.'", intval('.$var.') )';
        }else {
            $tmplContent = 'date( "'.$format.'", strtotime('.$var.') )';
        }
        return $tmplContent;
    }

    public function stringFormat($var,$format)
    {
        $tmplContent = 'sprintf("'.$format.'", '.$var.')';
        return $tmplContent;
    }

    public function numericFormat($var,$format)
    {
        $tmplContent = 'number_format("'.$var.'")';
        return $tmplContent;
    }

    public function autoBuildVar($name) {
        if('Think.' == substr($name,0,6)){
            return $this->parseThinkVar($name);
        }elseif(strpos($name,'.')) {
            $vars = explode('.',$name);
            $name = 'is_array($'.$vars[0].')?$'.$vars[0].'["'.$vars[1].'"]:$'.$vars[0].'->'.$vars[1];
        }elseif(strpos($name,':')){
            $name   =   '$'.str_replace(':','->',$name);
        }elseif(!defined($name)) {
            $name = '$'.$name;
        }
        return $name;
    }

    public function parseThinkVar($varStr){
        $vars = explode('.',$varStr);
        $vars[1] = strtoupper(trim($vars[1]));
        $parseStr = '';

        if(count($vars)==3){
            $vars[2] = trim($vars[2]);
            switch($vars[1]){
                case 'SERVER':    $parseStr = '$_SERVER[\''.$vars[2].'\']';break;
                case 'GET':         $parseStr = '$_GET[\''.$vars[2].'\']';break;
                case 'POST':       $parseStr = '$_POST[\''.$vars[2].'\']';break;
                case 'COOKIE':    $parseStr = '$_COOKIE[\''.$vars[2].'\']';break;
                case 'SESSION':   $parseStr = '$_SESSION[\''.$vars[2].'\']';break;
                case 'ENV':         $parseStr = '$_ENV[\''.$vars[2].'\']';break;
                case 'REQUEST':  $parseStr = '$_REQUEST[\''.$vars[2].'\']';break;
                case 'CONST':     $parseStr = strtoupper($vars[2]);break;
                case 'LANG':       $parseStr = 'L("'.$vars[2].'")';break;
                case 'CONFIG':    $parseStr = 'C("'.$vars[2].'")';break;
                default:break;
            }
        }else if(count($vars)==2){
            switch($vars[1]){
                case 'NOW':       $parseStr = "date('Y-m-d g:i a',time())";break;
                case 'VERSION':  $parseStr = 'THINK_VERSION';break;
                case 'TEMPLATE':$parseStr = 'C("TMPL_FILE_NAME")';break;
                case 'LDELIM':    $parseStr = 'C("TMPL_L_DELIM")';break;
                case 'RDELIM':    $parseStr = 'C("TMPL_R_DELIM")';break;
            }
            if(defined($vars[1])){ $parseStr = strtoupper($vars[1]);}
        }
        return $parseStr;
    }

}

?>