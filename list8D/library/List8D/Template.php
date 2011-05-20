<?php

class List8D_Template extends Zend_View {
	
	 
   static function getThemeSettings() {
   		$settings = new Zend_Config_Ini(APPLICATION_PATH."/configs/theme.ini");
   		return $settings;
   }
   
   public function setLayoutVar($index,$value) {
      $this->_helper->layout->getLayoutInstance()->$index = $value;
   }
   
   public function setLayoutTitle($value) {
   		$this->setLayoutVar("title",$value);
   }
   
   
	
}