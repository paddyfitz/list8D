<?php

class List8D_Theme_Root_Helper_HeadLink extends Zend_View_Helper_HeadLink {
	
	public function appendStylesheet($href, $media='all', $conditionalStylesheet=null, $extras=null) {
		if (isset($_SERVER['HTTPS'])) {
			$s = "s";
		} else {
			$s = "";
		}
		$href_print_out = null;
		if (strpos($href,":") !== false) {
			$href = explode(":",$href);
			$theme = $href[0];
			$href = $href[1];
			if (is_file(APPLICATION_PATH."/themes/".$theme."/css/".$href)) 		
				$href_out = "http$s://{$_SERVER['HTTP_HOST']}".$this->view->baseUrl()."/themes/".$theme."/css/".$href;
			if (is_file(APPLICATION_PATH."/themes/".$theme."/css/print-".$href))
				$href_print_out = "http$s://{$_SERVER['HTTP_HOST']}".$this->view->baseUrl()."/themes/".$theme."/css/print-".$href;
		} else {
			//! TODO - do we really need to be interating over every extended theme info file each time we append a css file?
			$currentTheme = $this->view->theme;
			$href_out = false;
			// if current theme has css file use that
			// TODO needs to handle https
			if (is_file(APPLICATION_PATH."/themes/".$currentTheme."/css/".$href)) {		
				$href_out = "http$s://{$_SERVER['HTTP_HOST']}".$this->view->baseUrl()."/themes/".$currentTheme."/css/".$href;
				if (is_file(APPLICATION_PATH."/themes/".$currentTheme."/css/print-".$href)){
					$href_print_out = "http$s://{$_SERVER['HTTP_HOST']}".$this->view->baseUrl()."/themes/".$currentTheme."/css/print-".$href;
				}		
			} else if (is_file(APPLICATION_PATH."/themes/".$currentTheme."/".$href)) {
				$href_out = "http$s://{$_SERVER['HTTP_HOST']}".$this->view->baseUrl()."/themes/".$currentTheme."/".$href;
				if(is_file(APPLICATION_PATH."/themes/".$currentTheme."/print-".$href)){
					$href_print_out = "http$s://{$_SERVER['HTTP_HOST']}".$this->view->baseUrl()."/themes/".$currentTheme."/print-".$href;
				}			
			}
			
			// otherwise look in each of the extended themes
			else {
				$themeinfo = List8D_Config_Ini::getThemeInfo($currentTheme);
				while ($themeinfo->extends) {
					if (is_file(APPLICATION_PATH."/themes/".$themeinfo->extends."/css/".$href)) {
						$href_out = "http$s://{$_SERVER['HTTP_HOST']}".$this->view->baseUrl()."/themes/".$themeinfo->extends."/css/".$href;
						if(is_file(APPLICATION_PATH."/themes/".$themeinfo->extends."/css/print-".$href)) {
							$href_print_out = "http$s://{$_SERVER['HTTP_HOST']}".$this->view->baseUrl()."/themes/".$themeinfo->extends."/css/print-".$href;
						}	
					} else if (is_file(APPLICATION_PATH."/themes/".$themeinfo->extends."/".$href)) {
						$href_out = "http$s://{$_SERVER['HTTP_HOST']}".$this->view->baseUrl()."/themes/".$themeinfo->extends."/".$href;
						if (is_file(APPLICATION_PATH."/themes/".$themeinfo->extends."/print-".$href)) {
							$href_print_out = "http$s://{$_SERVER['HTTP_HOST']}".$this->view->baseUrl()."/themes/".$themeinfo->extends."/print-".$href;
						}		
					}
					$themeinfo = List8D_Config_Ini::getThemeInfo($themeinfo->extends);	
				}
			}
			
		}
		
		if ($href_out) {
			$this->__call('appendStylesheet',array($href_out, $media, $conditionalStylesheet, $extras));
		}
		if($href_print_out){
			$this->__call('appendStylesheet',array($href_print_out, "print", $conditionalStylesheet, $extras));
		}
		return $this;
	}
	
	
 	
}
	