<?php

function pre_dump($subject) {
	echo "<pre>";
	var_dump($subject);
	echo "</pre>";
}

function s_unserialize ($serial) {
	
	$result = @unserialize (utf8_encode($serial));
	
	if ($result===false && $serial != serialize(false)) {
		//find an array of 1...
		if (preg_match("/^a:1:\\{i:0;(.*)\\}$/",$serial,$matches)) {
			return s_unserialize($matches[1]);
		} else if(preg_match("/^s:[0-9]+:\"(.*)\";$/",$serial,$matches)) {
			return $matches[1];
		}
		else{
			return $serial;
		}
		
	} else {
		return $result;
	}	
}