<?php

class myException extends Exception
{
	private $type;

	private $extra;

	public function __construct($msg,$code=0,$extra)
	{
        	parent::__construct($message,$code);

        	$this->type  = get_class($this);
        	$this->extra = $extra;
	}

	public function __toString()
	{
		$trace = $this->getTrace();
		if($this->extra)
		{
			array_shift($trace);
		}

		$this->class    = $trace[0]['class'];
		$this->function = $trace[0]['function'];
		$this->file     = $trace[0]['file'];
		$this->line     = $trace[0]['line'];
		$file           = file($this->file);
		$traceInfo      = '';
		$time           = date("y-m-d H:i:m");
		foreach($trace as $t)
		{
			    $traceInfo .= '['.$time.'] '.$t['file'].' ('.$t['line'].') ';
			    $traceInfo .= $t['class'].$t['type'].$t['function'].'(';
			    $traceInfo .= implode(', ', $t['args']);
			    $traceInfo .=")\r\n";
		}
		$error['message']   = $this->message;
		$error['type']      = $this->type;
		$error['detail']    = PAGE_NAME."\n";
		$error['detail']   .=   ($this->line-2).': '.$file[$this->line-3];
		$error['detail']   .=   ($this->line-1).': '.$file[$this->line-2];
		$error['detail']   .=   ($this->line).': <strong>'.$file[$this->line-1].'</strong>';
		$error['detail']   .=   ($this->line+1).': '.$file[$this->line];
		$error['detail']   .=   ($this->line+2).': '.$file[$this->line+1];
		$error['class']     =   $this->class;
		$error['function']  =   $this->function;
		$error['file']      = $this->file;
		$error['line']      = $this->line;
		$error['trace']     = $traceInfo;

		//log::write('('.$this->type.') '.$this->message);

		return $error ;
	}


}

?>
