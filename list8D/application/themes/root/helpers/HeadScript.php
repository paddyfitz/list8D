<?php

class List8D_Theme_Root_Helper_HeadScript extends Zend_View_Helper_HeadScript {
	 
  public function appendFile($href, $type='text/javascript', $attrs=null) {
		
		$themeSettings = List8D_Config_Ini::getThemeSettings();
		
		if (LIST8D_APPLICATION=='front')
			$currentTheme = $themeSettings->front->theme;
		else
			$currentTheme = $themeSettings->theme;
				
		$session = new Zend_Session_Namespace('Default');		
				
		if (isset($_GET['theme'])) {
		  $currentTheme = $_GET['theme'];
		  $session->theme = $_GET['theme'];
		} else if ($session->theme) {
		  $currentTheme = $session->theme;
		}
		echo
		$href_out = false;
		// if current theme has js file use that
		if (is_file(APPLICATION_PATH."/themes/".$currentTheme."/js/".$href)) {		
			$href_out = $this->view->baseUrl()."/themes/".$currentTheme."/js/".$href;		
		} 
		
		// otherwise look in each of the extended themes
		else {
			$themeinfo = List8D_Config_Ini::getThemeInfo($currentTheme);
			while ($themeinfo->extends) {
				if (is_file(APPLICATION_PATH."/themes/".$themeinfo->extends."/js/".$href)) {
					$href_out = $this->view->baseUrl()."/themes/".$themeinfo->extends."/js/".$href;		
					break;
				}
				$themeinfo = List8D_Config_Ini::getThemeInfo($themeinfo->extends);	
			}
		}

//		echo APPLICATION_PATH."../public/js/".$href;exit;
		// if in list8D core use 
		if (!$href_out && is_file(APPLICATION_PATH."/../public/js/".$href)) {
			$href_out = $this->view->baseUrl()."/js/".$href;	
		}
		
		if ($href_out) {
			$this->__call('appendFile',array($href_out, $type, $attrs));
		}
		return $this;
	}
		
	
}