<?php

class List8D_Theme_Root_Helper_Url extends Zend_View_Helper_Url {

	public function url(array $urlOptions = array(), $name = null, $reset = false, $encode = true,$clear = true) {
		$router = Zend_Controller_Front::getInstance()->getRouter();
		$params = array();
		if (isset($urlOptions['destination']) && is_array($urlOptions['destination'])) {
			$params[] = "destination=".$router->assemble($urlOptions['destination'], $name, $reset, $encode);
		} else if (isset($urlOptions['destination'])) {
			$params[] = "destination=".$urlOptions['destination'];
			unset($urlOptions['destination']);
		} 
		
		if (count($params))
			$params = "?".implode("&",$params);
		else
			$params = "";
		$requestParams = Zend_Controller_Front::getInstance()->getRequest()->getParams();
		foreach($requestParams as $param => $value) {
			if (!isset($urlOptions[$param])) {
				$newUrlOptions[$param]=$value;
			}		
		}

		if ($clear) {
			foreach($requestParams as $key => $value) {
				$newUrlOptions[$key] = null;
			}
		}
		
		if (!isset($urlOptions['load']) && isset($newUrlOptions['load']))
			$newUrlOptions['load'] = null;	
		$urlOptions= array_merge($newUrlOptions,$urlOptions);

		return $router->assemble($urlOptions, $name, $reset, $encode).$params;
	}

}