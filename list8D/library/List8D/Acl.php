<?php

class List8D_Acl extends Zend_Acl {
	
	public function __construct($user=null,$resourceObject=null) {
		
				
		global $application;
    $settings = $application->getOptions();
    
		foreach($settings['access']['roles'] as $roleName => $role) {
				
			if (empty($role['inherits'])) {
				$this->addRole(new Zend_Acl_Role($roleName));
			} else {
				if (!$this->hasRole($roleName)) {
					$this->addRole(new Zend_Acl_Role($roleName),$role['inherits']);
				}
			}

			foreach($role as $resourceName => $resource) {
				
				if (!$this->has($resourceName))
					$this->addResource(new Zend_Acl_Resource($resourceName));
					
				if ($resourceName!='title' && $resourceName!='inherits') {
				
					if (!empty($resource['privileges'])) {

						foreach ($resource['privileges'] as $permissionName => $permission) {
						
								
						  $permissionSplit = explode(":",$permission);
						  $permission = $permissionSplit[0];
						  $assert = null;
						  if (isset($permissionSplit[1])) {
						  	if ($permission=='edit-tagged') {
						  		$assert = new List8D_Assert_Tag($user,$resourceObject,explode(",",$permissionSplit[1]));
						  	}
						  }
						  $this->allow($roleName,$resourceName,$permission,$assert);
						  
						}
					}
				}
			}
		}
		 
			
	}
	
	public function isAllowed($role = null, $resource = null, $privilege = null) {
		
		$origResource = $resource;
		if ($resource instanceof List8D_Model_List) {
			$resource = 'list';
		}  else if ($resource instanceof List8D_Model_User) {
			$resource = 'user';
		} else if ($resource instanceof List8D_Model_Resource) {
			$resource = 'resource';
		} else if ($resource instanceof List8D_Model_Item) {
			$origResource = $resource->getTrunk();
			$resource = 'list';
		}
				
		if (!$this->has($resource))
			$this->addResource(new Zend_Acl_Resource($resource));
		
		
		if ($role instanceof List8D_Model_User) {
			$role = $role->getRole();
		}
		
		if (empty($role))
			$role = 'guest';
		
		if (!$this->hasRole($role))		
			$this->addRole($role);
	
		if (parent::isAllowed($role,$resource,$privilege)) 
			return true;
			
		if (is_object($origResource) && $privilege=='edit' && ($resource=='list' || $resource=='user') && $this->isAllowed($role,$origResource,'edit-tagged'))
			return true;
			
		return false;
		
	}
	
	/*private $acl;
	private $currentUser;

	public function __construct() {
		$this->acl = new Zend_Acl();

		// Define Roles
		$this->acl->addRole( new Zend_Acl_Role('Academic'));
		$this->acl->addRole( new Zend_Acl_Role('Guest'));
		$this->acl->addRole( new Zend_Acl_Role('DeptAdmin'), 'Academic');
		$this->acl->addRole( new Zend_Acl_Role('LibraryStaff'), 'Academic');

		$this->currentUser = new List8D_Model_User();
		$this->currentUser->getCurrentUser();

		// we have a valid user
		if (null !== $this->currentUser->getId()) {

			// Grab user role
			$this->acl->addRole( new Zend_Acl_Role( $this->currentUser->getLogin() ), $this->currentUser->getRole()->getRoleName() );

		}

	}

	// Test to see if a user has a generic role - not against a specific resource
	private function isRole($roleName) {
		// Not a known user.
		if (null === $this->currentUser->getId()) {
			return false;
		}
		
		if (!$this->acl->hasRole('test')) {
			$this->acl->add(new Zend_Acl_Resource("test"));
			$this->acl->allow($roleName, "test");
		}
		return $this->acl->isAllowed($this->currentUser->getLogin(), "test");
	}

	// Inherits Academic (could have DA in future, too?) - all reporting and stock mgt.
	public function isLibraryStaff() {
		return $this->isRole("LibraryStaff");
	}

	// Inherits Academic - may get some extra reporting privs?
	public function isDeptAdmin() {
		return $this->isRole("DeptAdmin");
	}

	// Basic list editing priv - no stock mgt.
	public function isAcademic() {
		return $this->isRole("Academic");
	}

	// Check to see if the current user has permissions on a specific list
	public function checkListACL(List8D_Model_List $list) {

		// Not a known user.
		if (null === $this->currentUser->getId()) {
			return false;
		}

		// Add resources (tags)
		foreach ((array) $this->currentUser->getTags() as $tag) {
			$this->acl->add(new Zend_Acl_Resource($tag->getId()));
			$this->acl->allow('Academic', $tag->getId());
		}


		// TODO: test only 'parent' list?
		foreach((array) $list->getAllTags() as $tag) {
			//echo "Testing tag id " . $tag->getId();
			try {
				if($this->acl->isAllowed($this->currentUser->getLogin(), $tag->getId())) {
					return true;
				}
			} catch (Exception $e) {
				// Zend_ACL generates an exception when you call isAllowed on an
				// undefined resource, so we just consider that a soft fail
			}
		}

		// You're not on the list, not coming in.
		return false;
	}*/
		
}