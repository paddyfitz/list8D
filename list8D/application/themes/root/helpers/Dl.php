<?php

class List8D_Theme_Root_Helper_Dl extends List8D_ViewHelper
{
    public function dl($list,$options=array())
    {

    	$output = "<dl>";
    	foreach($list as $title => $definition) {
    		
    		$output .= "<dt>$title</dt>";
    		$output .= "<dd>$definition</dd>";
    		
    	}
    	$output .= "</dl>";
    	
    	return $output;
    	
    }
}