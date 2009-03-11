<?php

class Log extends Base
{
    const EMERG   = 'EMERG';  // Emergency: system is unusable
    const ALERT    = 'ALERT';  // Alert: action must be taken immediately
    const CRIT      = 'CRIT';  // Critical: critical conditions
    const ERR       = 'ERR';  // Error: error conditions
    const WARN    = 'WARN';  // Warning: warning conditions
    const NOTICE  = 'NOTIC';  // Notice: normal but significant condition
    const INFO     = 'INFO';  // Informational: informational messages
    const DEBUG   = 'DEBUG';  // Debug: debug messages
    const SQL       = 'SQL';  // SQL：sql messages

    const SYSTEM = 0;
    const MAIL      = 1;
    const TCP       = 2;
    const FILE       = 3;

    static $log =   array();

    static $format =  '[ c ]';

    static function record($message,$level=self::ERR,$record=false) {
        if($record || in_array($level,C('LOG_RECORD_LEVEL'))) {
            $now = date(self::$format);
            self::$log[] =   "{$now} {$level}: {$message}\r\n";
        }
    }

    static function save($type=self::FILE,$destination='',$extra='')
    {
        if(empty($destination)) {
            $destination = LOG_PATH.date('y_m_d').".log";
        }
        if(self::FILE == $type) {
            if(is_file($destination) && floor(C('LOG_FILE_SIZE')) <= filesize($destination) ){
                  rename($destination,dirname($destination).'/'.time().'-'.basename($destination));
            }
        }
        $result   =  error_log(implode("",self::$log), $type,$destination ,$extra);
        self::$log = array();
        //clearstatcache();
    }

    static function write($message,$level=self::ERR,$type=self::FILE,$destination='',$extra='')
    {
        $now = date(self::$format);
        if(empty($destination)) {
            $destination = LOG_PATH.date('y_m_d').".log";
        }
        if(self::FILE == $type) { 
            if(is_file($destination) && floor(C('LOG_FILE_SIZE')) <= filesize($destination) ){
                  rename($destination,dirname($destination).'/'.time().'-'.basename($destination));
            }
        }
        error_log("{$now} {$level}: {$message}\r\n", $type,$destination,$extra );
        //clearstatcache();
    }
}

?>