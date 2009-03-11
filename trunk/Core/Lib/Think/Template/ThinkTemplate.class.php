<?php

class  ThinkTemplate extends Base
{
    protected $tagLib          =  array();

    protected $templateFile  =  '';

    public $tVar                 = array();

    static function  getInstance()
    {
        return get_instance_of(__CLASS__);
    }

    public function get($name) {
        if(isset($this->tVar[$name])) {
            return $this->tVar[$name];
        }else {
            return false;
        }
    }

    public function set($name,$value) {
        $this->tVar[$name]= $value;
    }

    public function load($templateFile,$charset,$templateVar,$varPrefix) {
        $this->tVar = $templateVar;
        $templateCacheFile  =  $this->loadTemplate($templateFile,$charset);
        extract($templateVar, empty($varPrefix)? EXTR_OVERWRITE : EXTR_PREFIX_ALL,$varPrefix);
        include $templateCacheFile;
    }

    public function loadTemplate ($tmplTemplateFile='',$charset='')
    {
        if(empty($tmplTemplateFile))    $tmplTemplateFile = C('TMPL_FILE_NAME');
        if(empty($charset)) $charset = C('OUTPUT_CHARSET');
        if(!is_file($tmplTemplateFile)){
            $tmplTemplateFile =  dirname(C('TMPL_FILE_NAME')).'/'.$tmplTemplateFile.C('TEMPLATE_SUFFIX');
            if(!is_file($tmplTemplateFile)){
                throw_exception(L('_TEMPLATE_NOT_EXIST_'));
            }
        }
        $this->templateFile    =  $tmplTemplateFile;

        $tmplCacheFile = CACHE_PATH.md5($tmplTemplateFile).C('CACHFILE_SUFFIX');
        $tmplContent = '';
        if (!$this->checkCache($tmplTemplateFile)) {
            $tmplContent = file_get_contents($tmplTemplateFile);
            $tmplContent = $this->compiler($tmplContent,$charset);
            if( false === file_put_contents($tmplCacheFile,trim($tmplContent))) {
                throw_exception(L('_CACHE_WRITE_ERROR_'));
            }
        }
        return $tmplCacheFile;
    }

    protected function compiler( $tmplContent,$charset='')
    {
        $tmplContent = $this->parse($tmplContent);

        if(ini_get('short_open_tag')) {
            $tmplContent = preg_replace('/(<\?(?!php|=|$))/i', '<?php echo \'\\1\'; ?>'."\n", $tmplContent );
        }
        if(empty($charset))  $charset = C('OUTPUT_CHARSET');
        if(C('TEMPLATE_CHARSET') != $charset) {
			if (preg_match('/<meta.*?charset=.*?>/i', $tmplContent, $regs)) {
				$meta = str_ireplace('charset='.C('TEMPLATE_CHARSET'), 'charset='.$charset, $regs[0]);
				$tmplContent = str_ireplace($regs[0], $meta, $tmplContent);
			}
        }
        $tmplContent =  preg_replace('/<\/form(\s*)>/is','<?php if(C("TOKEN_ON")):?><input type="hidden" name="<?php echo C("TOKEN_NAME");?>" value="<?php echo Session::get(C("TOKEN_NAME")); ?>"/><?php endif;?></form>',$tmplContent);

        $tmplContent = preg_replace('/<!--###literal(\d)###-->/eis',"\$this->restoreLiteral('\\1')",$tmplContent);

        return $tmplContent;
    }

    protected function checkCache($tmplTemplateFile)
    {
        $tmplCacheFile = CACHE_PATH.md5($tmplTemplateFile).C('CACHFILE_SUFFIX');
        if(!is_file($tmplCacheFile)){
            return false;
        }elseif (!C('TMPL_CACHE_ON')){
            return false;
        }elseif (filemtime($tmplTemplateFile) > filemtime($tmplCacheFile)) {
            return false;
        }elseif (C('TMPL_CACHE_TIME') != -1 && time() > filemtime($tmplCacheFile)+C('TMPL_CACHE_TIME')) {
            return false;
        }
        return true;
    }

    protected function cleanCache($filename)
    {
        if(is_file($filename)){
            unlink($filename);
        }
        return;
    }

    protected function cleanDir($cacheDir=CACHE_PATH)
    {
        if ( $dir = opendir( $cacheDir ) )
        {
            while ( $file = readdir( $dir ) )
            {
                $check = is_dir( $file );
                if ( !$check )
                    unlink( $cacheDir . $file );
            }
            closedir( $dir );
            return true;
        }
    }

