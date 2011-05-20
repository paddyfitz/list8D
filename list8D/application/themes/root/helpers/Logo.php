<?php

class List8D_Theme_Root_Helper_Logo extends List8D_ViewHelper  {
	
	public function logo() {
		
		
		$themeSettings = List8D_Config_Ini::getThemeSettings();
		$frontController = Zend_Controller_Front::getInstance();

		if ($frontController->getDefaultControllerName() == "front") 
			$themeSettings= $themeSettings->front;
		
		if (isset($themeSettings->logo->alt)) {
			$alt = $themeSettings->logo->alt;
		} else {
			$alt = "logo";
		}
		
		if (isset($themeSettings->logo->src)) {
			return "<img alt='$alt' src='{$themeSettings->logo->src}' class='logo' />"; 
		} else {
			return "";
		}
		
	}
	
}