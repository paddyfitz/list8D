<?php

class List8D_Theme_Root_Helper_Ul extends List8D_ViewHelper
{
    public function ul($list,$options=array())
    {
			if (!count($list))
				return null;
			
			if(empty($options['attributes']) || !is_array($options['attributes']))	
				$options['attributes'] = array();
				
    	$output = "<ul {$this->view->attributes($options['attributes'])}>";
    	foreach($list as $item) {
    		if (is_string($item)) {
					$item = array(
						'value'=>$item,
						'options'=>array(),
					);
    		} 
    		
    		if (!isset($item['options'])) {
    		  $item['options']=array();
    		}
    		
    		if (!isset($item['children'])) {
    		  $item['children']=array();
    		}
    		
    		$output .= $this->view->li($item['value'],$item['children'],$item['options']);
    		
    	}
    	$output .= "</ul>";
    	
    	return $output;
    	
    }
}