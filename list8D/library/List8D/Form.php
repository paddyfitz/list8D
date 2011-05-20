<?php 

class List8D_Form extends Zend_Form {
 
  public function getThemeSettings() {
  	global $application;
		$environment = $application->getEnvironment();
		$themeSettings = new Zend_Config_Ini(APPLICATION_PATH."/configs/theme.ini");
		if (isset($themeSettings->$environment))
			return $themeSettings->$environment;
		else 
			return $themeSettings->production;
  }
  
	public function __construct() {
		
		
		// get theme settings & info
		$themeSettings = List8D_Config_Ini::getThemeSettings();
		$currentTheme = $themeSettings->theme;
		
		$session = new Zend_Session_Namespace('Default');	
		if ($session->theme) {
			$currentTheme = $session->theme;
		}
		
		$themeinfo = List8D_Config_Ini::getThemeInfo($currentTheme);
		$currentThemeInfo = $themeinfo;
		$themeinfos = array($currentTheme => $themeinfo);
		
		while($themeinfo->extends) {
		
		  $key = $themeinfo->extends;
		  // load extended theme infos					
		  $themeinfo = List8D_Config_Ini::getThemeInfo($themeinfo->extends);
		  $themeinfos[$key] = $themeinfo;
		  
		}
		
		foreach(array_reverse($themeinfos) as $key => $themeinfo) {
		
			$this->addElementPrefixPath('List8D_Form_Decorator',
                 									APPLICATION_PATH . "/themes/".$key."/decorators",
                 									'decorator');
	
		}
		
		$this->addPrefixPath('List8D_Form_Element',
                 									APPLICATION_PATH . "/../library/List8D/Form/Element",
                 									'element');		
		
		parent::__construct();
	}

}