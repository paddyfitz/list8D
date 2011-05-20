<?php

class AclController extends List8D_Controller {

	public function init() {
		parent::init();
	}

	public function indexAction() {
	//throw new Zend_Controller_Action_Exception('You do not have permission to access this page.',403);
		$request = $this->getRequest();
		$params = $request->getParams();

		$list = new List8D_Model_List;
		$list = $list->getById(1);
		
		$acl = new List8D_ACL;
		$this->view->list = $list;
		$this->view->aclresult = $acl->checkListACL($list);

	}


}