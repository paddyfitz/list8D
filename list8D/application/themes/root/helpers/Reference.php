<?php 

class List8D_Theme_Root_Helper_Reference extends List8D_ViewHelper  {

	public function setView($view) {
    
    $this->view = $view;
    
	}

	public function reference($item,$resource=null) {
		
		if ($resource==null) {
			$resource = $item->getResource();
		}
		
		$reference = Zend_Registry::get('reference');
		
		if ($resource->useTypeReference())
			$type = $resource->useTypeReference();
		else 
			$type = $resource->getType();
		
		return $reference->render($type,$item,$resource);
			
	}
	
}
