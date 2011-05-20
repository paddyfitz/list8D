<?php
class List8D_Model_User {

 	protected $_mapper;

	protected $_id;
	protected $_login;
	protected $_email;
	protected $_displayName;
	protected $_role_id;
	protected $_instId;
	protected $_acl = null;
	protected $_tagCache = array();

	/**
	 * Return current web user. Might be abstracted further in future.
	 *
	 * @return List8D_Model_User
	 */
	function getCurrentUser() {
		if (isset($_SERVER['REMOTE_USER']) && $_SERVER['REMOTE_USER'] != "") {
			$this->findByLogin($_SERVER['REMOTE_USER']);
		} else {
			$this->setRole('guest');
			$this->setDisplayName('Guest');
		}		
		return $this;
	}

	/**
	 * Get lists for the current user.
	 *
	 * @return array of List8D_Model_List
	 */
	function getMyLists() {
		if ($this->_id !== null) {
			return $this->getMapper()->getMyLists($this);
		} else {
			return null;
		}
	}
	
	function getTags($direction='none') {
		
		if (!isset($this->_tagCache[$direction])) {
			$this->_tagCache[$direction] = array();
			foreach($this->getMapper()->getTags($this,$direction) as $id => $namespace) {
				$this->_tagCache[$direction][$id] = new List8D_Model_Tag();
				$this->_tagCache[$direction][$id]->find($id);	
			}
			
		} 
		
		return $this->_tagCache[$direction];

	}

	function getId() {
		  return $this->_id;
	}
	
	function setId($value) {
		$this->_id=$value;
		return $this;
	}
	
	function getLogin() {
		return $this->_login;
	}
	function setLogin($v) {
		$this->_login = $v;
		return $this;
	}
	function getDisplayName() {
		return $this->_displayName;
	}
	function setDisplayName($name) {
		$this->_displayName=$name;
		return $this;
	}
	function getEmail() {
		return $this->_email;
	}
	function setEmail($email) {
		$this->_email=$email;
		return $this;
	}

	function getRole() {
		return $this->_role_id;
	}
	function setRole($roleId) {
		$this->_role_id=$roleId;
		return $this;
	}
	

	function getInstitutionId() {
		return $this->_instId;
	}
	function setInstitutionId($instId) {
		$this->_instId=$instId;
		return $this;
	}


 	function setMapper($mapper)
 	{
 	    $this->_mapper = $mapper;
 	    return $this;
 	}
 	
 	public function getMapper()
 	{ 
 	    if (null === $this->_mapper) {
 	        $this->setMapper(new List8D_Model_UserMapper());
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
 	    if ($this->getMapper()->find($id, $this))
	 	    return $this;
	 	  else 
	 	  	return false;
 	}
 	
 	public function getById($id) {
 		return $this->find($id);
 	}

	public function findByLogin($login) {
		$this->getMapper()->findByLogin($login, $this);
		$login = $this->getLogin();
		if (empty($login))
			return false;
		return $this;
	}
 	
 	public function fetchAll() {
 	    return $this->getMapper()->fetchAll();
 	}
 	
 	public function fetch($limit=0,$offset=0) {
 	    return $this->getMapper()->fetch($limit,$offset);
 	}
	
	public function getRecentLists() {
		$db = Zend_Registry::get('dbResource');

	  if ($this->getId()) {
			$lists = $db->getDbAdapter()->query("SELECT list_id FROM `recent_lists` WHERE user_id = {$this->getId()} ORDER BY created DESC LIMIT 10");
			$return = array();
			foreach($lists as $list) {
				$return[$list['list_id']] = new List8D_Model_List();
				$return[$list['list_id']] = $return[$list['list_id']]->getById($list['list_id']);
			}
			return $return;
		} else {
			return array();
		}
		
	} 	
	
	public function getAcl() {
		if (!isset($this->_acl)) {
			$this->_acl = new List8D_Acl();
		}
		return $this->_acl;
	}
	
	public function isAllowed($resource,$action) {
		
		// if resource is an object we may need a new acl that is passed the object
		if (is_object($resource)) {
			$acl = new List8D_Acl($this,$resource);
			$user = $this;			
		} 
		// if resource is a string we can do a simple isAllowed
		else {
			$acl = $this->getAcl();
			$user = $this->getRole();
		}
		
		return $acl->isAllowed($user,$resource,$action);
		
	}
	
	
}
