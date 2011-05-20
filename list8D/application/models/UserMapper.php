<?php 

class List8D_Model_UserMapper {
	
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
			$this->setDbTable('List8D_Model_DbTable_User');
		}
		return $this->_dbTable;
	}

	/**
	 * Get lists for the current user.
	 *
	 * @param List8D_Model_User
	 * @return array of List8D_Model_List
	 */
	function getMyLists(List8D_Model_User $user) {
		$ret = array();

		if ($user->getId() !== null) {
			
			$userTags = $this->getTags($user,'none');

			$tag = new List8D_Model_TagMap;
			$tagDbTable = $tag->getMapper()->getDbTable();
			
			$tagIds = array_keys($user->getTags('none'));
			
			if (count($tagIds)) {
				$result = $tagDbTable->fetchAll($tagDbTable->select('list_id')
					->where("`tag_id` in (".implode(',',$tagIds).")")
				);
				$lists = array();
				
				foreach($result as $row) {
					
					$list = new List8D_Model_List();
					$lists[$row->list_id] = $list->getById($row->list_id);
					
				}				
			} else {
				$lists = array();
			}
			
			return $lists;
			
			
		}
		
		return $ret;
	}

	public function getTags($user,$direction='none') {
		
		// Get tags for user
		$um = new List8D_Model_UserMap;
		$umDbTable = $um->getMapper()->getDbTable();

		$tag = new List8D_Model_Tag;
		$tagDbTable = $tag->getMapper()->getDbTable();
		
		$result = $umDbTable->fetchAll(
		  $umDbTable->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
		  ->setIntegrityCheck(false)
		  ->where("`user_id` = ?", $user->getId())
		  ->join('tag', 'usermap.tag_id = tag.id')
		);

		$tagCheck = array();
		$tagCache = array();
		foreach($result as $row){
		  $tagCheck[$row->id] = $row;
		}
		// while tags, get child tags
		// The $tagCheck array will grow as we discover more
		// children, but only if they're not in the cache, until
		// we run out of tags to look at. Eventually $tagCache
		// should simply contain the tags we actually care about.
		while (!empty($tagCheck)) {
			
			// get the tag to check 
		  $row = array_pop($tagCheck);
		  
		  // check cache
		  if (empty($tagCache[$row->id])) {
		  	
		  	$query = $tagDbTable->select();
		  	
		  	
		  	if ($direction=='none') {
		  		// direction is none so we dont need to do anything
		  	} elseif ($direction == 'up') {
		  		// direction is up so we need to get tags parent
		  		$query->where("`id` = ?",$row->parent_id);
		  	} elseif ($direction == 'down') {
		  		// direction is down so we need to get tags children
		  		$query->where("`parent_id` = ?",$row->id);
		  	} elseif ($direction == 'both') {
		  		// direction is both so we need to get tags children and parent
		  		$query->where("`parent_id` = ?",$row->id)->orWhere("`id` = ?",$row->parent_id);
		  	}
		  	if ($direction!='none') {
		  	
		  		$result = $tagDbTable->fetchAll($query);
				
		  		foreach($result as $row2) {
		  			$tagCheck[$row2->id] = $row2;
			  	}
				}
					  	
				// store in cache
		  	$tagCache[$row->id] = $row->namespace;
		  	
		  }
			
		}
		
		return $tagCache;
		
	}
	
	public function save(List8D_Model_User $user) {
		$data = array(
			'id' => $user->getId(),
			'login' => $user->getLogin(),
			'email' => $user->getEmail(),
			'displayname' => $user->getDisplayName(),
			'role' => $user->getRole(),
			'institutionid' => $user->getInstitutionId(),
			'updated' => date('Y-m-d H:i:s'),
		);

		if (null === ($id = $user->getId())) {
			$data['created'] = $data['updated'];
			$this->getDbTable()->insert($data);
			$user->setId( $this->getDbTable()->getAdapter()->lastInsertId() );
			
		} else {
			$this->getDbTable()->update($data, array('id = ?' => $id));
		}
	}
	
	public function find($id, List8D_Model_User $user) {
		$result = $this->getDbTable()->find($id);
		if (0 == count($result)) {
			return false;
		}
		$row = $result->current();
		$user->setId($row->id)
			->setLogin($row->login)
			->setEmail($row->email)
			->setDisplayName($row->displayname)
			->setRole($row->role)
			->setInstitutionId($row->institutionid);
		return true;
	}

	public function findByLogin($login, List8D_Model_User $user) {

		$select = $this->getDbTable()->select();
		$select->where('login = ?', $login);
		$row = $this->getDbTable()->fetchRow($select);

		if (empty($row['id'])) {
			return null;
		}

		$user->setId($row->id)
			->setLogin($row->login)
			->setEmail($row->email)
			->setDisplayName($row->displayname)
			->setRole($row->role)
			->setInstitutionId($row->institutionid);
	}
	
	public function fetchAll() {
		return $this->fetch();
	}
	
	public function fetch($limit=0, $offset=0) {
		$query = $this->getDbTable()->select();
		if ($limit) 
			$query->limit($limit,$offset);
		$resultSet = $this->getDbTable()->fetchAll($query);
		$entries   = array();
		foreach ($resultSet as $row) {
			$entry = new List8D_Model_User();
			$entry->setId($row->id)
				->setLogin($row->login)
				->setEmail($row->email)
				->setDisplayName($row->displayname)
				->setRole($row->role)
				->setInstitutionId($row->institutionid);

			$entries[] = $entry;
		}
		return $entries;
	}
	
	public function delete(List8D_Model_User $user) {
		$this->getDbTable()->delete(array('id = ?' => $user->getId()));

	}
}
