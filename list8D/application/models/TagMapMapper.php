<?php 

class List8D_Model_TagMapMapper {
	
	protected $_dbTable;
	
	public function setDbTable($dbTable) {
		if (is_string($dbTable)) {
			$dbTable = new $dbTable();
		}
		if (!$dbTable instanceof Zend_Db_Table_Abstract) {
			throw new Exception('Invalid table data gateway provided');
		}
		$this->_dbTable = $dbTable;
		return $this;
	}

	public function getDbTable() {
		if (null === $this->_dbTable) {
			$this->setDbTable('List8D_Model_DbTable_TagMap');
		}
		return $this->_dbTable;
	}

	public function getTagIdsByListId($list_id) {
		$result = $this->getDbTable()->fetchAll(
			$this->getDbTable()
			->select()
			->where("`list_id` = ?", $list_id)
		);

		$tagIds = array();
		foreach($result as $row){
			$tagIds[] = $row->tag_id;
		}
		return $tagIds;
	}
	public function getListIdsByTagId($tag_id) {
		$result = $this->getDbTable()->fetchAll(
			$this->getDbTable()
			->select()
			->where("`tag_id` = ?", $tag_id)
		);
		
		$listIds = array();
		foreach($result as $row){
			$listIds[] = $row->list_id;
		}
		return $listIds;
	}
	
	public function log($data=array()) {
		$log = new List8D_Model_Log();
		$log->save($data);
	}

	public function findTagMap($tag_id, $list_id) {
		$select = $this->getDbTable()->select();
		$select->where('tag_id = ?', $tag_id)
			->where('list_id = ?',  $list_id);
		$row = $this->getDbTable()->fetchRow($select);

		if (!empty($row->id)) {
			$tm = new List8D_Model_TagMap;
			$tm = $tm->find($row->id);
			return $tm;
		}
		return null;
	}

	public function save(List8D_Model_TagMap $tagmap) {
		$data = array(
			'list_id' => $tagmap->getListId(),
			'tag_id' => $tagmap->getTagId(),
		);

		$select = $this->getDbTable()->select();
		$select->where('tag_id = ?', $tagmap->getTagId())
			->where('list_id = ?',  $tagmap->getListId());
		$row = $this->getDbTable()->fetchRow($select);

		if (!empty($row->id)) {
			$tagmap->setId($row->id);
		}


		if (null === ($id = $tagmap->getId())) {
			$this->getDbTable()->insert($data);
			
			// get the id of the thing just added
			$tagmap->setId($this->getDbTable()->getAdapter()->lastInsertId());

			// log the insert
			$this->log(array('action'=>'insert', 'table'=>$this->getDbTable()->info('name'), 'id'=>$tagmap->getId()));
			
		} else {
		
			// we have to do a select first to find out what the current values are for logging (see below)
			$existingData = $this->getDbTable()->fetchAll($this->getDbTable()->select()->where( 'id = ?', $id));
			
			$this->getDbTable()->update($data, array('id = ?' => $id));
			
			// log changes
			// go through every piece of data for the object and if it's changed, make a separate log entry for it
			foreach ($data as $key=>$value) {
				if ($data[$key] != $existingData[0][$key]) {
					$this->log(array('action'=>'update', 'table'=>$this->getDbTable()->info('name'), 'id'=>$id, 'column'=>$key, 'value_from'=>$existingData[0][$key], 'value_to'=>$data[$key]));
				}
			}
			
			
		}
	}
	
	public function find($id, List8D_Model_TagMap $tagmap) {
		$result = $this->getDbTable()->find($id);
		if (0 == count($result)) {
			return;
		}
		$row = $result->current();
		$tagmap->setId($row->id)
			->setListId($row->list_id)
			->setTagId($row->tag_id);
		return $tagmap;
	}
	
	public function fetchAll() {
		$resultSet = $this->getDbTable()->fetchAll();
		$entries   = array();
		foreach ($resultSet as $row) {
			$entry = new List8D_Model_TagMap();
			$entry->setId($row->id)
				->setListId($row->list_id)
				->setTagId($row->tag_id);

			$entries[] = $entry;
		}
		return $entries;
	}
	public function delete(List8D_Model_TagMap $tagmap) {
		$this->getDbTable()->delete(array('id = ?' => $tagmap->getId()));
	}
}
