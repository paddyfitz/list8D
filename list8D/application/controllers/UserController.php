<?php

class UserController extends List8D_Controller {

	public function init() {
		parent::init();
	}

	public function indexAction() {
	
		$user = new List8D_Model_User();
		$currentUser = $user->getCurrentUser();
		if (!$currentUser->isAllowed('user','view'))
			throw new Zend_Controller_Action_Exception('You do not have permission to edit list with id '.$params['id'].'.',403);
			
		$user = new List8D_Model_User();
		$this->layoutView->title = "Users";
    // set template name to some thing a little simplier
		$this->viewRenderer->setViewScriptPathSpec('users.:suffix');

		$limit = 50;
		
		if (isset($this->params['page'])) 
			$page = $this->params['page'];
		else
			$page = 1;
		
		$this->view->page = $page;
		$dbTable = $user->getMapper()->getDbTable();
		//! TODO a tonne of this stuff desparatly needs moving into the model
		if (isset($this->params['search_user_by_username_and_display_name'])) {
			$filter = $this->params['search_user_by_username_and_display_name'];
			$query = $dbTable->select()->where("login LIKE '%{$filter}%'")->orWhere("displayname LIKE '%{$filter}%'");
			$this->view->pages = ceil(count($dbTable->fetchAll($query))/$limit);
			$query->limit($limit,($page-1)*$limit);

			$resultSet = $dbTable->fetchAll($query);
			$entries  = array();
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
			$this->view->users = $entries;
		} else if (isset($this->params['filterusername'])) {
			
			$filter = $this->params['filterusername'];
			$this->view->filter = $filter;
			$query = $dbTable->select()->where("login REGEXP '^{$filter}'");
			$this->view->pages = ceil(count($dbTable->fetchAll($query))/$limit);
			$query->limit($limit,($page-1)*$limit);

			$resultSet = $dbTable->fetchAll($query);
			$entries  = array();
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
			$this->view->users = $entries;
		} else {
			$this->view->pages = ceil(count($user->fetchAll())/$limit);
			$this->view->users = $user->fetch($limit,($page-1)*$limit);
		}
		
		$this->view->findUserForm = new List8D_Form_FindUser();
		
		
	}

