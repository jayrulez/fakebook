<?php

class Validation extends Base
{
    static $regex = array(
            'require'=> '/.+/',
            'email' => '/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/',
            'phone' => '/^((\(\d{2,3}\))|(\d{3}\-))?(\(0\d{2,3}\)|0\d{2,3}-)?[1-9]\d{6,7}(\-\d{1,4})?$/',
            'mobile' => '/^((\(\d{2,3}\))|(\d{3}\-))?(13|15|18)\d{9}$/',
            'url' => '/^https?:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"\"])*$/',
            'currency' => '/^\d+(\.\d+)?$/',
            'number' => '/\d+$/',
            'zip' => '/^[1-9]\d{5}$/',
            'qq' => '/^[1-9]\d{4,12}$/',
            'integer' => '/^[-\+]?\d+$/',
            'double' => '/^[-\+]?\d+(\.\d+)?$/',
            'english' => '/^[A-Za-z]+$/',
    );

    static function check($value,$checkName)
    {
        $matchRegex = self::getRegex($checkName);
        return preg_match($matchRegex,trim($value));
    }

    static function getRegex($name)
    {
        if(isset(self::$regex[strtolower($name)])) {
            return self::$regex[strtolower($name)];
        }else {
            return $name;
        }
    }

}

?>