<?php

class TagController extends List8D_Controller {

	public function init() {
		parent::init();
	}

	public function indexAction() {
		
		$this->_helper->getHelper('viewRenderer')->setViewScriptPathSpec('tags.:suffix');
		$this->layoutView->title = 'Tags';
		$tag = new List8D_Model_Tag();
		$this->view->tags = $tag->getRoots();
		
	}
	
	public function createAction() {
		$this->layoutView->title = 'Create tag';
		
		// action body
		$form    = new List8D_Form_AddTag();
		
		$user = new List8D_Model_User();
		$currentUser = $user->getCurrentUser();
		
		if (!$currentUser->isAllowed('tag','create')) 
			throw new Zend_Controller_Action_Exception('You do not have permission to edit list with id '.$params['id'].'.',403);
			
		if ($this->getRequest()->isPost()) {
			if ($form->isValid($this->request->getPost())) {
				$tag = new List8D_Model_Tag();
				$tag->setParentId($form->getValue('parent_id'))
					->setTagName($form->getValue('tag'))
					->setNamespace($form->getValue('namespace'))
					->save();
					
				if (isset($this->params['destination'])) {
				  return $this->_helper->getHelper('redirector')->gotoUrl($this->params['destination'],array('prependBase'=>false));
				} else {
				  return $this->_helper->getHelper('redirector')->gotoSimple('view','user',null,array('id'=>$user->getId()));
				} 
			}
		}
		
		$tag = new List8D_Model_Tag();
		$this->view->tags = $tag->getMapper()->fetchByParent(null);
		$this->view->form = $form;
		
	}

	/*
public function adduserAction() {

		$tag = new List8D_Model_Tag;
		$tag = $tag->find($this->params['tagid']);

		// invalid tag id, go to index action.
		if ($tag->getId() == 0) {
			return $this->_helper->redirector('index');
		}

		// populate form
		$form = new List8D_Form_AddTagUser();
		$form->setAction($this->view->url());
		$this->view->success = 0;
		$this->view->tag = $tag;

		$form->getElement("tagid")->setValue($tag->getId());
		$this->view->title = $tag->getNamespace() . ':' . $tag->getTagName();

		// Grab list of users and popular form select
		$userpeer = new List8D_Model_User();
		$users = array();
		foreach ($userpeer->fetchAll() as $user) {
			$users[$user->getId()] = $user->getLogin() . ": " . $user->getDisplayName();
		}
		$element = $form->getElement("userid");
		$element->setMultiOptions($users);



        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->request->getPost())) {
				// Yay, form is valid.

				$umap = new List8D_Model_UserMap();
				$umap->setUserId($form->getValue('userid'))
					->setTagId($form->getValue('tagid'))
					->save();

				$this->view->success = 1;
			} 
		}
		// Pass out the form
		$this->view->form = $form;
	}

	public function addlistAction() {

		$list = new List8D_Model_List;
		$list = $list->getById($this->params['listid']);

		// invalid list id, go to index action.
		if ($list->getId() == 0) {
			return $this->_helper->redirector('index');
		}

		// populate form
		$form = new List8D_Form_AddTagList();
		$form->setAction($this->view->url());
		$this->view->success = 0;
		$this->view->list = $list;

		$form->getElement("listid")->setValue($list->getId());
		$this->view->title = $list->getTitle();

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->request->getPost())) {
				// Yay, form is valid.

				$namespace = "";
				$tagname = "";

				$t = explode(':', $form->getValue('tag'), 2);

				// No namespace, so set default one:
				if (count($t) == 1) {
					$conf = $this->getInvokeArg('bootstrap')->getApplication()->getOptions();
					$defaultns = $conf['list8d']['tag']['defaultnamespace'];
					$tagname = $t[0];
					if ($defaultns != "") {
						$namespace = $defaultns;
					} else {
						$namespace = "DefaultNamespaceNotSet";
					}
				} else if (count($t) == 2) {
					$namespace = $t[0];
					$tagname = $t[1];
				} else {
					throw new Exception("Something went very wrong.");
				}
				
				// Set namespace and tagname
				$tag = new List8D_Model_Tag;
				$tag->setNamespace($namespace)
					->setTagname($tagname)
					->save();

				// Retrieve existing tag if it exists, or create new one
				$tagmap = new List8D_Model_TagMap();
				$tagmap->setTagId($tag->getId())
					->setListId($list->getId())
					->save();

				$this->view->success = 1;
			}
		}
		// Pass out the form
		$this->view->form = $form;

	}
*/

	public function viewAction() {

		$tag = new List8D_Model_Tag;
	
		$user = new List8D_Model_User();
		$currentUser = $user->getCurrentUser();
		
		if (!$tag->find($this->params['id']))
			throw new Zend_Controller_Action_Exception('List with id '.$this->params['id'].' could not be found.',404);
			
		if (!$currentUser->isAllowed('tag','view')) 
			throw new Zend_Controller_Action_Exception('You do not have permission to edit list with id '.$params['id'].'.',403);

		$form = new List8D_Form_SetTagParent;
		$form->setAction($this->view->url());
		$form->getElement("tagid")->setValue($tag->getId());
		$tagpeer = new List8D_Model_Tag();
		$tags = array();
		$tags['0'] = 'None';
		foreach ($tagpeer->fetchAll() as $t) {
			$tags[$t->getId()] = $t->getNamespace() . ":" . $t->getTagname();
		}
		$element = $form->getElement("parentid");
		$element->setMultiOptions($tags);

		if ($this->getRequest()->isPost()) {
            if ($form->isValid($this->request->getPost())) {
				// Yay, form is valid.
				$t = new List8D_Model_Tag;
				$t = $t->find($form->getValue('tagid'));
				$t->setParentId($form->getValue('parentid'))
					->save();
				$tag = $t;
			}
		}

		$this->view->tag = $tag;
		$this->view->form = $form;

	}

