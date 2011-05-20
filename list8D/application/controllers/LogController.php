<?php

class LogController extends List8D_Controller
{
	public function init() {
		parent::init();
	}
	
  public function indexAction() {
  	$form = new List8D_Form_Log();
  	$params = $this->getRequest()->getParams();
    $logPeer = new List8D_Model_LogMapper();
		
		// get the appropriate where clause depending on the params
   	$where = $logPeer->getFilters($params);
   	
   	// do some paginator magic
    $page=$this->_getParam('page',1);
    $paginator = null;
    if ($where != '') {
			$paginator = Zend_Paginator::factory($logPeer->getDbTable()->select()->where($where));
		}
		else {
			$paginator = Zend_Paginator::factory($logPeer->getDbTable()->select());
		}
    $paginator->setItemCountPerPage(10);
    $paginator->setCurrentPageNumber($page);
		
    // display paginator and form
    $this->view->paginator=$paginator;
   	$this->view->form = $form;
  }
  
}