	public function addAction() {
		
		$user = new List8D_Model_User();
		$currentUser = $user->getCurrentUser();
		
			
		if (!$currentUser->isAllowed('user','create')) 
			throw new Zend_Controller_Action_Exception('You do not have permission create new users.',403);
			
		$request = $this->getRequest();
		$form = new List8D_Form_AddUser();

		$form->setAction($this->view->url());

		// Grab list of roles and popular form select
		$rolePeer = new List8D_Model_Role;
		$roles = array();
		foreach ($rolePeer->fetchAll() as $role) {
			$roles[$role->getId()] = $role->getRoleName();
		}
		$element = $form->getElement("role");
		$element->setMultiOptions($roles);

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
				// Yay, form is valid.
				$user = new List8D_Model_User;
				//TODO: check by login and instid?
				$user->setEmail($form->getValue('email'))
					->setDisplayName($form->getValue('displayname'))
					->setLogin($form->getValue('login'))
					->setRoleId($form->getValue('role'))
					->setInstitutionId($form->getValue('instid'));
				$user->save();
				$this->view->addsuccess = true;
				$this->view->addeduser = $user->getLogin();

				// Clear the form.
				$form->reset();
			} 
		}

		// Pass out the form
		$this->view->addUserForm = $form;

	}

	public function mylistsAction() {
		
		$this->viewRenderer->setViewScriptPathSpec('my-lists.:suffix');
		$this->layoutView->title = "My lists";
		$request = $this->getRequest();
		$params = $request->getParams();

		$user = new List8D_Model_User;
		if (isset($params['id'])) {
			
			$id = $params['id'];

			$user = $user->find($id);

			if ($user->getId() == 0) {
				// Invalid user ID
				return $this->_helper->redirector('index');
			}

			$this->view->user = $user;
			
		} else {
			// No params, so we'll display current user
			$this->view->user = $user->getCurrentUser(); 
		}
		
		if ($this->view->user->getId() !== null) {
			$this->view->lists = $user->getMyLists();
		}
		 
	}
	
	public function recentlistsAction() {
		
		$this->viewRenderer->setViewScriptPathSpec('recent-lists.:suffix');
		$this->layoutView->title = "Recent lists";
		$user = new List8D_Model_User();
		$user = $user->getCurrentUser();
		if ($user->getId() !== null) {
			$this->view->lists = $user->getRecentLists();
		}
		 
	}
	
	public function viewAction() {
	
		$user = new List8D_Model_User();
		$currentUser = $user->getCurrentUser();

		if ($currentUser->getId() != $this->params['id'] && !$currentUser->isAllowed('user','view'))
				throw new Zend_Controller_Action_Exception('You do not have permission to view user with id '.$this->params['id'].'.',403);
		
		$this->viewRenderer->setViewScriptPathSpec('user.:suffix');
		
    $user = new List8D_Model_User;
		
		if (isset($this->params['id'])) {
			
			$id = $this->params['id'];

			if (!($user->find($this->params['id'])))
				throw new Zend_Controller_Action_Exception('User with id '.$this->params['id'].' could not be found.',404);
			
			if ($user->getId() == 0) {
				// Invalid user ID
				return $this->_helper->redirector('index');
			}

			$this->view->user = $user;
			
		} else {
			// No params, so we'll display current user
			$this->view->user = $user->getCurrentUser(); 
		}
		
		$this->layoutView->title = $this->view->user->getDisplayName();
		
	}

	public function editAction() {
		
		$currentUser = new List8D_Model_User;
		$currentUser =  $currentUser->getCurrentUser();
		
		$user = new List8D_Model_User;
		if (isset($this->params['id'])) {
			if (!($user->find($this->params['id'])))
				throw new Zend_Controller_Action_Exception('User with id '.$this->params['id'].' could not be found.',404);
			$this->view->user = $user;
			
			$this->layoutView->title = "Edit user: ".$this->view->user->getDisplayName();
			
			
		
			//if (!$currentUser->isAllowed($user,'edit-tagged') && !$currentUser->isAllowed('user','edit')) 
			if (!$currentUser->isAllowed('user','edit'))
				throw new Zend_Controller_Action_Exception('You do not have permission to edit user with id '.$params['id'].'.',403);


		}
		// create new user
		else {
			$this->layoutView->title = "Create new user";
			if (!$currentUser->isAllowed('user','create')) 
				throw new Zend_Controller_Action_Exception('You do not have permission to edit user with id '.$params['id'].'.',403);

		}
		
		$this->viewRenderer->setViewScriptPathSpec('user.:suffix');
		
   		$form = new List8D_Form_EditUser();
		$form->setAction($this->view->url());
		
		// only library staff should be able to edit role and login
		if (!$currentUser->isAllowed('user','edit')) {
			$form->removeElement('role');
			$form->removeElement('login');
			$form->removeElement('instid');
		} else {
		
			// Grab list of roles and popular form select
			global $application;
    	$settings = $application->getOptions();
    	
    	$roles = array();
			foreach($settings['access']['roles'] as $roleName => $role) {
				$roles[$roleName] = $role['title'];
			}
			
			$element = $form->getElement("role");
			$element->setMultiOptions($roles);
			
		}
		

    if ($this->getRequest()->isPost() && $form->isValid($this->request->getPost())) {

    	
				// Yay, form is valid.
				$user = new List8D_Model_User;
				$user->find($this->params['id']);
				$this->view->user = $user;
				$user->setEmail($form->getValue('email'))
					->setDisplayName($form->getValue('displayname'));
				if ($currentUser->isAllowed('user','create')) {
				
					$user->setLogin($form->getValue('login'))
						->setRole($form->getValue('role'))
						->setInstitutionId($form->getValue('instid'));
				}
				$user->save();
    		return $this->_helper->redirector('view','user', null, array('id' => $user->getId()));
				$this->view->addsuccess = true;
				$this->view->addeduser = $user->getLogin();
			
		} else {
			// No POST, so populate form.
			if (isset($this->params['id'])) {
				// We have an item for updating
				//
				// Retrieve user
				$user = new List8D_Model_User;
				$user = $user->find($this->params['id']);
				$this->view->user = $user;
				// Generate confirmation form
				$form->getElement("email")->setValue($user->getEmail());
				$form->getElement("displayname")->setValue($user->getDisplayName());
				if($currentUser->isAllowed('user','edit')) {
					$form->getElement("role")->setValue($user->getRole());
					$form->getElement("login")->setValue($user->getLogin());
					$form->getElement("instid")->setValue($user->getInstitutionId());  	
				}
				
			} 
			
			$this->view->form = $form;
		}

		// Pass out the form
		$this->view->editUserForm = $form;

	}

	public function removeAction() {


		$request = $this->getRequest();
	    $params = $request->getParams();
		
		$currentUser = new List8D_Model_User();
		$currentUser = $user->getCurrentUser();
		
			if (!$currentUser->isAllowed('user','remove')) 
				throw new Zend_Controller_Action_Exception('You do not have permission to remove users.',403);

    	if (isset($params['id'])) {
			// We have an item for deletion
			//
			// Retreive user for confirmation form
			$user = new List8D_Model_User;

			// Generate confirmation form
			$form = new List8D_Form_ConfirmDelete();
			$form->setAction($this->view->url());
			
			if ($this->getRequest()->isPost()) {
				if ($form->isValid($request->getPost())) {
					// Form is valid and what we expect, so remove the user
					// Update user object with form data...
					$user = $user->find($form->getValue('id'));
					$this->view->user = $user;
					$user->delete();
					return $this->_helper->redirector('index');
				}
			} else {
				// This is a GET, so render the confirmation form
				$user = $user->find($params['id']);
				$this->view->user = $user;
				$hidden = $form->getElement("id");
				$hidden->setValue($user->getId());
				$this->view->confirmForm = $form;
			}
			
		} else {
			// No params, so we'll just display the indexAction.
			return $this->_helper->redirector('index');
		}

	}
	
	/*
public function logoutAction() {

			header("Location: http://logout:logout@{$_SERVER['SERVER_NAME']}");
			exit;
		
	}
*/

	public function addtagAction() {
		
		$user = new List8D_Model_User();
		$currentUser = $user->getCurrentUser();
		
		$this->viewRenderer->setViewScriptPathSpec('user-add-tag.:suffix');
		$this->layoutView->title = "Add tag to user";
		$tag = new List8D_Model_Tag();
		
		$user = new List8D_Model_User;
		$user = $user->find($this->params['id']);
				
		if (!$user) 
			throw new Zend_Controller_Action_Exception("User with id {$this->params['id']} could not be found",404); 
		
		
		if (!$currentUser->isAllowed($user,'tag-tagged') && !$currentUser->isAllowed('user','tag')) 
			throw new Zend_Controller_Action_Exception('You do not have permission to tag user with id '.$this->params['id'].'.',403);

		$form = new List8D_Form_AddTag();
		// we havent chosen a tag yet so display them
	
		$this->view->form = $form;

		if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
			/*
			$tag = $tag->find($this->params['tagid']);
			if (!$tag) 
				throw new Zend_Controller_Action_Exception("Tag with id {$this->params['tagid']} could not be found",404); 
			$umap = new List8D_Model_UserMap();
			$umap->setUserId($this->params['id'])
				 	 ->setTagId($this->params['tagid'])
				   ->save();
			 * 
			 */
			//create new tag
			$values = $form->getValues();

			$tag = new List8D_Model_Tag();
			$tag->setNamespace($values['namespace'])
					->setTagName($values['tag'])
					->save();

			$umap = new List8D_Model_UserMap();
			$umap->setUserId($this->params['id'])
				 	 ->setTagId($tag->getId())
				   ->save();

			if (isset($this->params['destination'])) {
			  return $this->_helper->getHelper('redirector')->gotoUrl($this->params['destination'],array('prependBase'=>false));
			} else
			  return $this->_helper->getHelper('redirector')->gotoSimple('view','user',null,array('id'=>$user->getId()));
				   
		}
		
				
	}
	

}