    public function parse($content)
    {
        $content = preg_replace('/'.C('TAGLIB_BEGIN').'literal'.C('TAGLIB_END').'(.*?)'.C('TAGLIB_BEGIN').'\/literal'.C('TAGLIB_END').'/eis',"\$this->parseLiteral('\\1')",$content);
        $this->getIncludeTagLib($content);
        if(!empty($this->tagLib)) {
            foreach($this->tagLib as $tagLibName=>$tagLibClass) {
                if(empty($tagLibClass)) {
                    import('Think.Template.TagLib.TagLib'.ucwords(strtolower($tagLibName)));
                }else {
                    import($tagLibClass);
                }
                $this->parseTagLib($tagLibName,$content);
            }
        }
        import('Think.Template.TagLib.TagLibCx');
        $this->parseTagLib('cx',$content,true);

        $content = preg_replace('/('.C('TMPL_L_DELIM').')(\S.+?)('.C('TMPL_R_DELIM').')/eis',"\$this->parseTag('\\2')",$content);

        return $content;
    }

    function parseLiteral($content) {
        if(trim($content)=='') {
            return '';
        }
        $content = stripslashes($content);
        static $_literal = array();
        $i  =   count($_literal);
        $_literal[$i] = $content;
        $parseStr   =   "<!--###literal{$i}###-->";
        $_SESSION["literal{$i}"]    =   $content;
        return $parseStr;
    }

    function restoreLiteral($tag) {
        $parseStr   =   $_SESSION['literal'.$tag];
        unset($_SESSION['literal'.$tag]);
        return $parseStr;
    }

    public function getIncludeTagLib(& $content)
    {
        $find = preg_match('/'.C('TAGLIB_BEGIN').'taglib\s(.+?)(\s*?)\/'.C('TAGLIB_END').'\W/is',$content,$matches);
        if($find) {
            $content = str_replace($matches[0],'',$content);
            $tagLibs = $matches[1];
            $xml =  '<tpl><tag '.$tagLibs.' /></tpl>';
            $xml = simplexml_load_string($xml);
            if(!$xml) {
                throw_exception(L('_XML_TAG_ERROR_'));
            }
            $xml = (array)($xml->tag->attributes());
            $array = array_change_key_case($xml['@attributes']);
            $tagLibName =  explode(',',$array['name']);
            $tagLibClass  =  isset($array['class'])?explode(',',$array['class']):array_fill(0,count($tagLibName),'');
            $tagLibList  = array_combine($tagLibName,$tagLibClass);
            $this->tagLib = $tagLibList;
        }
        return;
    }

    public function parseTagLib($tagLib,&$content,$hide=false)
    {
        $tLib =  get_instance_of('TagLib'.ucwords(strtolower($tagLib)));
        if($tLib->valid()) {
            $tagList =  $tLib->getTagList();
            foreach($tagList as $tag) {
                if( !$hide) {
                    $startTag = $tagLib.':'.$tag['name'];
                }else {
                    $startTag = $tag['name'];
                }
                if($tag['nested'] && C('TAG_NESTED_LEVEL')>1) {
                    $level   =   C('TAG_NESTED_LEVEL');
                }else{
                    $level   =   1;
                }
                $endTag = $startTag;
                if(false !== stripos($content,C('TAGLIB_BEGIN').$startTag)) {
                    if(empty($tag['attribute'])){
                        if($tag['content'] !='empty'){
                            for($i=0;$i<$level;$i++) {
                                $content = preg_replace('/'.C('TAGLIB_BEGIN').$startTag.'(\s*?)'.C('TAGLIB_END').'(.*?)'.C('TAGLIB_BEGIN').'\/'.$endTag.'(\s*?)'.C('TAGLIB_END').'/eis',"\$this->parseXmlTag('".$tagLib."','".$tag['name']."','\\1','\\2')",$content);
                            }
                        }else{
                            $content = preg_replace('/'.C('TAGLIB_BEGIN').$startTag.'(\s*?)\/(\s*?)'.C('TAGLIB_END').'/eis',"\$this->parseXmlTag('".$tagLib."','".$tag['name']."','\\1','')",$content);
                        }
                    }elseif($tag['content'] !='empty') {
                        for($i=0;$i<$level;$i++) {
                            $content = preg_replace('/'.C('TAGLIB_BEGIN').$startTag.'\s(.*?)'.C('TAGLIB_END').'(.+?)'.C('TAGLIB_BEGIN').'\/'.$endTag.'(\s*?)'.C('TAGLIB_END').'/eis',"\$this->parseXmlTag('".$tagLib."','".$tag['name']."','\\1','\\2')",$content);
                        }
                    }else {
                        $content = preg_replace('/'.C('TAGLIB_BEGIN').$startTag.'\s(.*?)\/(\s*?)'.C('TAGLIB_END').'/eis',"\$this->parseXmlTag('".$tagLib."','".$tag['name']."','\\1','')",$content);
                    }
                }
            }
        }
    }

