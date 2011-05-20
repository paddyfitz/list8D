<?php 

abstract class List8D_Report {
	
	protected $_aclResource;
	protected $_title;
	protected $_fields;
	protected $_page=1;
	protected $_pageCount;
	protected $_limit=25;
	protected $_countLimit=500;
	protected $_filters=array();
	public $controller;
	protected $_message;
	protected $_query;
	protected $_countQuery;
	protected $_orderBy="";
	protected $_itemTypes = array();

	function init() {}
	
	function __construct () {
		$this->init();
	}
	
	function setQuery($query) {
		$this->_query = $query;
		return $this;
	}
	
	function getQuery () {
		return $this->_query;
	}
	
	function setItemTypes($type) {
		$this->_itemTypes = $type;
		return $this;
	}
	
	function getItemTypes () {
		return $this->_itemTypes;
	}
	
	function addItemType() {
		foreach (func_get_args() as $arg) {
			$this->_itemTypes[] = $arg;
		}
		return $this;
	}
	function addItemTypes() {
		$args = func_get_args();
		call_user_func_array(array($this,'addItemType'),$args);
	}


	function setOrderBy($order,$desc = false) {
		$this->_orderBy = " ORDER BY $order";
		if ($desc) {
			$this->_orderBy .= " DESC";
		}
		return $this;
	}
	
	function getOrderBy () {
		return $this->_orderBy;
	}

	
	function setCountQuery($query) {
		$this->_countQuery = $query;
		return $this;
	}
	
	function getCountQuery () {
		if (!isset($this->_countQuery))
			$this->setCountQuery("SELECT count(*) AS count FROM ({$this->getQuery()} LIMIT {$this->getCountLimit()}) AS count_table");
		return $this->_countQuery;
	}
	
	function setFields($fields) {
		$this->_fields = $fields;
		return $this;
	}
	
	public function getAclResource() {
		return $this->_aclResource;
	}
	
	public function getMessage() {
		return $this->_message;
	}
	
	public function setMessage($message) {
		$this->_message = $message;
	}
	
	public function getTitle() {
		return $this->_title;
	}

	public function setTitle($title) {
		$this->_title = $title;
	}
	
	public function setPage($page) {
		$this->_page = $page;
		return $this;
	}
	
	public function getPage() {
		return $this->_page;
	}
	
	public function setPageCount($pageCount) {
		$this->_pageCount = $pageCount;
		return $this;
	}
	
	public function getPageCount() {
		if (!isset($this->_pageCount))
			$this->setPageCount(ceil($this->getTotalItemCount()/$this->getLimit()));
		return $this->_pageCount;
	}
	
	public function setLimit($limit) {
		$this->_limit = $limit;
		return $this;
	}
	
	public function setCountLimit($limit) {
		$this->_countLimit = $limit;
		return $this;
	}
	
	public function getCountLimit() {
		return $this->_countLimit;
	}
	
	public function getLimit() {
		return $this->_limit;
	}
	
	public function getFields() {
		return $this->_fields;
	}
	
	public function setView($view) {
		$this->_view = $view;
	}
	
	public function getView() {
		return $this->_view;
	}
	
	public function setController($controller) {
		$this->controller = $controller;
	}
	
	public function pager() {
		
		return $this->getView()->pager($this->getTotalItemCount(),$this->getLimit(),get_class($this)."-page");
			
	}
	
	public function getFilters() {
		$filters = $this->_filters;
		foreach($filters as $key => &$filter) {
			$function = "get".$key."Filter";
			$filter['value'] = $this->$function();
		}
		return $filters;
	}
	
	public function getItems($reload = false) {
		
		// if items have not been loaded or we want to reload load them
		if (!isset($this->_items) || $reload)
			$this->loadItems();
			
		return $this->_items;
		
	}
	
	public function loadItems() {
	
		$db = Zend_Registry::get('dbResource');
		$db = $db->getDbAdapter();
		
		$this->_rows = $db->fetchAll($this->getQuery().$this->getOrderBy().' LIMIT '.$this->getLimit().' OFFSET '.($this->getPage()-1)*$this->getLimit());
	
		$this->_items = array();
		
		$fields = $this->getFields();

		foreach ($this->_rows as $row) {
			
			foreach($fields as $key => $field) {
				$func = $field['function'];
				$item[$field['label']] = $this->$func($row);
			}
			
			$this->_items[] = $item;
			
		}
		return $this;
		
	}
	
	public function getTotalItemCount() {
	
		$db = Zend_Registry::get('dbResource');
		$db = $db->getDbAdapter();
		$count = $db->fetchAll($this->getCountQuery());
		
		return $count[0]['count'];
		
	}
}