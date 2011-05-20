<?php

class List8D_Theme_Root_Helper_A extends List8D_ViewHelper {
	
    
    public function a($text,$arguments=array(),$options=array())
    {
			$output = "<a ";
			
			if (isset($arguments)) {
				$options['attributes']['href'] = $this->view->url($arguments);
			}
				$output .= $this->view->attributes($options['attributes']);
			
			$output .= ">";
			
			$output .= $text;
			
			$output .= "</a>";
			
    	return $output;
		}	
}