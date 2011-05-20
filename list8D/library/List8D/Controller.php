<?php 

class List8D_Controller extends Zend_Controller_Action {
    
    
    public function init()
    {
    		
    		$params = $this->getRequest()->getParams();
				$this->view   = new List8D_View();    
				$this->view->setEncoding('UTF-8');
				$this->layout = $this->_helper->getHelper('Layout')->getLayoutInstance();
				$this->layoutView = $this->layout->getView();
				$this->layoutView->displayTitle = true;
				$this->layoutView->setEncoding('UTF-8');
				$this->viewRenderer = $this->_helper->getHelper('viewRenderer');
				$this->request = $this->getRequest();
				$this->params = $this->request->getParams();
				$this->view->params = $this->params;
				global $application;
				$environment = $application->getEnvironment();
				if (isset($params['view']) && !isset($this->templateView))
					$this->templateView = $params['view'];
								
				if (isset($this->templateView))
					$view = "/views/".$this->templateView;
				else
					$view = false;
								
				$themeSettings = List8D_Config_Ini::getThemeSettings();
					
				$currentTheme = $themeSettings->theme;

				$this->view->referenceStyle = $themeSettings->reference;
				$session = new Zend_Session_Namespace('Default');	

				if (isset($this->theme)) {
					$currentTheme = $this->theme;
				} else if (isset($_GET['theme'])) {
					$currentTheme = $_GET['theme'];
					$session->theme = $_GET['theme'];
				} else if ($this->params['controller'] == 'front') {
					$this->theme = $themeSettings->front->theme;
					$currentTheme = $this->theme;
				} else if ($session->theme) {
					$currentTheme = $session->theme;
				} 

				$this->view->theme = $currentTheme;    
				$this->layout->theme = $currentTheme;
				$this->layoutView->theme = $currentTheme;
				$this->viewRenderer->theme = $currentTheme;

				$themeinfo = List8D_Config_Ini::getThemeInfo($currentTheme);

				$currentThemeInfo = $themeinfo;
				$themeinfos = array($currentTheme => $themeinfo);

				$a = $currentThemeInfo->toArray();	
				
				while($themeinfo->extends) {
				
					$key = $themeinfo->extends;
					// load extended theme infos					
					$themeinfo = List8D_Config_Ini::getThemeInfo($themeinfo->extends);
					$themeinfos[$key] = $themeinfo;
					
				}

				foreach(array_reverse($themeinfos) as $key => $themeinfo) {

					// add view helper paths
					$this->view->addHelperPath(APPLICATION_PATH . "/themes/".$key ."/helpers", 'List8D_Theme_'.str_replace(" ","",ucwords(str_replace("_"," ",$key)))."_Helper");
					$this->layoutView->addHelperPath(APPLICATION_PATH . "/themes/".$key ."/helpers", 'List8D_Theme_'.str_replace(" ","",ucwords(str_replace("_"," ",$key)))."_Helper");
					
					// add template paths
					$this->view->addScriptPath(APPLICATION_PATH . "/themes/".$key ."/templates");
					if ($view)
						$this->view->addScriptPath(APPLICATION_PATH . "/themes/".$key ."/templates".$view);

					// add layout paths
					$this->layout->getView()->addScriptPath(APPLICATION_PATH . "/themes/".$key ."/layouts");				
					
				}
				// what all css to load
				if (!empty($a['css']['all'])) {
				  foreach($currentThemeInfo->get('css.all') as $css) {
						$this->view->headLink()->appendStylesheet($css,'all');
				  }
				}
				
				if (!empty($a['css']['screen'])) {
				  // what screen css to load
				  foreach($a['css']['screen'] as $css) {
						$this->view->headLink()->appendStylesheet($css,'screen');
				  }
				}
				
				if (!empty($a['css']['aural'])) {
				  // what aural css to load
				  foreach($a['css']['aural'] as $css) {
						$this->view->headLink()->appendStylesheet($css,'aural');
				  }
				}
							
				
				if (!empty($a['css']['conditional'])) {

				  // what conditional css to load
				  foreach($a['css']['conditional'] as $key => $css) {
				  	$condition = implode(" IE ",explode("IE",$key));
						foreach($css as $medium => $css2) {
							foreach($css2 as $css3) {
								$this->view->headLink()->appendStylesheet($css3,$medium,$condition);
							}							
						}
				  }
				}

				if (!empty($a['js']['head'])) {
				  // what js to load
				  foreach($a['js']['head'] as $js) {
						$this->view->headScript()->appendFile($js);
				  }
				}
							
				// set paths
				$tmplPath = APPLICATION_PATH . "/themes/".$themeSettings->theme."/templates" ;
				if ($view)
					$tmplPath = APPLICATION_PATH . "/themes/".$themeSettings->theme."/templates".$view ;
				$layoutPath = APPLICATION_PATH . "/themes/".$themeSettings->theme ."/layouts";
	
				// add action helper path
				Zend_Controller_Action_HelperBroker::addPath("List8D/helpers", 'List8D_Action_Helper');
									
        $viewRenderer = $this->_helper->getHelper('viewRenderer');

        $viewRenderer->setView($this->view)
                     ->setViewBasePathSpec($tmplPath)
                     ->setViewScriptPathSpec(':controller-:action.:suffix')
                     ->setViewScriptPathNoControllerSpec('list.tpl.php')
                     ->setViewSuffix('tpl.php');
				

        $this->layout->setLayout("default")
				             ->setViewSuffix('tpl.php');

				// set layout variables
				if ($themeSettings->variables) {
					foreach($themeSettings->variables as $key => $variable) {
						$this->layout->getView()->$key = $variable;			
						$this->view->$key = $variable;
					}
				}
				
				$frontController = Zend_Controller_Front::getInstance();
				if (isset($themeSettings->front->variables) && $themeSettings->front->variables && $frontController->getDefaultControllerName() == "front") {
					foreach($themeSettings->front->variables as $key => $variable) {
						$this->layout->getView()->$key = $variable;						
						$this->view->$key = $variable;
					}
				}
				$session = new Zend_Session_Namespace('Default');
				if (is_array($session->recentLists)) {
					$recentLists = $session->recentLists;
				} else {
					$recentLists = array();
				}
				$recentLists = array_unique($recentLists);		
				$listPeer = new List8D_Model_List;

				foreach($recentLists as &$list) {
					$list = $listPeer->getById($list);
				}
				$this->layoutView->recentLists = $recentLists;
							
				// add jquery
				$this->view->headScript()->appendFile('jquery-1.4.1.pack.js');
				
				if (!empty($a['js']['inline'])) {
				  // what js to load
				  foreach($a['js']['inline'] as $js) {
						$this->view->inlineScript()->appendFile($js);
				  }
				}
				
				$user = new List8D_Model_User;
				$this->layout->getView()->user =  $user->getCurrentUser();
				$this->view->currentUser = $this->layout->getView()->user;
				
				$this->view->inlineScript()->appendFile('list8D.js');		
				$list8DJS = "var list8D = {";
				$list8DJS .= "controller: ".json_encode($this->getRequest()->getControllerName()).",";
				$list8DJS .= "action: ".json_encode($this->getRequest()->getActionName()).",";
				$list8DJS .= "params:".json_encode($this->getRequest()->getParams());
				$list8DJS .= "};";
				
				$this->view->headScript()->appendScript($list8DJS);		
				
    		$this->flashMessenger = $this->_helper->getHelper('FlashMessenger');

    }
    