    public function parseXmlTag($tagLib,$tag,$attr,$content)
    {
        //if (MAGIC_QUOTES_GPC) {
            $attr = stripslashes($attr);
            $content = stripslashes($content);
        //}
        if(ini_get('magic_quotes_sybase')) {
            $attr =  str_replace('\"','\'',$attr);
        }
        $tLib =  get_instance_of('TagLib'.ucwords(strtolower($tagLib)));
        if($tLib->valid()) {
            $parse = '_'.$tag;
            $content = trim($content);
            return $tLib->$parse($attr,$content);
        }
    }

    public function parseTag($tagStr){
        //if (MAGIC_QUOTES_GPC)
            $tagStr = stripslashes($tagStr);
        if(preg_match('/^[\s|\d]/is',$tagStr)){
            return C('TMPL_L_DELIM') . $tagStr .C('TMPL_R_DELIM');
        }
        $flag =  substr($tagStr,0,1);
        $name   = substr($tagStr,1);
        if('$' == $flag){
            return $this->parseVar($name);
        }elseif(':' == $flag){
            return  '<?php echo '.$name.';?>';
        }elseif('~' == $flag){
            return  '<?php '.$name.';?>';
        }elseif('&' == $flag){
            return '<?php echo C("'.$name.'");?>';
        }elseif('%' == $flag){
            return '<?php echo L("'.$name.'");?>';
		}elseif('@' == $flag){
            if(strpos($name,'.')) {
                $array   =  explode('.',$name);
	    		return '<?php echo $_SESSION["'.$array[0].'"]["'.$array[1].'"];?>';
            }else{
    			return '<?php echo $_SESSION["'.$name.'"];?>';
            }
		}elseif('#' == $flag){
            if(strpos($name,'.')) {
                $array   =  explode('.',$name);
	    		return '<?php echo $_COOKIE["'.$array[0].'"]["'.$array[1].'"];?>';
            }else{
    			return '<?php echo $_COOKIE["'.$name.'"];?>';
            }
		}elseif('.' == $flag){
            return '<?php echo $_GET["'.$name.'"];?>';
        }elseif('^' == $flag){
            return '<?php echo $_POST["'.$name.'"];?>';
        }elseif('*' == $flag){
            return '<?php echo constant("'.$name.'");?>';
        }

        $tagStr = trim($tagStr);
        if(substr($tagStr,0,2)=='//' || (substr($tagStr,0,2)=='/*' && substr($tagStr,-2)=='*/')){
            return '';
        }
        $varArray = explode(':',$tagStr);
        $tag = trim(array_shift($varArray));

        $args = explode('|',$varArray[0],2);
        switch(strtoupper($tag)){
            case 'INCLUDE':
                $parseStr = $this->parseInclude(trim($args[0]));
                break;
            default:
                $parseStr = C('TMPL_L_DELIM') . $tagStr .C('TMPL_R_DELIM');
                break;
        }
        return $parseStr;
    }

    public function parseVar($varStr){
        $varStr = trim($varStr);
        static $_varParseList = array();
        if(isset($_varParseList[$varStr])) return $_varParseList[$varStr];
        $parseStr ='';
        $varExists = true;
        if(!empty($varStr)){
            $varArray = explode('|',$varStr);
            $var = array_shift($varArray);
            if(preg_match('/->/is',$var)){
                return '';
            }
            if('Think.' == substr($var,0,6)){
                $name = $this->parseThinkVar($var);
            }elseif( false !== strpos($var,'.')) {
                $vars = explode('.',$var);
                switch(C('TMPL_VAR_IDENTIFY')) {
                    case 'array': 
                        $name = '$'.$vars[0].'["'.$vars[1].'"]';
                        break;
                    case 'obj':  
                        $name = '$'.$vars[0].'->'.$vars[1];
                        break;
                    default: 
                        $name = 'is_array($'.$vars[0].')?$'.$vars[0].'["'.$vars[1].'"]:$'.$vars[0].'->'.$vars[1];
                }
                $var  = $vars[0];
            }
            elseif(false !==strpos($var,':')){
                $vars = explode(':',$var);
                $var  =  str_replace(':','->',$var);
                $name = "$".$var;
                $var  = $vars[0];
            }
            elseif(false !== strpos($var,'[')) {
                $name = "$".$var;
                preg_match('/(.+?)\[(.+?)\]/is',$var,$match);
                $var = $match[1];
            }
            else {
                $name = "$$var";
            }
            if(count($varArray)>0) {
                $name = $this->parseVarFunction($name,$varArray);
            }
            $parseStr = '<?php echo ('.$name.'); ?>';
        }
        $_varParseList[$varStr] = $parseStr;
        return $parseStr;
    }

