<?php

class List8D_ViewHelper
{
	public $view;
		
  public function setView($view) {
   	$this->view = $view;
  }
    
  public function getThemeSettings() {
  	global $application;
		$environment = $application->getEnvironment();
		$themeSettings = new Zend_Config_Ini(APPLICATION_PATH."/configs/theme.ini");
		if (isset($themeSettings->$environment))
			return $themeSettings->$environment;
		else 
			return $themeSettings->production;
  }
}