    public function postDispatch() {
      if( isset( $this->flashMessenger ) )
        $this->view->flashMessenger = $this->layoutView->flashMessenger = $this->flashMessenger;	
    }
    
    public function writeMessage() {
    
    }
    
    public function getDestination() {

    	if(isset($_GET['destination'])) {
    		return $_GET['destination'];
    	} else {
    		return $this->view->url();
    	}
    	
    }
 	function isReadOnly($list, $currentUser){
		$readOnly = false;
		$conf = $this->getInvokeArg('bootstrap')->getApplication()->getOptions();
		$currentYear = $conf['admin']['currentYear'];
		
		//find the topmost list
		if($list->getListId()){
			$parentPeer = new List8D_Model_List;
			$parentList = $parentPeer->getById($list->getListId());
			//if $parentList is itself a nested list, and so on.....
			while ($parentList->getListId()){
				$parentList = $parentPeer->getById($parentList->getListId());
			}
			if($parentList->getDataValue('year') < $currentYear && !$currentUser->isAllowed($parentList, 'edit-old')){
				$readOnly = true;
			}
		}
		else{
			if($list->getDataValue('year') < $currentYear && !$currentUser->isAllowed($list, 'edit-old')){
				$readOnly = true;
			}
		}
		return $readOnly;
	}
    
}
