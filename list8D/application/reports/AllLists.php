<?php 

class List8D_Report_AllLists extends List8D_Report {

	//protected $_aclResource = "report-all-lists";
	//protected $_title = "All reading lists";
	public function init() {
		
		$this->setQuery("SELECT list.id, code.value as code, title.value as title FROM list LEFT JOIN list_data as code ON code.row_id = list.id AND code.`key`='code' LEFT JOIN list_data as title ON title.row_id = list.id AND title.`key`='title' ");
		
		$this->setFields(array(
			'code'=>array(
	  		'label'=>'Code',
	  		'function'=>'getCodeValue',
	  	),
	  	'name'=>array(
	  		'label'=>'Name',
	  		'function'=>'getTitleValue',
	  	),
	  	'tags'=>array(
	  		'label'=>'Tags',
	  		'function'=>'getTagsValue',
	  	),
	  	'actions'=>array(
	  		'label'=>'Actions',
	  		'function'=>'getActionsValue',
	  	),
		));
		
	}
	
	public function getCodeValue($row) {
		$value = unserialize($row['code']);
		return "<a href='{$this->getView()->url(array('controller'=>'list','action'=>'view','id'=>$row['id']))}'>{$value}</a>";
	}
	
	public function getTitleValue($row) {
		$value = unserialize($row['title']);
		return "<a href='{$this->getView()->url(array('controller'=>'list','action'=>'view','id'=>$row['id']))}'>{$value}</a>";
	}
	
	public function getTagsValue($row) {
		$list = new List8D_Model_List();
		$list = $list->getById($row['id']);
		return implode(", ",$list->getTags());
	}
	
	public function getActionsValue($row) {
		
		$return = "";
		
		$return .= "<a href='{$this->getView()->url(array('controller'=>'list','action'=>'view','id'=>$row['id']))}' class='icon icon-edit'>edit</a>";
		//$return .= "<a href='{$this->getView()->url(array('controller'=>'list','action'=>'delete','id'=>$row['id']))}' class='icon icon-delete'>delete</a>";
		
		return $return;
	}
	
	public function getItemType() {
		return 'list';
	}
	
}