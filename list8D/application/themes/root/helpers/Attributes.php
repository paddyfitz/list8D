<?php

class List8D_Theme_Root_Helper_Attributes extends List8D_ViewHelper
{
    public function attributes($attributes = array())
    {
    
    	$output = "";
    	
    	foreach($attributes as $key => $value) {
    		if (is_array($value)) {
    			$output .= "$key='";
    			foreach($value as $class) {
    				$output .= "$class ";
    			}
    			$output .= "'";
    		} else {
    			$output .= "$key='$value' ";
    		}
    		
    	}
    	
    	return $output;
    }
    
}