<?php 

class List8D_Config_Ini extends Zend_Config_Ini {

	static function getThemeInfo($theme) {
		
		//! TODO - this sould probably be stored in the registry but this will work for now
		global $themeInfo;
		
		if (!isset($themeInfo))
			$themeInfo = array();
		
		if (!isset($themeInfo[$theme]))	{
		  $info = new Zend_Config_Ini(APPLICATION_PATH."/themes/".$theme."/".$theme.".info");
		  $themeInfo[$theme] = $info;
		} 
		
		global $application;
		$environment = $application->getEnvironment();
	
	  if (isset($themeInfo[$theme]->$environment))
	  	return $themeInfo[$theme]->$environment;			
	  else 
	  	return $themeInfo[$theme]->production;
	  	
	  
	}
	
	static function getThemeSettings() {
	
		//! TODO - this sould probably be stored in the registry but this will work for now
		global $themeSettings;
		
		if (!isset($themeSettings)) {
		  $themeSettings = new Zend_Config_Ini(APPLICATION_PATH."/configs/theme.ini");
		}
		
	  // get theme settings & info
	  global $application;
	  
	  $environment = $application->getEnvironment();
	  if (isset($themeSettings->$environment))
	  	return $themeSettings->$environment;
	  else 
	  	return $themeSettings->production;
	  	
	}
	
}