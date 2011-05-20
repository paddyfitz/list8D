<?php 

class List8D_Report_CoreText extends List8D_Report {

	public function init() {

		$this->setQuery("SELECT c.`changed`, c.`value_from` AS `from`, c.`value_to` AS `to`, i.`list_id`, d.row_id as `item_id` FROM change_log c LEFT JOIN item_data d ON d.id = c.row_id LEFT JOIN item i on i.id = d.row_id WHERE d.key = 'core_text' AND c.table = 'item_data' AND c.action = 'update'");

		$this->setOrderBy('changed',true);
		
		$this->setFields(array(
			'date_changed'=>array(
				'label'=>"Changed",
				'function'=>'getDateChanged',
			),
			'action'=>array(
				'label'=>'Action',
				'function'=>'getAction',
			),
			'title'=>array(
				'label'=>"List - Item",
				'function'=>'getObjectTitle',
			),
		));

		$this->setTitle("Core text modifications");

		//$this->addItemTypes("item","list");
		
	}
	
	function getDateChanged ($row) {
		return $this->getView()->date($row['changed'],'condensed');
	}
	
	function getObjectTitle ($row) {

		$list = new List8D_Model_List();
		$list = $list->getById($row['list_id']);

		$item = new List8D_Model_Item();
		$item = $item->getById($row['item_id']);

		$return = "<a href='{$this->getView()->url(array('controller'=>'list','action'=>'view','id'=>$item->getTrunkId()))}#item_{$item->getId()}'>{$list->getTrunk()->getDataValue("code")} - {$list->getTrunk()->getDataValue("title")}</a>";
		$return .= " - <a href='{$this->getView()->url(array('controller'=>'item','action'=>'view','id'=>$item->getId()))}'>{$item->getResource()->getTitle()}</a>";
		
		return $return;

	}
	
	function getChangedTo ($row) {
		
		$return = unserialize($row['to']);
		return ($return ? "added" : "removed");
	}
	
	function getAction ($row) {
		$return = "";
		if (!((bool)unserialize($row['from'])) && unserialize($row['to'])) {
			return "<span class='added status'>added</span>";
		} elseif (unserialize($row['from']) && !((bool)unserialize($row['to']))) {
			return "<span class='removed status'>removed</span>";
		} else  {
			return "<span class='changed status'>changed</span>";
		}
	}

}