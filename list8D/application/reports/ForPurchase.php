<?php 

class List8D_Report_ForPurchase extends List8D_Report {
	
	public function init() {
	
		$this->setQuery('SELECT r.id, r.created, r2.value as title, r3.value as ordered FROM `resource` AS r LEFT JOIN `resource_data` AS r1 ON r1.row_id = r.id AND r1.`key`="Voyager_rcn" LEFT JOIN `resource_data` AS r2 ON r2.row_id = r.id AND r2.`key`="title" LEFT JOIN `resource_data` AS r3 ON r3.row_id = r.id AND r2.`key`="ordered" WHERE (r1.`value` IS NULL OR r1.`value` = "") AND (r.class="List8D_Model_Resource_Book" OR r.class="List8D_Model_Resource_PhysicalMedia" OR r.class="List8D_Model_Resource_BookLegacy" OR r.class="List8D_Model_Resource_PhysicalMediaLegacy")');

		$this->setOrderBy('r.created');
		
		$this->setFields(array(
			'title'=>array(
		  	'label'=>'Title',
		  	'function'=>'getTitleValue',
			),
			'ordered'=>array(
		  	'label'=>'Ordered',
		  	'function'=>'getOrderedValue',
			),
	  ));
	  
		$this->addItemTypes("resource");
		
	}
	
	
	//protected $_aclResource = "report-for-purchase";
	//protected $_title = "Resources requiring purchasing";
	
	
	/*
protected $_filters = array(
		'ModuleCode'=>array(
			'title'=>"Module code"
		)
	);
*/

	public function getTitleValue($row) {
		$title = unserialize($row['title']);
		
		$resource = new List8D_Model_Resource();
		$resource = $resource->getById($row['id']);
		$return = "<a href='{$this->getView()->url(array('controller'=>'resource','action'=>'view','id'=>$row['id'],'List8D_Report_ForPurchase-page'=>null,'destination'=>array()))}'>{$title}</a>";
		$return .= "<ul>";
		foreach($resource->getItems() as $item) {
			$return .= "<li>";
			$return .= $this->getView()->date($row['created'],'condensed')." ";
			$return .= "<a href='{$this->getView()->url(array('controller'=>'list','action'=>'view','id'=>$item->getTrunk()->getId()))}#item_{$item->getId()}'>{$item->getTrunk()->getDataValue("code")}</a>";
			if ($item->getDataValue("private_notes")) 
				$return .= ", {$item->getDataValue("private_notes")}";
			$return .= "</li>";
		}
		$return .= "</ul>";
		return $return;
	}
	
	
	
	public function getOrderedValue($row) {
		$output = "<a href='{$this->getView()->url(array('controller'=>'data','action'=>'toggleresource','resourceid'=>$row['id'],'data'=>'ordered','List8D_Report_ForPurchase-page'=>null,'destination'=>array()))}' ";
		if ($row['ordered']) {
			$output .= "class='ordered'><span class='text'>ordered</span>";
		} else {
			$output .= "class='purchase'><span class='text'>needs purchasing</span>";		
		}
		$output .= "</a>";
		return $output;
	}
		
}