<?php

class Debug extends Base
{
    static private $marker =  array();

    static public function mark($name)
    {
        self::$marker['time'][$name]  =  microtime(TRUE);
        if(MEMORY_LIMIT_ON) {
            self::$marker['mem'][$name] = memory_get_usage();
            self::$marker['peak'][$name] = function_exists('memory_get_peak_usage')?memory_get_peak_usage(): self::$marker['mem'][$name];
        }
    }

    static public function useTime($start,$end,$decimals = 6)
    {
        if ( ! isset(self::$marker['time'][$start]))
        {
            return '';
        }
        if ( ! isset(self::$marker['time'][$end]))
        {
            self::$marker['time'][$end] = microtime(TRUE);
        }
        return number_format(self::$marker['time'][$end] - self::$marker['time'][$start], $decimals);
    }

    static public function useMemory($start,$end)
    {
        if(!MEMORY_LIMIT_ON) {
            return '';
        }
        if ( ! isset(self::$marker['mem'][$start]))
        {
            return '';
        }
        if ( ! isset(self::$marker['mem'][$end]))
        {
            self::$marker['mem'][$end] = memory_get_usage();
        }
        return number_format((self::$marker['mem'][$end] - self::$marker['mem'][$start])/1024);
    }

    static function getMemPeak($start,$end) {
        if(!MEMORY_LIMIT_ON) {
            return '';
        }
        if ( ! isset(self::$marker['peak'][$start]))
        {
            return '';
        }
        if ( ! isset(self::$marker['peak'][$end]))
        {
            self::$marker['peak'][$end] = function_exists('memory_get_peak_usage')?memory_get_peak_usage(): memory_get_usage();
        }
        return number_format(max(self::$marker['peak'][$start],self::$marker['peak'][$end])/1024);
    }
}

?>