	public function listdelAction() {
		
		$list = new List8D_Model_List;
		$list = $list->getById($this->params['listid']);

		
		$tag = new List8D_Model_Tag;
		
		$user = new List8D_Model_User();
		$currentUser = $user->getCurrentUser();
		
		if (!($list instanceof List8D_Model_List) || !$tag->find($this->params['tagid']))
			throw new Zend_Controller_Action_Exception('Page could not be found.',404);
			
		if ((!$currentUser->isAllowed($list,'tag-tagged') && !$currentUser->isAllowed('list','tag'))||$this->isReadOnly($list,$currentUser)) 
			throw new Zend_Controller_Action_Exception('You do not have permission to edit list with id '.$params['id'].'.',403);
			
		if (!$list && !$tag) {
			throw new Zend_Controller_Action_Exception("List with id {$this->params['listid']} and tag with id {$this->params['tagid']} could not be found",404); 
		} else if (!$tag) {
			throw new Zend_Controller_Action_Exception("Tag with id {$this->params['tagid']} could not be found",404); 
		} else if (!$list) {
			throw new Zend_Controller_Action_Exception("List with id {$this->params['listid']} could not be found",404); 
		} 
		
		$tagmap = new List8D_Model_TagMap;
		$tagmap = $tagmap->findTagMap($this->params['tagid'], $this->params['listid']);
		$this->view->tagmap = $tagmap;

		// Generate confirmation form
		$form = new List8D_Form_ConfirmDelete();
		$form->setAction($this->view->url());

		if ($this->getRequest()->isPost()) {
			if ($form->isValid($this->request->getPost())) {
				// Form is valid and what we expect, so remove the user
				// Update user object with form data...
				$tagmap = $tagmap->find($form->getValue('id'));
				$tagmap->delete();
				if (isset($this->params['destination']))
					return $this->_helper->getHelper('redirector')->gotoUrl($this->params['destination']);
				else
					return $this->_helper->getHelper('redirector')->gotoSimple('view','list',null,array('id'=>$list->getId()));
			}
		} else {
			$this->_helper->getHelper('viewRenderer')->setViewScriptPathSpec('confirm.:suffix');
			$this->layoutView->title = "Removing tag";
			$this->view->question = "Are you sure you want to remove the tag '{$tag->getTagName()}' from list '{$list->getTitle()}'? This action is irreversible.";
			// This is a GET, so render the confirmation form
			$hidden = $form->getElement("id");
			$hidden->setValue($tagmap->getId());
			$this->view->form = $form;
		}
	}

	public function userdelAction() {
		
		
		$user = new List8D_Model_User;
		$user->find($this->params['userid']);
		
		$tag = new List8D_Model_Tag;
		$tag->find($this->params['tagid']);
		
		$currentUser = new List8D_Model_User();
		$currentUser = $currentUser->getCurrentUser();
			
		if (!$currentUser->isAllowed($user,'tag-tagged') && !$currentUser->isAllowed('user','tag')) 
			throw new Zend_Controller_Action_Exception('You do not have permission to tag user with id '.$params['userid'].'.',403);
			
		if (!$user && !$tag) {
			throw new Zend_Controller_Action_Exception("User with id {$this->params['userid']} and tag with id {$this->params['tagid']} could not be found",404); 
		} else if (!$tag) {
			throw new Zend_Controller_Action_Exception("Tag with id {$this->params['tagid']} could not be found",404,404); 
		} else if (!$user) {
			throw new Zend_Controller_Action_Exception("User with id {$this->params['userid']} could not be found",404); 
		} 

		$usermap = new List8D_Model_UserMap;

		$usermap = $usermap->findUserMap($this->params['tagid'], $this->params['userid']);
		$this->view->usermap = $usermap;
		
		// Generate confirmation form
		$form = new List8D_Form_ConfirmDelete();
		$form->setAction($this->view->url());

		if ($this->getRequest()->isPost()) {
			if ($form->isValid($this->request->getPost())) {
				// Form is valid and what we expect, so remove the user
				// Update user object with form data...
				$usermap = $usermap->find($form->getValue('id'));
				$usermap->delete();
				if (isset($this->params['destination']))
					return $this->_helper->getHelper('redirector')->gotoUrl($this->params['destination']);
				else
					return $this->_helper->getHelper('redirector')->gotoSimple('view','user',null,array('id'=>$user->getId()));
			}
		} else {
		
			$this->_helper->getHelper('viewRenderer')->setViewScriptPathSpec('confirm.:suffix');
			$this->layoutView->title = "Removing tag";
			$this->view->question = "Are you sure you want to remove the tag '{$tag->getTagName()}' from user '{$user->getDisplayName()}'? This action is irreversible.";
			// This is a GET, so render the confirmation form
			$hidden = $form->getElement("id");
			$hidden->setValue($usermap->getId());
			$this->view->form = $form;
		}
	}
}

