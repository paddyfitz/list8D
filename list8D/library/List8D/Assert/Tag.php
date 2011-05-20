<?php

class List8D_Assert_Tag implements Zend_Acl_Assert_Interface {
	
	protected $_user;
	protected $_resource;
	protected $_namespaces;
	
	public function __construct($user,$resource,$namespaces) {
		$this->_user = $user;
		$this->_resource = $resource;
		$this->_namespaces = $namespaces;
	}
	
	public function assert(Zend_Acl $acl,Zend_Acl_Role_Interface $role = null,Zend_Acl_Resource_Interface $resource = null,$privilege = null) {
		
		// we need to make sure this resource has been saved to the db ie has id
		// if it doesnt we cant check its tags

		if (!$this->_resource->getId()) 
			throw new Exception("Resource id was empty, a resource's tag not be checked with out it.",500);		
		
		if ($this->_resource instanceof List8D_Model_Item || $this->_resource instanceof List8D_Model_NestedList) {
			$this->_resource = $this->_resource->getTrunk();
		}
			
		// we will get tags on the mappers as the mappers return id and namespace rather than initiating tag objects
		// may be quicker for check permission of one list
		// but tags arent cached so may be slower for checking permissions on multiple lists
		$userTags = $this->_user->getMapper()->getTags($this->_user,'none');
		$listTags = $this->_resource->getMapper()->getTags($this->_resource,'up');
		
		

		foreach($userTags as $tag => $namespace) {
			
			if (isset($listTags[$tag]) && (array_search($namespace,$this->_namespaces)!==false || $this->_namespaces === null)) {
				
				return true;
				
			} 
			
		}
		return false;
		
	}
	
}