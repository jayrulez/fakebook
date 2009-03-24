<?php

class Page extends Base
{
    protected $firstRow ;

    protected $listRows ;

    protected $parameter  ;

    protected $totalPages  ;

    protected $totalRows  ;

    protected $nowPage    ;

    protected $coolPages   ;

    protected $rollPage   ;

    protected $config   =   array('header'=>' posts','prev'=>L('_PREVIOUS_'),'next'=>L('_NEXT_'),'first'=>L('_FIRST_'),'last'=>L('_LAST_'));

    public function __construct($totalRows,$listRows='',$parameter='')
    {
        $this->totalRows = $totalRows;
        $this->parameter = $parameter;
        $this->rollPage = C('PAGE_NUMBERS');
        $this->listRows = !empty($listRows)?$listRows:C('LIST_NUMBERS');
        $this->totalPages = ceil($this->totalRows/$this->listRows);    
        $this->coolPages  = ceil($this->totalPages/$this->rollPage);
        $this->nowPage  = !empty($_GET[C('VAR_PAGE')])&&($_GET[C('VAR_PAGE')] >0)?$_GET[C('VAR_PAGE')]:1;

        if(!empty($this->totalPages) && $this->nowPage>$this->totalPages) {
            $this->nowPage = $this->totalPages;
        }
        $this->firstRow = $this->listRows*($this->nowPage-1);
    }

    public function setConfig($name,$value) {
        if(isset($this->config[$name])) {
            $this->config[$name]    =   $value;
        }
    }

    public function show($isArray=false){

        if(0 == $this->totalRows) return;
        $nowCoolPage      = ceil($this->nowPage/$this->rollPage);
        $url  =  $_SERVER['REQUEST_URI'].(strpos($_SERVER['REQUEST_URI'],'?')?'':"?").$this->parameter;

        $upRow   = $this->nowPage-1;
        $downRow = $this->nowPage+1;
		$preRow =  $this->nowPage-$this->rollPage;
		$nextRow = $this->nowPage+$this->rollPage;
		$theEndRow = $this->totalPages;

		$replaceVAR = false;

		if (preg_match('/&'.C('VAR_PAGE').'=\\d+/', $url)) {
			$replaceVAR = true;

			$upUrl = preg_replace('/&'.C('VAR_PAGE').'=\\d+/',"&".C('VAR_PAGE')."=".$upRow,$url);
			$downUrl = preg_replace('/&'.C('VAR_PAGE').'=\\d+/',"&".C('VAR_PAGE')."=".$downRow,$url);
			$preUrl = preg_replace('/&'.C('VAR_PAGE').'=\\d+/',"&".C('VAR_PAGE')."=".$preRow,$url);
			$nextUrl = preg_replace('/&'.C('VAR_PAGE').'=\\d+/',"&".C('VAR_PAGE')."=".$nextRow,$url);
			$theFirstUrl = preg_replace('/&'.C('VAR_PAGE').'=\\d+/',"&".C('VAR_PAGE')."=1",$url);
			$theEndUrl = preg_replace('/&'.C('VAR_PAGE').'=\\d+/',"&".C('VAR_PAGE')."=".$theEndRow,$url);
		} else {
			$upUrl = $url."&".C('VAR_PAGE')."=".$upRow;
			$downUrl = $url."&".C('VAR_PAGE')."=".$downRow;
			$preUrl = $url."&".C('VAR_PAGE')."=".$preRow;
			$nextUrl = $url."&".C('VAR_PAGE')."=".$nextRow;
			$theFirstUrl = $url."&".C('VAR_PAGE')."=1";
			$theEndUrl = $url."&".C('VAR_PAGE')."=".$theEndRow;
		}

        if ($upRow>0){
            $upPage="[<a href='".$upUrl."'>".$this->config['prev']."</a>]";
        }else{
            $upPage="";
        }

        if ($downRow <= $this->totalPages){
            $downPage="[<a href='".$downUrl."'>".$this->config['next']."</a>]";
        }else{
            $downPage="";
        }
        if($nowCoolPage == 1){
            $theFirst = "";
            $prePage = "";
        }else{
            $prePage = "[<a href='".$preUrl."' >Prev ".$this->rollPage." page</a>]";
            $theFirst = "[<a href='".$theFirstUrl."' >".$this->config['first']."</a>]";
        }
        if($nowCoolPage == $this->coolPages){
            $nextPage = "";
            $theEnd="";
        }else{
            $nextPage = "[<a href='".$nextUrl."' >Next ".$this->rollPage." page</a>]";
            $theEnd = "[<a href='".$theEndUrl."' >".$this->config['last']."</a>]";
        }
        $linkPage = "";
        for($i=1;$i<=$this->rollPage;$i++){
            $page=($nowCoolPage-1)*$this->rollPage+$i;
            if($page!=$this->nowPage){
                if($page<=$this->totalPages){
					if($replaceVAR)
						$linkPage .= "&nbsp;<a href='".preg_replace('/&'.C('VAR_PAGE').'=\\d+/',"&".C('VAR_PAGE')."=".$page,$url)."'>&nbsp;".$page."&nbsp;</a>";
					else
						$linkPage .= "&nbsp;<a href='".$url."&".C('VAR_PAGE')."=$page'>&nbsp;".$page."&nbsp;</a>";
                }else{
                    break;
                }
            }else{
                if($this->totalPages != 1){
                    $linkPage .= " [".$page."]";
                }
            }
        }
        $pageStr = 'Total '.$this->totalRows.' '.$this->config['header'].'/'.$this->totalPages.' pages '.$upPage.' '.$downPage.' '.$theFirst.' '.$prePage.' '.$linkPage.' '.$nextPage.' '.$theEnd;
        if($isArray) {
            $pageArray['totalRows'] =   $this->totalRows;
            $pageArray['upPage']    =   $url.'&'.C('VAR_PAGE')."=$upRow";
            $pageArray['downPage']  =   $url.'&'.C('VAR_PAGE')."=$downRow";
            $pageArray['totalPages']=   $this->totalPages;
            $pageArray['firstPage'] =   $url.'&'.C('VAR_PAGE')."=1";
            $pageArray['endPage']   =   $url.'&'.C('VAR_PAGE')."=$theEndRow";
            $pageArray['nextPages'] =   $url.'&'.C('VAR_PAGE')."=$nextRow";
            $pageArray['prePages']  =   $url.'&'.C('VAR_PAGE')."=$preRow";
            $pageArray['linkPages'] =   $linkPage;
            $pageArray['nowPage'] =   $this->nowPage;
            return $pageArray;
        }
        return $pageStr;
    }
}

?>