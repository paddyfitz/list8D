<?php

class ResourceController extends List8D_Controller
{
    public function init() {
						parent::init();
		}

    public function indexAction()
    {
    }
    
    public function listAction()
    {
    }
    
    public function viewAction()
    {
    	$resource = new List8D_Model_Resource();
      $params = $this->getRequest()->getParams();
      $user = new List8D_Model_User();
			$currentUser = $user->getCurrentUser();
		
      
			$resource = $resource->getById($params['id']);
			
			if (!($resource instanceof List8D_Model_Resource))
				// Some "not found" condition occurs.
				throw new Zend_Controller_Action_Exception('Resource not found.',404);

			if (!$currentUser->isAllowed('resource','view')) 
				throw new Zend_Controller_Action_Exception('You do not have permission to view resource with id '.$params['id'].'.',403);
			
    	// set template name to some thing a little simplier
			$this->viewRenderer->setViewScriptPathSpec('resource.:suffix');
			
			$this->view->resource = $resource;
			
    }
}