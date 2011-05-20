<?php 

class List8D_Model_UserMapMapper {
	
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
			$this->setDbTable('List8D_Model_DbTable_UserMap');
		}
		return $this->_dbTable;
	}

	public function getTagIdsByUserId($user_id) {
		$result = $this->getDbTable()->fetchAll(
			$this->getDbTable()
			->select()
			->where("`user_id` = ?", $user_id)
		);

		$tagIds = array();
		foreach($result as $row){
			$tagIds[] = $row->tag_id;
		}
		return $tagIds;
	}
	public function getUserIdsByTagId($tag_id) {
		$result = $this->getDbTable()->fetchAll(
			$this->getDbTable()
			->select()
			->where("`tag_id` = ?", $tag_id)
		);
		
		$userIds = array();
		foreach($result as $row){
			$userIds[] = $row->user_id;
		}
		return $userIds;
	}

	public function findUserMap($tag_id, $user_id) {
		$select = $this->getDbTable()->select();
		$select->where('tag_id = ?', $tag_id)
			->where('user_id = ?',  $user_id);
		$row = $this->getDbTable()->fetchRow($select);

		if (!empty($row->id)) {
			$um = new List8D_Model_UserMap;
			$um = $um->find($row->id);
			return $um;
		}
		return null;
	}

	public function save(List8D_Model_UserMap $tagmap) {
		$data = array(
			'user_id' => $tagmap->getUserId(),
			'tag_id' => $tagmap->getTagId(),
		);

		if (null === ($id = $tagmap->getId())) {
			$this->getDbTable()->insert($data);
		} else {
			$this->getDbTable()->update($data, array('id = ?' => $id));
		}
		
		$tagmap->setId($this->getDbTable()->getAdapter()->lastInsertId() );
		
	}
	
	public function find($id, List8D_Model_UserMap $usermap) {
		$result = $this->getDbTable()->find($id);
		if (0 == count($result)) {
			return;
		}
		$row = $result->current();
		$usermap->setId($row->id)
			->setUserId($row->user_id)
			->setTagId($row->tag_id);
		return $usermap;
	}
	
	public function fetchAll() {
		$resultSet = $this->getDbTable()->fetchAll();
		$entries   = array();
		foreach ($resultSet as $row) {
			$entry = new List8D_Model_TagMap();
			$entry->setId($row->id)
				->setUserId($row->user_id)
				->setTagId($row->tag_id);

			$entries[] = $entry;
		}
		return $entries;
	}
	public function delete(List8D_Model_UserMap $usermap) {
		$this->getDbTable()->delete(array('id = ?' => $usermap->getId()));
	}
	
}
