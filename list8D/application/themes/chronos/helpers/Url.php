<?php

class List8D_Theme_Chronos_Helper_Url extends Zend_View_Helper_Url {

	public function url(array $urlOptions = array(), $name = null, $reset = false, $encode = true) {
		$params = Zend_Controller_Front::getInstance()->getRequest()->getParams();
		$output = $params['launch_presentation_return_url'];
		$output .= http_build_query($urlOptions);
		return $output;
	}

}