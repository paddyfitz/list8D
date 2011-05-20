<?php

class List8D_Theme_Root_Helper_Li extends List8D_ViewHelper
{
    public function li($text,$children = array(),$options = array())
    {

    	if(empty($options['attributes']) || !is_array($options['attributes']))	
				$options['attributes'] = array();
    
    	$output = "<li {$this->view->attributes($options['attributes'])}>";
    				
    	if (isset($options['link']) && is_array($options['link'])) {
  			$output .= $this->view->a($text,$options['link']['href'],array('attributes'=>$options['link']));
    	} else {
	    	$output .= $text;	
    	}
    	
    	
    	if (isset($children) && is_array($children) && count($children)) {
    	 $output .= $this->view->ul($children);
      }
    
    	$output .= "</li>";
    	
    	return $output;
    	
    }
}