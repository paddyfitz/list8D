<?php

class List8D_Theme_Root_Helper_RenderAuthors extends List8D_ViewHelper {
	
    
    public function renderAuthors($authors,$options=array()) {
    	
    	if (!empty($authors)) {
    		return "by ".$authors;
    	} else {
    		return "";
    	}
    	
    }
    
}