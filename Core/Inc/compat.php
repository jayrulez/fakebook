<?php

if(version_compare(PHP_VERSION,'5.2.0','<') ) {
	if (!function_exists('json_encode')) {
		 function format_json_value(&$value)
		{
			if(is_bool($value)) {
				$value = $value?'true':'false';
			}elseif(is_int($value)) {
				$value = intval($value);
			}elseif(is_float($value)) {
				$value = floatval($value);
			}elseif(defined($value) && $value === null) {
				$value = strval(constant($value));
			}elseif(is_string($value)) {
				$value = '"'.addslashes($value).'"';
			}
			return $value;
		}

		function json_encode($data)
		{
			if(is_object($data)) {
				$data = get_object_vars($data);
			}else if(!is_array($data)) {
				return format_json_value($data);
			}
			if(empty($data) || is_numeric(implode('',array_keys($data)))) {
				$assoc  =  false;
			}else {
				$assoc  =  true;
			}
			$json = $assoc ? '{' : '[' ;
			foreach($data as $key=>$val) {
				if(!is_null($val)) {
					if($assoc) {
						$json .= "\"$key\":".json_encode($val).",";
					}else {
						$json .= json_encode($val).",";
					}
				}
			}
			if(strlen($json)>1) {
				$json  = substr($json,0,-1);
			}
			$json .= $assoc ? '}' : ']' ;
			return $json;
		}
	}
	if (!function_exists('json_decode')) {
		function json_decode($json,$assoc=false)
		{
			$begin  =  substr($json,0,1) ;
			if(!in_array($begin,array('{','['))) {
				return $json;
			}
			$parse = substr($json,1,-1);
			$data  = explode(',',$parse);
			if($flag = $begin =='{' ) {
				$result   = new stdClass();
				foreach($data as $val) {
					$item    = explode(':',$val);
					$key =  substr($item[0],1,-1);
					$result->$key = json_decode($item[1],$assoc);
				}
				if($assoc) {
					$result   = get_object_vars($result);
				}
			}else {
				$result   = array();
				foreach($data as $val) {
					$result[]  =  json_decode($val,$assoc);
				}
			}
			return $result;
		}
	}
	if (!function_exists('property_exists')) {
		function property_exists($class, $property) {
			if (is_object($class))
			 $class = get_class($class);
			return array_key_exists($property, get_class_vars($class));
		}
	}
}

?>