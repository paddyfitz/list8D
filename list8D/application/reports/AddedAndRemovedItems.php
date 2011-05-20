<?php 

class List8D_Report_AddedAndRemoveditems extends List8D_Report_Type_List {
	
	protected $_fields = array(
	  'list'=>array(
	  	'title'=>"List",
	  	'attributes'=>array(),
	  ),
	  'action'=>array(
	  	'title'=>'Action',
	  	'attributes'=>array(),
	  ),
	  'resource'=>array(
	  	'title'=>'Resource',
	  	'attributes'=>array(),
	  ),
	);

	
	public function getTotalItemCount() {
		$db = Zend_Registry::get('dbResource');
		$db = $db->getDbAdapter();
		$count = $db->fetchAll("SELECT count(*) as count FROM `change_log` WHERE `table` = 'item' AND (`action` = 'insert' OR (`action`='delete' AND `value_from` IS NOT NULL AND `value_from`!='')) ORDER BY `changed` DESC");
		return $count[0]['count'];
	}
	
	
	
	public function loadItems() {
		$db = Zend_Registry::get('dbResource');
		$db = $db->getDbAdapter();

		$this->_rows = $db->fetchAll("SELECT `row_id`,`changed`,`action`,`value_from` FROM `change_log` WHERE `table` = 'item' AND (`action` = 'insert' OR (`action`='delete' AND `value_from` IS NOT NULL AND `value_from`!='')) ORDER BY `changed` DESC LIMIT ".$this->getLimit()." OFFSET ".($this->getPage()-1)*$this->getLimit());
		$this->_items = array();
		
		foreach ($this->_rows as $row) {
			if ($row['action'] == 'insert') {
				$action = "<span class='added'>added</span>";
				$itemOnList = "on list ";
			} else {
				$action = "<span class='removed'>removed</span>";
				$itemOnList = "";
			}
			$item = new List8D_Model_Item();
			if ($item = $item->getById($row['row_id'])) {
				$trunk = $item->getTrunk();
				$resource = $item->getResource();
			} else {
				$oldRow = unserialize($row['value_from']);
				$trunk = new List8D_Model_List();
				$trunk = $trunk->getById($oldRow['trunk_id']);
				$resource = new List8D_Model_Resource();
				$resource = $resource->getById($oldRow['resource_id']);
			}
			
			$tr = array(
			  'attributes'=>array(
			  	'class'=>"item"
			  ),
			  'fields'=>array(
			  	'list'=>array(
			  		'value'=>"<a href='{$this->getView()->url(array('controller'=>'list','action'=>'view','id'=>$trunk->getId()))}}' title='view {$itemOnList}list'>{$trunk->getDataValue('code')}</a>",
			  	),
			  	'action'=>array(
			  		'value'=>$action,
			  	),
			  	'resource'=>array(
			  		'value'=>"<a href='{$this->getView()->url(array('controller'=>'resource','action'=>'view','id'=>$resource->getId()))}' title='view resource'>{$resource->getDataValue('title')}</a>",
			  	)
			  ),
			);
			
			
			$this->_items[$item->getId()] = $tr;
		}
		
		
		
	}
	
	public function getItemType() {
	
	}
	
}