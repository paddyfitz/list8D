<?php

class List8D_Theme_Root_Helper_L extends List8D_ViewHelper {
	
    
    public function l($text,$arguments=array(),$options=array()) {

    	$options['attributes']['href'] = $arguments;
    	$output = $this->view->a($text,$options);
    	
    	return $output;
		}	
		
}