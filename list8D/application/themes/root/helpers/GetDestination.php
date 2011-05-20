<?php

class List8D_Theme_Root_Helper_GetDestination extends List8D_ViewHelper {
	
	public function getDestination() {
		
		//! TODO posible xss vunerability needs to be escaped
		if(isset($_GET['destination'])) {
    	return $_GET['destination'];
    } else {
    	return $this->view->url();
    }

	}
	
}