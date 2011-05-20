<?php

class list8D_template {

	public function render ($filename=null,$variables=array()) {

		if ($filename==null)
			$filename = $this->filename;
		
		$template = clone $this;
		
		foreach($variables as $key => $variable) {
			$template->$key = $variable;
		}
		
		return $template->_render($filename);
	
	}
	
	public function _render($filename,$variables=array()) {

		ob_start();
		require('templates/'.$filename);
		$return = ob_get_contents();
		ob_end_clean();
		return $return;
	}
	
	public function getVariables() {
		return $this->variables;
	}

}