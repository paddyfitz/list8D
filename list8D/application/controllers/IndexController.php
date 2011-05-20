<?php

class IndexController extends List8D_Controller
{

    public function init() {
    	
			parent::init();
			
		}

    public function indexAction()
    {
    	$this->layoutView->title = null;
	    $this->viewRenderer->setViewScriptPathSpec('home.:suffix');
	    $user = new List8D_Model_User();
    	$this->view->user = $user->getCurrentUser();
    }


}

