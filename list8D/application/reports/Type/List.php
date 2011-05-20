<?php 

class List8D_Report_Type_List extends List8D_Report {

	protected $_items;
	protected $_itemObjects;
	
	public function __construct($controller=false) {
		
		if ($controller) {
			$this->setController($controller);
		}
		
		if (method_exists($this,'init'))
			$this->init();
			
    $params = $this->controller->getRequest()->getParams();
    
    if (isset($params[get_class($this)."-page"]))
    	$page = $params[get_class($this)."-page"];
    else 
    	$page = 1;
    if ($page > $this->getPageCount() && $this->getPageCount()) {
    	$page = $this->getPageCount();
    }
    	
		$this->setPage($page);

	}
	
	public function getItems($reload = false) {
		
		// if items have not been loaded or we want to reload load them
		if (!isset($this->_items) || $reload)
			$this->loadItems();
			
		return $this->_items;
		
	}
	
	public function getPageCount() { 

		if (!isset($this->_pageCount)) {
			$this->_pageCount = ceil($this->getTotalItemCount()/$this->getLimit());
		}
		return $this->_pageCount;
	}
	
}