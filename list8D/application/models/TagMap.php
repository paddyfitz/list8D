<?php
	function tagcmp ($a, $b) {
		$ta = $a->getNameSpace() . ':' . $a->getTagname();
		$tb = $b->getNameSpace() . ':' . $b->getTagname();
		return strcmp($ta, $tb);
	}

class List8D_Model_TagMap {

	protected $_mapper;
	protected $_list_id;
	protected $_tag_id;

	function getTags($list) {
		if ($list instanceof List8D_Model_List) {
			$list = $list->getId();
		}
		$ids = $this->getMapper()->getTagIdsByListId($list);
		$tags = array();
		foreach ((array) $ids as $id) {
			$tmp = new List8D_Model_Tag;
			$tags[] = $tmp->find($id);
		}
		usort($tags, "tagcmp");
		return $tags;
	}



	function getAllTags($list) {
		if ($list instanceof List8D_Model_List) {
			$list = $list->getId();
		}
		$ids = $this->getMapper()->getTagIdsByListId($list);

		// Messy, but should be fast...
		$tagholder = array();

		foreach ((array) $ids as $id) {
			$tmp = new List8D_Model_Tag;
			$tagholder[$id] = $tmp->find($id);

			$t = $tmp->getParent();
			while ($t !== null) {
				$tagholder[$t->getId()] = clone($t);
				$t = $t->getParent();
			}
		}
		usort($tagholder, "tagcmp");
		return $tagholder;
	}

	function getLists($tag) {
		if ($tag instanceof List8D_Model_Tag) {
			$tag = $tag->getId();
		}
		$ids = $this->getMapper()->getListIdsByTagId($tag);

		$lists = array();
		foreach ((array) $ids as $id) {
			$tmp = new List8D_Model_List;
			$lists[] = $tmp->getById($id);
		}
		return $lists;
	}

	function getId() {
	
		if (isset($this->_id))
		  return $this->_id;

	}
	function setId($value) {
		$this->_id=$value;
		return $this;
	}

	function getListId() {
		return $this->_list_id;
	}
	function setListId($value) {
		$this->_list_id=$value;
		return $this;
	}
	function getList() {
		$l = new List8D_Model_List;
		$l = $l->getById($this->_list_id);
		return $l;
	}

	function getTagId() {
		return $this->_tag_id;
	}
	function setTagId($value) {
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
			$this->setMapper(new List8D_Model_TagMapMapper());
		}
		return $this->_mapper;
	}

	public function log($data=array()) {
		$this->getMapper()->log($data);
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

	public function findTagMap($tag, $list) {
		if ($tag instanceof List8D_Model_Tag) {
			$tag = $tag->getId();
		}
		if ($list instanceof List8D_Model_List) {
			$list = $list->getId();
		}
		return $this->getMapper()->findTagMap($tag, $list);
	}

}
