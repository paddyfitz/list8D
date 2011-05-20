<?php 

class List8D_Reference {
	
	public function render ($type, $item, $resource=null) {
		
		if (!isset($resource)) {
			$resource = $item->getResource();
		}
		
		if (method_exists($this,'render'.$type))
			return call_user_func(array($this,'render'.$type),$item,$resource);
		else
			return $this->renderDefault($item,$resource);
		
	}
	
}