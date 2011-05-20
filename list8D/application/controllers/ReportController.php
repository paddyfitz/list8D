<?php

class ReportController extends List8D_Controller {
	
	protected $_limit = 25;
	
	public function standardReport($reportClass) {
		
		// set common template
		$this->viewRenderer->setViewScriptPathSpec('report.:suffix');
		
		// get current user for permission check
		$user = new List8D_Model_User();
		$currentUser = $user->getCurrentUser();

		if (!class_exists($reportClass)) 
			throw new Zend_Controller_Action_Exception("We could not find the report '{$reportClass}'.",404);
		
		// initiate report
		$report = new $reportClass($this);
		
		// check permissions
		if (!$currentUser->isAllowed('report','view') && !$currentUser->isAllowed($report->getAclResource(),'view')) 
			throw new Zend_Controller_Action_Exception("You do not have permission to view the '{$report->getTitle()}' report.",403);
		
		// set the layout title
		$this->layoutView->title = $report->getTitle();
		
		// set the report page and limit
		if (isset($this->params[get_class($report).'-page'])) 
			$report->setPage($this->params[get_class($report).'-page']);
			
		$report->setLimit($this->_limit);
		
		// set the report view
		$report->setView($this->view);
		
		// load report into view
		$this->view->report = $report;
		
		
	}
	
	public function forpurchaseAction() {
	
		$this->standardReport('List8D_Report_ForPurchase');
		
	}
	
	public function addedandremovedAction() {
	
		$this->standardReport('List8D_Report_AddedAndRemovedItems');
		
	}

	public function alllistsAction() {
		
		$this->standardReport('List8D_Report_AllLists');
			
	}
	
	public function privatenoteschangedAction() {
	
		$this->standardReport('List8D_Report_LibrarianNotesChanges');
	
	}

	public function coretextAction() {

		$this->standardReport('List8D_Report_CoreText');
		
	}
}