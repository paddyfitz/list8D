<?php

class List8D_Theme_Root_Helper_UrlThumb extends List8D_ViewHelper {
	
    
    public function urlThumb($url) {
			return "<img src='http://images.websnapr.com/?url={$url}&size=s' />";
		}	
		
}