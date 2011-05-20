<?php 

class List8D_Model_TagMapper {
	
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
			$this->setDbTable('List8D_Model_DbTable_Tag');
		}
		return $this->_dbTable;
	}

	public function log($data=array()) {
		$log = new List8D_Model_Log();
		$log->save($data);
	}

	public function save(List8D_Model_Tag $tag) {
		$data = array(
			'parent_id' => $tag->getParentId(),
			'namespace' => $tag->getNamespace(),
			'tagname' => $tag->getTagName(),
			'immutable' => ($tag->isImmutable() ? 1 : 0),
			'updated' => date('Y-m-d H:i:s'),
		);

		if (null === $tag->getId()) {
			// Check if namespace:tag exists - fetch data if it does
			$select = $this->getDbTable()->select();
			$select->where('tagname = ?', $tag->getTagName())
				->where('namespace = ?', $tag->getNamespace());
			$row = $this->getDbTable()->fetchRow($select);

			if (!empty($row['id'])) {
				$tag->setId($row->id)
				->setParentId($row->parent_id)
				->setImmutable(($row->immutable == 1 ? true : false));
			}
		}

		if (null === ($id = $tag->getId())) {
			$this->getDbTable()->insert($data);
			// get the id of the thing just added
			$tag->setId($this->getDbTable()->getAdapter()->lastInsertId());
			
			// log the insert
			$tag->log(array('action'=>'insert', 'table'=>$this->getDbTable()->info('name'), 'id'=>$tag->getId()));
			
		} else {
		
			// we have to do a select first to find out what the current values are for logging (see below)
			$existingData = $this->getDbTable()->fetchAll($this->getDbTable()->select()->where( 'id = ?', $id));

			$this->getDbTable()->update($data, array('id = ?' => $id));
			
			// log changes
			// go through every piece of data for the object and if it's changed, make a separate log entry for it
			foreach ($data as $key=>$value) {
				if ($data[$key] != $existingData[0][$key]) {
					$tag->log(array('action'=>'update', 'table'=>$this->getDbTable()->info('name'), 'id'=>$id, 'column'=>$key, 'value_from'=>$existingData[0][$key], 'value_to'=>$data[$key]));
				}
			}
			
			
		}
	}
	
	public function find($id, List8D_Model_Tag $tag) {
		$result = $this->getDbTable()->find($id);
		if (0 == count($result)) {
			return false;
		}
		$row = $result->current();
		$tag->setId($row->id)
			->setParentId($row->parent_id)
			->setTagName($row->tagname)
			->setNamespace($row->namespace)
			->setImmutable(($row->immutable == 1 ? true : false));
		return true;
	}
	
	public function findTag($namespace,$tag, List8D_Model_Tag $root) {
		$query = $this->getDbTable()->select()->where("namespace='$namespace'")->where("tagname='$tag'");
		$result = $this->getDbTable()->fetchAll($query);
		if (0 == count($result)) {
			return false;
		}
		$row = $result->current();
		$root->setId($row->id)
			->setParentId($row->parent_id)
			->setTagName($row->tagname)
			->setNamespace($row->namespace)
			->setImmutable(($row->immutable == 1 ? true : false));
		return true;
	}
	
	public function fetchAll($namespace = null) {
		$select = $this->getDbTable()->select();
		if ($namespace) {
			$select->where("namespace = ?",$namespace);
		}

		$resultSet = $this->getDbTable()->fetchAll($select);
		$entries   = array();
		foreach ($resultSet as $row) {
			$entry = new List8D_Model_Tag();
			$entry->setId($row->id)
			->setParentId($row->parent_id)
			->setTagName($row->tagname)
			->setNamespace($row->namespace)
			->setMapper($this)
			->setImmutable(($row->immutable == 1 ? true : false));

			$entries[] = $entry;
		}
		return $entries;
	}
	
	public function fetchByNamespace($namespace) {
		return $this->fetchAll($namespace);
	}
	
	public function fetchByParent($parent_id = null) {
		$select = $this->getDbTable()->select();
		
		if ($parent_id == null) {
			$select->where('parent_id is null');
		} else {
			$select->where('parent_id = ?', $parent_id);
		}

		$rows = $this->getDbTable()->fetchAll($select);
		
		$tags = array();
		foreach ($rows as $row) {
			$t = new List8D_Model_Tag;
			$t->find($row['id']);
			$tags[] = $t;
		}
		return $tags;
	}

	public function getNamespaces() {
		
		$select = $this->getDbTable()->select()->from('tag',array('namespace'))->group('namespace');
		
		$rows = $this->getDbTable()->fetchAll($select);

		$return = array();
		foreach($rows as $row) {
			$return[] = $row['namespace'];
		}
		
		return $return;	
		
	}
	
	public function getTagsWithListCount($namespace, $lists=false, $limit=0) {
		
		if ($limit) {
			$limit = "LIMIT $limit";
		} else {
			$limit = "";
		}
		
		// we need to do this the old fashion way (ie with out pdo)
		// because of bugs in pdo that cause problems with the "uber query"
		global $application;
		$options = $application->getOptions();

		$link = mysql_connect($options['resources']['db']['params']['host'], $options['resources']['db']['params']['username'], $options['resources']['db']['params']['password']);
		if (!$link) {
    	throw new Zend_Db_Exception('Could not connect to MySQL database: ' . mysql_error(),mysql_errno());
		}
		if (!mysql_select_db($options['resources']['db']['params']['dbname'],$link)) 
			throw new Zend_Db_Exception("Could not select database {$options['resources']['db']['params']['dbname']}: " . mysql_error(),mysql_errno());
		
		$result = mysql_query("DROP PROCEDURE IF EXISTS sub_tree");
		if (!$result) {
    	throw new Zend_Db_Statement_Exception('Invalid query: ' . mysql_error(),mysql_errno());
		}
		$result = mysql_query("CREATE PROCEDURE sub_tree()
BEGIN
  DROP TABLE IF EXISTS sub_tree;
  CREATE TEMPORARY TABLE sub_tree
    SELECT id, id as parent_id, tagname
    FROM tag
    WHERE namespace = '{$namespace}';
  ALTER TABLE sub_tree ADD PRIMARY KEY(id,parent_id);
  REPEAT
  	DROP TABLE IF EXISTS sub_tree2;
  	CREATE TEMPORARY TABLE sub_tree2
  		SELECT * FROM sub_tree;
    INSERT IGNORE INTO sub_tree
  	  SELECT f.id, s.`parent_id`, s.tagname
      FROM tag AS f
      JOIN sub_tree2 AS s ON f.parent_id = s.id;
  UNTIL Row_Count() = 0 END REPEAT; 
END");	
		if (!$result) {
    	throw new Zend_Db_Statement_Exception('Invalid query: ' . mysql_error(),mysql_errno());
		}
		$result = mysql_query("CALL sub_tree()");
		if (!$result) {
    	throw new Zend_Db_Statement_Exception('Invalid query: ' . mysql_error(),mysql_errno());
		}
		
		if ($lists)
			$lists ="WHERE l.list_id IN (".implode(",",$lists).")";
		else 
			$lists = "";
//echo 			"SELECT b.tagname, b.parent_id, count(*) as count FROM (SELECT tagname, t.parent_id FROM tagmap as l JOIN sub_tree AS t ON l.tag_id = t.id $lists GROUP BY l.list_id) AS b GROUP BY b.parent_id ORDER BY count DESC {$limit}";exit;
		$result = mysql_query("SELECT b.tagname, b.parent_id, count(*) as count FROM (SELECT tagname, t.parent_id FROM tagmap as l JOIN sub_tree AS t ON l.tag_id = t.id $lists GROUP BY l.list_id) AS b GROUP BY b.parent_id ORDER BY count DESC {$limit}",$link);
		if (!$result) {
    	throw new Zend_Db_Statement_Exception('Invalid query: ' . mysql_error(),mysql_errno());
		}
		
		$return = array();
		while ($data = mysql_fetch_assoc($result)) {
			$return[$data['parent_id']] = array('tagname'=>$data['tagname'],'count'=>$data['count']);
		}
		mysql_close($link);
		
		return $return;
		
	}
	
	public function getListIdsWithChildTagOf($id) {
		
		// we need to do this the old fashion way (ie with out pdo)
		// because of bugs in pdo that cause problems with the "uber query"
		global $application;
		$options = $application->getOptions();

		$link = mysql_connect($options['resources']['db']['params']['host'], $options['resources']['db']['params']['username'], $options['resources']['db']['params']['password']);
		if (!$link) {
    	throw new Zend_Db_Exception('Could not connect to MySQL database: ' . mysql_error());
		}
		if (!mysql_select_db($options['resources']['db']['params']['dbname'],$link)) 
			throw new Zend_Db_Exception("Could not select database {$options['resources']['db']['params']['dbname']}: " . mysql_error(),mysql_errno());
		
		$result = mysql_query("DROP PROCEDURE IF EXISTS sub_tree");
		if (!$result) {
    	throw new Zend_Db_Statement_Exception('Invalid query: ' . mysql_error(),mysql_errno());
		}
		$result = mysql_query("CREATE PROCEDURE sub_tree()
BEGIN
  DROP TABLE IF EXISTS sub_tree;
  CREATE TEMPORARY TABLE sub_tree
    SELECT id, id as parent_id, tagname
    FROM tag
    WHERE id = {$id};
  ALTER TABLE sub_tree ADD PRIMARY KEY(id,parent_id);
  REPEAT
  	DROP TABLE IF EXISTS sub_tree2;
  	CREATE TEMPORARY TABLE sub_tree2
  		SELECT * FROM sub_tree;
    INSERT IGNORE INTO sub_tree
  	  SELECT f.id, s.`parent_id`, s.tagname
      FROM tag AS f
      JOIN sub_tree2 AS s ON f.parent_id = s.id;
  UNTIL Row_Count() = 0 END REPEAT; 
END");	
		if (!$result) {
    	throw new Zend_Db_Statement_Exception('Invalid query: ' . mysql_error(),mysql_errno());
		}
		$result = mysql_query("CALL sub_tree()");
		if (!$result) {
    	throw new Zend_Db_Statement_Exception('Invalid query: ' . mysql_error(),mysql_errno());
		}
		$result = mysql_query("SELECT list_id FROM tagmap JOIN sub_tree ON tagmap.tag_id = sub_tree.id",$link);
		if (!$result) {
    	throw new Zend_Db_Statement_Exception('Invalid query: ' . mysql_error(),mysql_errno());
		}
		
		$return = array();
		while ($data = mysql_fetch_assoc($result)) {
			$return[] = $data['list_id'];
		}
		mysql_close($link);
		
		return $return;

		
	}
}
