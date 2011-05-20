<?php
class List8D_Model_UserMap {

 	protected $_mapper;
	protected $_user_id;
	protected $_tag_id;

	function getTags($user) {
		if ($user instanceof List8D_Model_User) {
			$user = $user->getId();
		}
		$ids = $this->getMapper()->getTagIdsByUserId($user);
		$tags = array();
		foreach ((array) $ids as $id) {
			$tmp = new List8D_Model_Tag;
			$tags[] = $tmp->find($id);
		}
		return $tags;
	}
	function getUsers($tag) {
		if ($tag instanceof List8D_Model_Tag) {
			$tag = $tag->getId();
		}
		$ids = $this->getMapper()->getUserIdsByTagId($tag);

		$users = array();
		foreach ((array) $ids as $id) {
			$tmp = new List8D_Model_User;
			$users[] = $tmp->find($id);
		}
		return $users;
	}

	public function findUserMap($tag, $user) {
		if ($tag instanceof List8D_Model_Tag) {
			$tag = $tag->getId();
		}
		if ($user instanceof List8D_Model_User) {
			$user = $user->getId();
		}
		return $this->getMapper()->findUserMap($tag, $user);
	}

	function getId(){
		if (isset($this->_id))
		  return $this->_id;
	}
	function setId($value){
		$this->_id=$value;
		return $this;
	}

	function getUserId(){
		  return $this->_user_id;
	}
	function setUserId($value){
		$this->_user_id=$value;
		return $this;
	}
	function getUser() {
		$t = new List8D_Model_User;
		$t = $t->find($this->_user_id);
		return $t;
	}

	
	function getTagId(){
		  return $this->_tag_id;
	}
	function setTagId($value){
		$this->_tag_id=$value;
		return $this;
	}
	function getTag() {
		$t = new List8D_Model_Tag;
		$t = $t->find($this->_tag_id);
		return $t;
	}

	
 	function setMapper($mapper) {
 	    $this->_mapper = $mapper;
 	    return $this;
 	}
 	
 	public function getMapper() {
 	    if (null === $this->_mapper) {
 	        $this->setMapper(new List8D_Model_UserMapMapper());
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
 	    $this->getMapper()->find($id, $this);
 	    return $this;
 	}
 	
 	public function fetchAll() {
 	    return $this->getMapper()->fetchAll();
 	}

}
