<?php
require_once("FrontController.php");
class LtiController extends FrontController {
	
	public function init() {

		$params = $this->getRequest()->getParams();
		
		$themeSettings = List8D_Config_Ini::getThemeSettings();
		$this->theme = $themeSettings->lti->theme;
		
		if (isset($params['custom_view'])) 
			$this->templateView = $params['custom_view'];
		if (isset($params['launch_presentation_return_url']))
			$this->urlBase = $params['launch_presentation_return_url'];
 
		parent::init();
		
	}
	
	public function listAction() {
	
		$params = $this->getRequest()->getParams();

		if (!empty($params['user_id'])) {
			$user = new List8D_Model_User();
			$user->findByLogin($params['user_id']);
			$this->view->user = $user;		
		}
		
		$this->layoutView->id = $params['context_id'];
		$moduleCodes = explode(",",$params['context_id']);
		$listPeer = new List8D_Model_List();
		
		
		if (isset($params['custom_tabId']))
			$moduleCode = $params['custom_tabId'];
		else 
			$moduleCode = $moduleCodes[0];
		$this->layoutView->selectedTab = $moduleCode;					
		if(count($moduleCodes)>1) {
			$this->layoutView->tabs = array();
			foreach($moduleCodes as $code) {
				
				$list = $listPeer->findByData("sds_id",$code);
				
				if ($list instanceof List8D_Model_List)
					$this->layoutView->tabs[$code] = $list;
			}
		} 
		$this->getRequest()->setParam("id",$moduleCode);
		$this->getRequest()->setParam("getBy",'sds_id');

		return parent::listAction();
		
	}
	
	public function testAction() {
	
		$this->_helper->layout->setLayout('blank');
		
		$this->viewRenderer->setViewScriptPathSpec('test.:suffix');

		$this->layoutView->title = "LTI Tester";

		
	}

}



