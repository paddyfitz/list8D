<?php
class List8D_Model_Tag {

 	protected $_mapper;

	protected $_id;
	protected $_parent_id;
	protected $_namespace;
	protected $_tagname;
	protected $_immutable;

	function getId(){
		  return $this->_id;
	}
	
	public function log($data=array()) {
		$this->getMapper()->log($data);
	}
	
	function setId($value){
		$this->_id=$value;
		return $this;
	}
	
	function isImmutable() {
		return $this->_immutable;
	}
	function setImmutable($v) {
		if (($v === true) or ($v === false)) {
			$this->_immutable = $v;
		} 
		return $this;
	}
	function getTagName() {
		return $this->_tagname;
	}
	function setTagName($tagname) {
		$this->_tagname=$tagname;
		return $this;
	}

	function getNamespace() {
		return $this->_namespace;
	}
	function setNamespace($ns) {
		$this->_namespace=$ns;
		return $this;
	}
	
	function getNamespaces() {
		return $this->getMapper()->getNamespaces();
	}

	function getParentId() {
		return $this->_parent_id;
	}

	// Check that we're not creating a loop
	function checkValidParent($tag, $parent_id) {
		// No parent
		if ($parent_id == 0) {
			return true;
		}

		$t = new List8D_Model_Tag;
		$t = $t->find($parent_id);
		$seen = array();
		$seen[$tag->getId()] = 1;
		while ($t !== null && $t !== false) {
			if (isset($seen[$t->getId()]) && $seen[$t->getId()] == 1) {
				return false;
			}
			$seen[$t->getId()] = 1;
			$t = $t->getParent();
		}
		return true;
	}

	function setParentId($parent_id) {

		// Loop check
		if ($this->checkValidParent($this, $parent_id) == false) {
			return $this;
		}

		if ($parent_id == $this->_id) {
			return $this;
		}

		if (($parent_id == '') || ($parent_id == 0))
			$parent_id=null;

		$this->_parent_id=$parent_id;
		return $this;
	}

	function getParent() {
		if (null !== $this->_parent_id) {
			$parent = new List8D_Model_Tag();
			$parent = $parent->find($this->_parent_id);
			return $parent;
		} else {
			return null;
		}
	}

	function getLists(){
		$t = new List8D_Model_TagMap;
		return $t->getLists($this->_id);
	}

	function getUsers() {
		$userMap = new List8D_Model_UserMap;
		return $userMap->getUsers($this->_id);
	}


 	function setMapper($mapper)
 	{
 	    $this->_mapper = $mapper;
 	    return $this;
 	}
 	
 	public function getMapper()
 	{ 
 	    if (null === $this->_mapper) {
 	        $this->setMapper(new List8D_Model_TagMapper());
 	    }
 	    return $this->_mapper;
 	}
 	
 	public function save() {
 	    $this->getMapper()->save($this);
 	}

	function delete() {
		$this->getMapper()->delete($this);
	}
 	
 	public function find($id) {
 	    if($this->getMapper()->find($id, $this))
 	    	return $this;
 	    else
 	    	return false;
 	}
 	
 	public function findTag($namespace,$tag) {
 		
 		if ($this->getMapper()->findTag($namespace,$tag,$this))
 			return $this;
 		else
 			return false;
 		
 	}
 	
 	public function fetchAll() {
 	    return $this->getMapper()->fetchAll();
 	}
	
	public function fetchByNamespace($namespace) {
	    return $this->getMapper()->fetchByNamespace($namespace);
	}
	
	public function getChildren() {
		return $this->getMapper()->fetchByParent($this->getId());
	}
	
	public function __toString() {
		return $this->_namespace.": ".$this->_tagname;
	}
	
	public function getRoots() {
		return $this->getMapper()->fetchByParent(null);
	}
	
	public function getTagsWithListCount($namespace,$lists=false, $limit=0) {
		
		return $this->getMapper()->getTagsWithListCount($namespace, $lists, $limit);
		
	}
	
	public function getListIdsWithChildTagOf($id) {
		return $this->getMapper()->getListIdsWithChildTagOf($id);
	}
	
}
