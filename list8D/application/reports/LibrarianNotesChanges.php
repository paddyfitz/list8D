<?php 

class List8D_Report_LibrarianNotesChanges extends List8D_Report {

	public function init() {

		$this->setQuery("SELECT item_data.key as item_key, list_data.key as list_key, change_log.changed, list_data.row_id as list_id, item_data.row_id as item_id, change_log.value_from, change_log.value_to
 FROM (SELECT MAX(id) as id FROM change_log WHERE (action = 'update' OR action = 'insert') AND (`table`='list_data' OR `table`='item_data') GROUP BY row_id) AS ids 
 LEFT JOIN change_log as change_log ON ids.id = change_log.id 
 LEFT JOIN list_data ON list_data.id = change_log.`row_id` AND `change_log`.`table` = 'list_data'
 LEFT JOIN item_data ON item_data.id = change_log.`row_id` AND `change_log`.`table` = 'item_data'
 WHERE (item_data.key='private_notes' OR list_data.key='private_notes') AND !((change_log.value_to='s:0:\"\";' OR change_log.value_to='') AND (change_log.value_from='' OR change_log.value_to=''))");

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
				'label'=>"List - Item, changed to",
				'function'=>'getObjectTitle',
			),
		));
		
		$this->addItemTypes("item","list");
		
	}
	
	function getDateChanged ($row) {
		return $this->getView()->date($row['changed'],'condensed');
	}
	
	function getObjectTitle ($row) {

		if (isset($row['list_id'])) {
			$item = new List8D_Model_List();
			$item = $item->getById($row['list_id']);
			$removedMessage = "list removed";
			$type = 'list';
		} elseif (isset($row['item_id'])) {
			$item = new List8D_Model_Item();
			$item = $item->getById($row['item_id']);
			$removedMessage = "item removed";
			$type = 'item';
		} 
		
		if($item) {
			if ($item instanceof List8D_Model_Item)
				$return = "<a href='{$this->getView()->url(array('controller'=>'list','action'=>'view','id'=>$item->getTrunkId()))}#item_{$item->getId()}'>{$item->getTrunk()->getDataValue("code")} - {$item->getResource()->getDataValue("title")}</a>";
			else
				$return = "<a href='{$this->getView()->url(array('controller'=>'list','action'=>'view','id'=>$item->getTrunkId()))}#{$type}_{$item->getId()}'>{$item->getDataValue("title")}</a>";
		}	else {
			$return = $removedMessage;
		}
		$value = unserialize($row['value_to']);
		if (!empty($value)) {
			$return .= "<br/>".$value;
		}
		
		
		return $return;
		
	}
	
	function getChangedTo ($row) {
		
		return s_unserialize($row['value_to']);
		
	}
	
	function getAction ($row) {
		$return = "";
		
		$value_from = s_unserialize($row['value_from']);
		$value_to = s_unserialize($row['value_to']);
		if (empty($value_from)) {
			return "<span class='added status'>added</span>";
		} elseif (empty($value_to)) {
			return "<span class='removed status'>removed</span>";
		} else  {
			return "<span class='changed status'>changed</span>";
		}
	}

}