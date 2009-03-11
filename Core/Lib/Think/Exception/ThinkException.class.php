<?php

class ThinkException extends Exception
{
    private $type;
    private $extra;

    public function __construct($message,$code=0,$extra=false)
    {
        parent::__construct($message,$code);
        $this->type = get_class($this);
        $this->extra = $extra;
    }

    public function __toString()
    {
        $trace = $this->getTrace();
        if($this->extra) {
            array_shift($trace);
        }
        $this->class = $trace[0]['class'];
        $this->function = $trace[0]['function'];
        $this->file = $trace[0]['file'];
        $this->line = $trace[0]['line'];
        $file   =   file($this->file);
        $traceInfo='';
        $time = date("y-m-d H:i:m");
        foreach($trace as $t) {
            $traceInfo .= '['.$time.'] '.$t['file'].' ('.$t['line'].') ';
            $traceInfo .= $t['class'].$t['type'].$t['function'].'(';
            $traceInfo .= implode(', ', $t['args']);
            $traceInfo .=")\r\n";
        }
        $error['message']   = $this->message;
        $error['type']      = $this->type;
        $error['detail']    = L('_MODULE_').'['.MODULE_NAME.'] '.L('_ACTION_').'['.ACTION_NAME.']'."\r\n";
        $error['detail']   .=   ($this->line-2).': '.$file[$this->line-3];
        $error['detail']   .=   ($this->line-1).': '.$file[$this->line-2];
        $error['detail']   .=   '<span class="errline">'.($this->line).': <strong>'.$file[$this->line-1].'</strong></span>';//make red with css
        $error['detail']   .=   ($this->line+1).': '.$file[$this->line];
        $error['detail']   .=   ($this->line+2).': '.$file[$this->line+1];
        $error['class']     =   $this->class;
        $error['function']  =   $this->function;
        $error['file']      = $this->file;
        $error['line']      = $this->line;
        $error['trace']     = $traceInfo;

        Log::write('('.$this->type.') '.$this->message);

        return $error ;
    }
}

?>