    public function parseVarFunction($name,$varArray){
        $length = count($varArray);
        $template_deny_funs = explode(',',C('TMPL_DENY_FUNC_LIST'));
        for($i=0;$i<$length ;$i++ ){
            $args = explode('=',$varArray[$i]);
            $args[0] = trim($args[0]);
            switch(strtolower($args[0])) {
            case 'default': 
                $name   = '('.$name.')?('.$name.'):'.$args[1];
                break;
            default:  
                if(!in_array($args[0],$template_deny_funs)){
                    if(isset($args[1])){
                        if(strstr($args[1],'###')){
                            $args[1] = str_replace('###',$name,$args[1]);
                            $name = "$args[0]($args[1])";
                        }else{
                            $name = "$args[0]($name,$args[1])";
                        }
                    }else if(!empty($args[0])){
                        $name = "$args[0]($name)";
                    }
                }
            }
        }
        return $name;
    }
	
    public function parseThinkVar($varStr){
        $vars = explode('.',$varStr);
        $vars[1] = strtoupper(trim($vars[1]));
        $parseStr = '';
        if(count($vars)>=3){
            $vars[2] = trim($vars[2]);
            switch($vars[1]){
                case 'SERVER':
                    $parseStr = '$_SERVER[\''.strtoupper($vars[2]).'\']';break;
                case 'GET':
                    $parseStr = '$_GET[\''.$vars[2].'\']';break;
                case 'POST':
                    $parseStr = '$_POST[\''.$vars[2].'\']';break;
                case 'COOKIE':
                    if(isset($vars[3])) {
                        $parseStr = '$_COOKIE[\''.$vars[2].'\'][\''.$vars[3].'\']';
                    }else{
                        $parseStr = '$_COOKIE[\''.$vars[2].'\']';
                    }break;
                case 'SESSION':
                    if(isset($vars[3])) {
                        $parseStr = '$_SESSION[\''.$vars[2].'\'][\''.$vars[3].'\']';
                    }else{
                        $parseStr = '$_SESSION[\''.$vars[2].'\']';
                    }
                    break;
                case 'ENV':
                    $parseStr = '$_ENV[\''.$vars[2].'\']';break;
                case 'REQUEST':
                    $parseStr = '$_REQUEST[\''.$vars[2].'\']';break;
                case 'CONST':
                    $parseStr = strtoupper($vars[2]);break;
                case 'LANG':
                    $parseStr = 'L("'.$vars[2].'")';break;
				case 'CONFIG':
                    if(isset($vars[3])) {
                        $vars[2] .= '.'.$vars[3];
                    }
                    $parseStr = 'C("'.$vars[2].'")';break;
                default:break;
            }
        }else if(count($vars)==2){
            switch($vars[1]){
                case 'NOW':
                    $parseStr = "date('Y-m-d g:i a',time())";
                    break;
                case 'VERSION':
                    $parseStr = 'THINK_VERSION';
                    break;
                case 'TEMPLATE':
                    $parseStr = 'C("TMPL_FILE_NAME")';
                    break;
                case 'LDELIM':
                    $parseStr = 'C("TMPL_L_DELIM")';
                    break;
                case 'RDELIM':
                    $parseStr = 'C("TMPL_R_DELIM")';
                    break;
                default:
                    if(defined($vars[1]))
                        $parseStr = $vars[1];
            }
        }
        return $parseStr;
    }

    public function parseInclude($tmplPublicName){
        if(substr($tmplPublicName,0,1)=='$'){
            $tmplPublicName = $this->get(substr($tmplPublicName,1));
        }
        if(is_file($tmplPublicName)) {
            $parseStr = file_get_contents($tmplPublicName);
        }else {
            $tmplPublicName = trim($tmplPublicName);
            if(strpos($tmplPublicName,'@')){
                $tmplTemplateFile   =   dirname(dirname(dirname($this->templateFile))).'/'.str_replace(array('@',':'),'/',$tmplPublicName);
            }elseif(strpos($tmplPublicName,':')){
                $tmplTemplateFile   =   dirname(dirname($this->templateFile)).'/'.str_replace(':','/',$tmplPublicName);
            }else{
                $tmplTemplateFile = dirname($this->templateFile).'/'.$tmplPublicName;
            }
            $tmplTemplateFile .=  C('TEMPLATE_SUFFIX');
            $parseStr = file_get_contents($tmplTemplateFile);
        }
        return $this->parse($parseStr);
    }

}

?>