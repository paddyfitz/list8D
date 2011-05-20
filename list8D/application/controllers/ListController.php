<?php

class ListController extends List8D_Controller {
	
	public function init() {
		parent::init();
	}

	public function indexAction() {

		// TODO redirect to all lists report
		
	}

	public function searchAction() {

		$user = new List8D_Model_User();
		$currentUser = $user->getCurrentUser();
		
		if (!$currentUser->isAllowed('list','search')) 
			throw new Zend_Controller_Action_Exception('You do not have permission to search for lists.',403);
			
		$listPeer = new List8D_Model_List();
		$post = $this->getRequest()->getParam('q');

		$post = str_replace("'",'&#039;',trim($post));
		
		$list = new List8D_Model_List();

/*
		if ($list = $list->findByData('code',$post)) {
			return $this->_helper->redirector('view','list', null, array('id' => $list->getId());
		}
*/
		
		$this->viewRenderer->setViewScriptPathSpec('list-search-results.:suffix');

		$this->layoutView->title = "Search Results";
		
		$this->layoutView->title = "List search";
		
		$this->view->tryAgain = new List8D_Form_TitleSearchLists(); 
		
		if (!empty($this->params['page']))
			$this->view->page = $this->params['page'];
		else 
			$this->view->page = 1;
			
		if (empty($post)) 
			return;
		
		$yearToSearch = '2009';
		if(!empty($yearToSearch))
			$yearToSearchQuery = " AND `year`.`value` = '".serialize($yearToSearch)."'";
		else 
			$yearToSearchQuery = "";
			
		$this->layoutView->title = "List search results";
		
		$this->view->searchedFor = array("title or code"=>$post);
		
		$this->view->tryAgain->getElement('q')->setValue($post);
		
		$offset = ($this->view->page-1)*10;
		
		$codeQuery = "
SELECT SQL_CALC_FOUND_ROWS `list`.`row_id` FROM (
	SELECT `code`.`row_id`, `code`.`value` AS `code`, `year`.`value` AS `year` FROM `list_data` AS `year`
	JOIN `list_data` AS `code` ON `code`.`row_id` = `year`.`row_id` AND `code`.`key`='code' AND `year`.`key` = 'year'
	WHERE `code`.`value` = '".serialize($post)."'
) AS `list`
JOIN (
	SELECT `code`.`value` AS `code`, MAX(`year`.`value`) AS `max_year` 
	FROM `list_data` AS `year`
	JOIN `list_data` AS `code` ON `code`.`row_id` = `year`.`row_id` AND `code`.`key`='code' AND `year`.`key` = 'year'
	WHERE `code`.`value` = '".serialize($post)."' {$yearToSearchQuery}
	GROUP BY `code`.`value`
) AS `max_year` ON `list`.`code` = `max_year`.`code` AND `list`.`year` = `max_year`.`max_year`
LIMIT 10 OFFSET $offset;";

		$result = array();

		$db = Zend_Registry::get('dbResource');
		$db = $db->getDbAdapter();
	
		$result = $db->fetchAll($codeQuery);
		$totalResult = $db->fetchOne("SELECT FOUND_ROWS() AS `count`");

		if ($totalResult==1) 
			return $this->_helper->redirector('view','list', null, array('id' => $result[0]['row_id']));
		
		$titleQuery = "
SELECT SQL_CALC_FOUND_ROWS `list`.`row_id` FROM (
	SELECT `code`.`row_id`, `code`.`value` AS `code`, `year`.`value` AS `year` FROM `list_data` AS `year`
	JOIN `list_data` AS `code` ON `code`.`row_id` = `year`.`row_id` AND `code`.`key`='title' AND `year`.`key` = 'year'
	WHERE `code`.`value` like '%{$post}%'
) AS `list`
JOIN (
	SELECT `code`.`value` AS `code`, MAX(`year`.`value`) AS `max_year` FROM `list_data` AS `year`
	JOIN `list_data` AS `code` ON `code`.`row_id` = `year`.`row_id` AND `code`.`key`='title' AND `year`.`key` = 'year'
	WHERE `code`.`value` LIKE '%{$post}%' {$yearToSearchQuery}
	GROUP BY `code`.`value`
) AS `max_year` ON `list`.`code` = `max_year`.`code` AND `list`.`year` = `max_year`.`max_year`";
	
		if (count($result)) 
			$titleQuery .= " WHERE `list`.`row_id` NOT IN (".implode(",",call_user_func_array('array_merge',$result)).")";
		
		$limit = (10-$totalResult<0) ? 0 : 10-$totalResult; 
		$titleQuery .= " LIMIT {$limit} OFFSET $offset;"; 

		$result = array_merge($result, $db->fetchAll($titleQuery));
		$totalResult += $db->fetchOne("SELECT FOUND_ROWS() AS `count`");

		if ($totalResult==1)
			return $this->_helper->redirector('view','list', null, array('id' => $result[0]['row_id']));

		$result = array_slice($result,0,10);
		$return = array();
		foreach($result as $list) {
			if (!isset($return[$list['row_id']]))
				$return[$list['row_id']] = $listPeer->getById($list['row_id']);
		}
		
		$this->view->searchResults = $return;
		$this->view->numberOfResults = $totalResult;
		$this->view->pages = ceil($this->view->numberOfResults/10);
		
	}
	
	public function viewAction() {

		
		$user = new List8D_Model_User();
		$currentUser = $user->getCurrentUser();
		
		$params = $this->getRequest()->getParams();
				
		$db = Zend_Registry::get('dbResource');
		$db = $db->getDbAdapter();
		$list = new List8D_Model_List();
		$list = $list->getById($params['id']);
		
		if (!($list instanceof List8D_Model_List))
			// Some "not found" condition occurs.
			throw new Zend_Controller_Action_Exception('Page not found.',404);

		if (!$currentUser->isAllowed($list,'edit')) 
			throw new Zend_Controller_Action_Exception('You do not have permission to view list with id '.$params['id'].'.',403);
		
		if ((isset($this->params['load']) && $this->params['load']) || $list->getSize() < 200) {
			$list->quickLoad();
			$this->viewRenderer->setViewScriptPathSpec('list.:suffix');
		} else {
			$this->viewRenderer->setViewScriptPathSpec('loading.:suffix');
			$this->view->heading = "Large List";
			$this->view->description = "This list is really large, it will take several seconds to load.";
			$this->view->loadingHeading = "Loading list";
			return;
		}
		
		// document available warning
		if ($list->getDocumentUrls()) {
			$description = "<p>Clicking on ". (count($list->getDocumentUrls())>1 ? "these links" : "this link") ." leads to ". (count($list->getDocumentUrls())>1 ? "updates" : "an update") ." for this Reading List.</p><ul class='actions'>";
			foreach ($list->getDocumentUrls() as $url => $text) {
				$description .= "<li style='float:none; margin-bottom: 5px;' class='btn with-icon'><a href='{$url}' class='button icon icon-arrow-270'>Download $text</a></li>";
			}
			
			$description .= "</ul>";
			$this->flashMessenger->addMessage(array(
				"type"=>"warning",
				"title"=>"This list has " . (count($list->getDocumentUrls())>1 ? "" : "a ") . "document link". (count($list->getDocumentUrls())>1 ? "s" : ""),
				"description"=>$description,
				/*
"actions"=>array(
					array(
						'url'=>"http://bbc.co.uk",
						'text'=>"Download document",
						'class'=>'icon icon-arrow-180',
					)
				),
*/
				'closable'=>false,
				
			));
		}
		
		// send auto publish message
		if ($list->getDataValue('was_auto_published')) {
			$this->flashMessenger->addMessage(array(
				"type"=>"info",
				"title"=>"This list has been automatically published",
				"description"=>"<p>This List was automatically published when it was copied over from last year. Please confirm that you have made any necessary changes for it's academic year, and click 'Publish list'. Please note students will receive a similar notice.</p>",
				"actions"=>array(
					array(
						'url'=>array('controller'=>'list','action'=>'manualpublish','id'=>$list->getId()),
						'text'=>"Publish list",
						'class'=>'icon icon-tick-circle',
					)
				),
				'closable'=>false,
				
			));
		}
		
		// set layout title
		if ($list->getDataValue('code') && $this->view->theme != 'pontus') {
			$title = $list->getDataValue('code')." - ";
		} else {
			$title = "";
		}
		
		$readonly_text = "";
		$conf = $this->getInvokeArg('bootstrap')->getApplication()->getOptions();
		$currentYear = $conf['admin']['currentYear'];
				
		if($list->getDataValue('year') < $currentYear && !$currentUser->isAllowed($list, 'edit-old')){
			$readonly_text = " (Read only)";
			$this->view->readOnly = true;
		}
		
		$this->layoutView->title = $title.$list->getTitle().$readonly_text;
		
		$this->layoutView->list = $list;
		
		$this->view->list = $list;
		$this->view->currentUser = $currentUser;

		// populate tag form
		$tagform = new List8D_Form_AddTagList();
		$tagform->setAction($this->view->baseUrl() . '/tag/addlist');
		$this->view->tagform = $tagform;

		$tagform->getElement("listid")->setValue($list->getId());

		$session = new Zend_Session_Namespace('Default');
		
		// save to recently viewed
		$db = Zend_Registry::get('dbResource');
		
	  if ($currentUser) {
			$db->getDbAdapter()->query("REPLACE INTO `recent_lists` (created, user_id, list_id) VALUES ('".date('Y-m-d H:i:s')."',{$currentUser->getId()},{$list->getId()})"); 
			$count = $db->getDbAdapter()->query("SELECT COUNT(*) FROM `recent_lists`WHERE user_id='{$currentUser->getId()}'")->fetch();
			$count = $count['COUNT(*)'];
			if ($count>10) {
				$count = $count-10;
				$db->getDbAdapter()->query("DELETE FROM `recent_lists` WHERE user_id='{$currentUser->getId()}' ORDER BY created LIMIT {$count}"); 
			}
		}
		//$db->query();
		
		// add resorting code
		$this->view->inlineScript()->appendFile('interface/source/iutil.js');
		$this->view->inlineScript()->appendFile('interface/source/idrag.js');
		$this->view->inlineScript()->appendFile('interface/source/idrop.js');
		$this->view->inlineScript()->appendFile('interface/source/isortables.js');
		$this->view->inlineScript()->appendFile('inestedsortable.js');
		$this->view->inlineScript()->appendFile('execNestedSortable.js');

		$resourceHandler = new List8D_Model_Resource_Handler();
		$this->view->contentTypes = $resourceHandler->getResourceTypes();
		
		if (isset($params['view'])) {
			$this->view->view = $params['view'];
		} else {
		
			/*if (count($list->getChildren())>200) {
				$this->view->view = "small";	
			} else if (count($list->getChildren())>75) {
				$this->view->view = "small";	
			} else {
				$this->view->view = "small";	
			}*/
			$this->view->view = "small";
		}
	}

	public function additemAction() {
		$params = $this->getRequest()->getParams();

		$listPeer = new List8D_Model_List();
		$list = $listPeer->getById($params['id']);
		
		//add item here

		if (!isset($params['position']) || $params == "" || !is_numeric($params['position']))
			$position = null;
		else 
			$position = (int) $params['position'];

		$this->view->position = $position;

		$user = new List8D_Model_User();
		$currentUser = $user->getCurrentUser();
		
		if (!($list instanceof List8D_Model_List))
			throw new Zend_Controller_Action_Exception('List with id '.$params['id'].' could not be found.',404);
		
		if ((!$currentUser->isAllowed($list,'edit-tagged') && !$currentUser->isAllowed('list','edit')) || $this->isReadOnly($list,$currentUser)) 
			throw new Zend_Controller_Action_Exception('You do not have permission to edit list with id '.$params['id'].'.',403);

   		$typeHandler = new List8D_Model_Resource_Handler();		
		$types = $typeHandler->getResourceTypes();
	
		// If we don't have a resource type we need one
		if (!isset($params['type'])) {
			$params['type'] = current(array_keys($types));			
		}
		
		$this->layoutView->title = "Add item to list: \"{$list->getTitle()}\"";

    	$metatronHandler = new List8D_Model_Metatron_Handler();

   	
    	$this->view->contentTypes = $types;
    
    	if ($this->_request->isPost()){
	    	$this->view->isPost = true;
			}else{
	    	$this->view->isPost = false;
	    }
    
    
      	$form = new List8D_Form();
		$resourceType = $typeHandler->getResourceType($params['type']);
		if ($resourceType->getType() == 'List') {
			$form = $resourceType->getEditForm();
			$form->addElement('submit', 'submit', array(
			    'ignore'   => true,
			    'label'    => 'create',
			    'class'		 => array('btn'),
			));
		} else {
			$form = $resourceType->getSearchForm();
			$form->setMethod('post');
			if(is_int($position)){
				$form->setAction($this->view->url(array('controller'=>'list', 'action'=>'additem', 'id'=>$params['id'], 'type'=>$params['type'],'position'=>$position)));	
			}
			else{
				$form->setAction($this->view->url(array('controller'=>'list', 'action'=>'additem', 'id'=>$params['id'], 'type'=>$params['type'])));
			}
			$form->addElement('submit', 'submit', array(
			    'ignore'   => true,
			    'label'    => 'Search',
			    'class'		 => array('btn'),
			));
		}
		
		
		$this->view->currentType = $params['type']; 
		// We have a resource type so lets generate its search form
		if ((!$this->getRequest()->isPost() && !isset($params['resourceid'])) || ($this->getRequest()->isPost() && !$form->isValid($this->getRequest()->getPost()) && ($this->getRequest()->getPost('type') == null))) {
	
			$this->viewRenderer->setViewScriptPathSpec('add-item.:suffix');
			$this->view->searchForm = $form; 
		
		} 
		// We have a resource type and its a list and we're got the data for it
		else if (isset($params['type']) && $resourceType->getType() == 'List' && $this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
			$class = get_class($resourceType);
		
			$list = new $class;
			$data = $this->getRequest()->getPost();
			unset($data['submit']);
			$list->setDataByArray($data);
			$list->dataLoaded = true;
			$list->setListId((int) $params['id']);
			$parentList = new List8D_Model_List();
			$parentList = $parentList->getById($params['id']);
			if(is_int($position)){
				$place = $position;
			}
			else{
				$place = $parentList->getLength()+1;
			}
			$list->setPosition($place);

			$list->save();
			$redirector = $this->_helper->getHelper("Redirector");
			$redirector->setPrependBase("");
			if(is_int($position)){
				$id_to_return = 0;
				if($list->isNested()){
					$id_to_return = $list->getListId();
				}
				else{
					$id_to_return = $list->getId(); 
				}
				//return $redirector->gotoUrl($this->view->url(array('controller'=>'list','action'=>'view','id'=>$this->params['id'],'multiple'=>null,'token'=>null,'metatron'=>null,'resourceid'=>null,'type'=>null,'position'=>$position))."#list_".$list->getId());
				return $redirector->gotoUrl($this->view->url(array('controller'=>'list','action'=>'view','id'=>$list->getTrunkId(),'multiple'=>null,'token'=>null,'metatron'=>null,'resourceid'=>null,'type'=>null,'position'=>$position))."#list_".$list->getId());
    			
			}
			else{
    			return $redirector->gotoUrl($this->view->url(array('controller'=>'list','action'=>'view','id'=>$this->params['id'],'multiple'=>null,'token'=>null,'metatron'=>null,'resourceid'=>null,'type'=>null))."#list_".$list->getId());
			}
		
		}
		// We've searched for something
		else {
			//echo "I'm free"; exit;
			$this->viewRenderer->setViewScriptPathSpec('resource-results.:suffix');
			$session = new Zend_Session_Namespace('Default');
		
      		// We haven't selected anything 
      		if ((!isset($params['resourceid']) && !isset($params['multiple'])) || isset($_POST['moreresults']) || isset($_POST['fewerresults'])) {
	
				$limit = 10;
				$from = 0;
				$maxLimit = 200;
				$increment = 10;
				$more = false;
				
				if(isset($_POST['moreresults'])){
					$pageLimit = $params['limit'];
					if(($pageLimit + $increment) <= $maxLimit){
						$limit = $pageLimit + $increment;
						$from = $pageLimit;
					}
					else{
						$limit = $pageLimit;
						$from = $pageLimit - $increment;
					}
					//$metatronHandler->setRecordPage($limit/10);
				}
				if(isset($_POST['fewerresults'])){
					$pageLimit = $params['limit'];
					$limit = $pageLimit - $increment;
					$from = $pageLimit - ($increment * 2);
					//$metatronHandler->setRecordPage($limit/10);
				}
				
				$resourceDatas = $metatronHandler->findResources($form->getValues(),$params['type'],$limit/10);
				
				//determine whether or not to allow 'next'
				foreach($resourceDatas as $resourceData){
					if(sizeof($resourceData) == $increment){
						$more = true;
					}
				}
				
				
				$this->view->formValues = array();
				$this->layoutView->title = "Add ".$params['type']." to list: \"{$list->getTitle()}\"";
				$this->view->resourceType = $params['type'];
				$this->view->currentLimit = $limit;
				$this->view->from = $from + 1;
				$this->view->more = $more;
				$formValues = $form->getValues();
				
				//trim all out of resourceDatas before limit - increment
				//for now just Voyager needs this
				
				//delete from the beginning where index is less than limit - increment
				
				if(isset($resourceDatas["List8D_Model_Metatron_KentVoyager"])){
					if($from != 0){
					
						//pull out the Kent Voyager related items
						$voyagers = $resourceDatas["List8D_Model_Metatron_KentVoyager"];
					
						for($i = 0; $i < $from; $i++){
							unset($voyagers[$i]);
						}
						//re-index
						$voyagers = array_values($voyagers);
					
						$resourceDatas["List8D_Model_Metatron_KentVoyager"] = $voyagers;
					}
				}
				
				
				foreach($formValues as $key => $value) {
					$this->view->formValues[$form->getElement($key)->getName()] = array($form->getElement($key)->getName(),$form->getElement($key)->getLabel(),$value);
				}

				// put the results into the view
				if (!isset($resourceDatas)) {
					return;
				}
			
				$this->view->searchResults = $resourceDatas;

				// Put this results in the session as we cant be sure that the next requests results will be the same
				$token = hash('md5','additem'.time());
			
  				$session->$token = $resourceDatas;
				
  				$this->view->token = $token;

				//$this->view->moreResultsText = false;
				//$this->view->moreResultsLimit = $limit;				
 			
      		}
           
			else if (isset($params['resourceid']) && isset($params['metatron']) && isset($params['token'])) {
			
				$token = $params['token'];
			
				// get temporary resource
				$resource = $session->$token;
				$resource = $resource[$params['metatron']][$params['resourceid']];
			
			
				if (!is_object($resource))
					throw new Exception("There was a problem getting the resource object from the session");
				
				// build actual resource

				$typeClass = $typeHandler->getTypeClass($params['type']);
				$resource = $metatronHandler->buildResource($resource,$typeClass,true);
			
			
				// add to list
			
				$list = new List8D_Model_List();
				if (empty($params['onlist']))
					$listId = $params['id'];
				else
					$listId = $params['onlist'];
				
				
				if (!isset($params['position']) || $params == "" || !is_numeric($params['position']))
					$place = null;
				else 
					$place = (int) $params['position'];

				$list = $list->getById($listId);
			
				$item = $list->addItem($resource,$place);
				$item->setData('is_published',true);
				$item->save();
			
				// write message that add was successful
				$this->writeMessage("Item \"{$resource->getTitle()}\" was sucessfully added to the list \"{$list->getTitle()}\"","ok");

				// unset session info
				//$session->resourceType = null;
				//$session->theResults = null;
			
				$redirector = $this->_helper->getHelper("Redirector");
			
				// redirect back to the list
				if ($item->isList())
					$type='list';
				else
					$type='item';
				
				$redirector->setPrependBase("");
    		if (isset($this->params['then'])) {
				  if ($this->params['then']=='searchagain') {
					if(is_int($position)){
						return $redirector->gotoUrl($this->view->url(array('controller'=>'list','action'=>'additem','id'=>$this->params['id'],'token'=>null,'metatron'=>null,'resourceid'=>null,'type'=>$this->params['type'],'then'=>null, 'position' => $position)));
					}
					else{
				  		return $redirector->gotoUrl($this->view->url(array('controller'=>'list','action'=>'additem','id'=>$this->params['id'],'token'=>null,'metatron'=>null,'resourceid'=>null,'type'=>$this->params['type'],'then'=>null)));
					}
				  } elseif ($this->params['then']=='edit') {
					if(is_int($position)){
						return $redirector->gotoUrl($this->view->url(array('controller'=>'data','action'=>'edit','id'=>$this->params['id'],'token'=>null,'metatron'=>null,'resourceid'=>null,'type'=>null,'itemid'=>$item->getId(), 'position' => $position)));
				  	}
					else{
						return $redirector->gotoUrl($this->view->url(array('controller'=>'data','action'=>'edit','id'=>$this->params['id'],'token'=>null,'metatron'=>null,'resourceid'=>null,'type'=>null,'itemid'=>$item->getId())));
					}
				  }
				} else {
					if(is_int($position)){
						$id_to_return = 0;
						if($list->isNested()){
							$id_to_return = $list->getListId();
						}
						else{
							$id_to_return = $list->getId();
						}
						//return $redirector->gotoUrl($this->view->url(array('controller'=>'list','action'=>'view','id'=>$this->params['id'],'token'=>null,'metatron'=>null,'resourceid'=>null,'type'=>null, 'position' => $position))."#".$type."_".$item->getId());
						return $redirector->gotoUrl($this->view->url(array('controller'=>'list','action'=>'view','id'=>$item->getTrunkId(),'token'=>null,'metatron'=>null,'resourceid'=>null,'type'=>null, 'position' => $position))."#".$type."_".$item->getId());
					}
					else{
				  		return $redirector->gotoUrl($this->view->url(array('controller'=>'list','action'=>'view','id'=>$this->params['id'],'token'=>null,'metatron'=>null,'resourceid'=>null,'type'=>null))."#".$type."_".$item->getId());
					}
				}
					

			} else {
				
				//if we have a multi-select...
				//if something is chosen - otherwise kick them back to search
				if(isset($params['chosen'])){
					if(isset($params['multiple'])){
			
						// we want to added multiple resources
						if($params['multiple'] == "true"){

							$token = $params['token'];
							$resourcefromsession = $session->$token;
				
							foreach($params['chosen'] as $key => $value){
				
								$metatron = $key;
					
								foreach($params['chosen'][$key] as $key2 => $value2){
						
									$resourceid = $key2;								
						
									$resource = $resourcefromsession[$metatron][$resourceid];
						
									if (!is_object($resource)){
										throw new Exception("There was a problem getting the resource object from the session");
									}
									// build actual resource

									$typeClass = $typeHandler->getTypeClass($params['type']);
									$resource = $metatronHandler->buildResource($resource,$typeClass,true);

									// add to list
									$list = new List8D_Model_List();
									if (empty($params['onlist'])){
										$listId = $params['id'];
									} else {
										$listId = $params['onlist'];
									}

									if (!isset($params['position']) || $params == "" || !is_numeric($params['position'])){
										$place = null;
									} else {
										$place = (int) $params['position'];
									}

									$list = $list->getById($listId);

									$item = $list->addItem($resource,$place);
									$item->setData('is_published',true);
									$item->save();

									// write message that add was successful
									$this->writeMessage("Item \"{$resource->getTitle()}\" was successfully added to the list \"{$list->getTitle()}\"","ok");
						
								}
							}
						}	
						$redirector = $this->_helper->getHelper("Redirector");

						// redirect back to the list
						if ($item->isList()){
							$type='list';
						} else {
							$type='item';
						}
				
						$redirector->setPrependBase("");
						if (isset($_POST['searchagain']))
							if(is_int($position)){
								$id_to_return = 0;
								if($list->isNested()){
									$id_to_return = $list->getListId();
								}
								else{
									$id_to_return = $list->getId();
								}
								//return $redirector->gotoUrl($this->view->url(array('controller'=>'list','action'=>'additem','id'=>$this->params['id'],'token'=>null,'multiple'=>null, 'position' => $position),null,false,true));
								return $redirector->gotoUrl($this->view->url(array('controller'=>'list','action'=>'additem','id'=>$item->getTrunkId(),'token'=>null,'multiple'=>null, 'position' => $position),null,false,true));
							}
							else{
								return $redirector->gotoUrl($this->view->url(array('controller'=>'list','action'=>'additem','id'=>$this->params['id'],'token'=>null,'multiple'=>null),null,false,true));
							}
				    	
						else
							if(is_int($position)){
								$id_to_return = 0;
								if($list->isNested()){
									$id_to_return = $list->getListId();
								}
								else{
									$id_to_return = $list->getId();
								}
								//return $redirector->gotoUrl($this->view->url(array('controller'=>'list','action'=>'view','id'=>$this->params['id'],'token'=>null,'metatron'=>null,'resourceid'=>null,'type'=>null,'multiple'=>null, 'position' => $position))."#".$type."_".$item->getId());
								return $redirector->gotoUrl($this->view->url(array('controller'=>'list','action'=>'view','id'=>$item->getTrunkId(),'token'=>null,'metatron'=>null,'resourceid'=>null,'type'=>null,'multiple'=>null, 'position' => $position))."#".$type."_".$item->getId());
							}
							else{
				    	return $redirector->gotoUrl($this->view->url(array('controller'=>'list','action'=>'view','id'=>$this->params['id'],'token'=>null,'metatron'=>null,'resourceid'=>null,'type'=>null,'multiple'=>null))."#".$type."_".$item->getId());
						}
				
					}
				}
				else{
					//what do we want to do when someone clicks the 'add all' button with nothing selected?
					$redirector = $this->_helper->getHelper("Redirector");
					$redirector->setPrependBase("");
					if(is_int($position)){
						$id_to_return = 0;
						if($list->isNested()){
							$id_to_return = $list->getListId();
						}
						else{
							$id_to_return = $list->getId();
						}
						//$redirector->gotoUrl($this->view->url(array('controller'=>'list','action'=>'view','id'=>$this->params['id'],'token'=>null,'metatron'=>null,'resourceid'=>null,'type'=>null,'multiple'=>null,'id'=>$params['id'], 'position' => $position)));
						$redirector->gotoUrl($this->view->url(array('controller'=>'list','action'=>'view','id'=>$item->getTrunkId(),'token'=>null,'metatron'=>null,'resourceid'=>null,'type'=>null,'multiple'=>null,'id'=>$params['id'], 'position' => $position)));
					}
					else{	$redirector->gotoUrl($this->view->url(array('controller'=>'list','action'=>'view','id'=>$this->params['id'],'token'=>null,'metatron'=>null,'resourceid'=>null,'type'=>null,'multiple'=>null,'id'=>$params['id'])));
					}
				}
			}
		}
	}


	// adds a non-metatron resource via a user-filled form
	public function addmyitemAction() {
		
		$params = $this->getRequest()->getParams();

		$listPeer = new List8D_Model_List();
		$list = $listPeer->getById($params['id']);

		$user = new List8D_Model_User();
		$currentUser = $user->getCurrentUser();
		
		if (!($list instanceof List8D_Model_List))
			throw new Zend_Controller_Action_Exception('List with id '.$params['id'].' could not be found.',404);
		
		if ((!$currentUser->isAllowed($list,'edit-tagged') && !$currentUser->isAllowed('list','edit')) || $this->isReadOnly($list,$currentUser)) 
			throw new Zend_Controller_Action_Exception('You do not have permission to edit list with id '.$params['id'].'.',403);
	
		if (!$currentUser->isAllowed('resource','create')) 
			throw new Zend_Controller_Action_Exception('You do not have permission to create resources.',403);
		
		$this->layoutView->title = "Add item to list: \"{$list->getTitle()}\"";

    	$metatronHandler = new List8D_Model_Metatron_Handler();
    	$typeHandler = new List8D_Model_Resource_Handler();
    	$types = $typeHandler->getResourceTypes();
    	$this->view->contentTypes = $types;
		$this->view->list = $list;
   		$form = new List8D_Form();

    	// show the edit form when adding a new item by hand
    	if (isset($params['type']) && !$this->getRequest()->isPost()) {
   					
    		if ($params['type'] == 'List') {
				throw new Zend_Controller_Action_Exception('Cannot create a new nested list while adding new items to a list by hand.',404);
			}
		
      		$form = new List8D_Form();
			$resourceType = $typeHandler->getResourceType($params['type']);
			$form = $resourceType->getSearchForm();
			$form->setMethod('post');
			$form->addElement('submit', 'submit', array(
			    'ignore'   => true,
			    'label'    => 'create',
			    'class'		 => array('btn'),
			));
			$form->setAction($this->view->url(array('controller'=>'list', 'action'=>'addmyitem', 'id'=>$params['id'], 'type'=>$params['type'])));
			
			// set the current type for use in the view
			$this->view->currentType = $params['type'];
		
			// set the current action so we can use the same view for adding an item by hand (as here) or pulling from the metatron
			$this->view->currentAction = 'create';
   	
		}
	
		// If we don't have a resource type we need one
		if (!isset($params['type'])) {
		    // set template to list-select-type-to-add.tpl.php
		    $this->viewRenderer->setViewScriptPathSpec('list-select-type.:suffix');
	    
		    $this->view->types = $types;
	
		}
	
		// We have a resource type so lets generate its create form
		else if ((!$this->getRequest()->isPost() && !isset($params['resourceid'])) || ($this->getRequest()->isPost() && !$form->isValid($this->getRequest()->getPost()) && ($this->getRequest()->getPost('type') == null))) {
	
			$this->viewRenderer->setViewScriptPathSpec('add-item.:suffix');
			$this->view->searchForm = $form;
		
		} 
		// We've added something - this bit is called once the form has been resubmitted
		else {
			
			$resourceDatas = array();
			$this->view->formValues = array();
			//$formValues = $form->getValues();
			$data = $this->getRequest()->getPost();
			
			// create a new resource and fill it with the form values
			$typeClass = $typeHandler->getTypeClass($params['type']);
			$resource = new $typeClass();
			
			$resource->setDataByArray($data);
			$resource->dataLoaded = true;	
			$resource->save();
		
			// add to list
			$list = new List8D_Model_List();
			if (empty($params['onlist']))
				$listId = $params['id'];
			else
				$listId = $params['onlist'];
			if (!isset($params['position']) || $params == "" || !is_numeric($params['position']))
				$position = null;
			else 
				$position = (int) $params['position'];
		
			// get the list and add a new item to it, based on the newly-generated resource		
			$list = $list->getById($listId);
			$item = $list->addItem($resource,$position);
			$item->setData('is_published',true);
			$item->save();
			
			// write message that add was successful
			$this->writeMessage("Item \"{$resource->getTitle()}\" was sucessfully added to the list \"{$list->getTitle()}\"","ok");
		
			// redirect back to the list
			$redirector = $this->_helper->getHelper("Redirector");
			if ($item->isList())
				$type='list';
			else
				$type='item';
			if ($params['position']!='ask') {
				$redirector->setPrependBase("");
  			return $redirector->gotoUrl($this->view->url(array('controller'=>'list','action'=>'view','id'=>$this->params['id'],'token'=>null,'metatron'=>null,'resourceid'=>null,'type'=>null))."#".$type."_".$item->getId());
			} else {
			  return $this->_helper->redirector('insertitem','list', null, array('id' => $params['id'], $type.'id'=>$item->getId()));
			}
			
		}
	}
	



/*	
	public function itemselectedAction(){
		$params = $this->getRequest()->getParams();
		$defaultNamespace = new Zend_Session_Namespace('Default');

		$counter = $this->_getParam("counter");
		$itemVar = "item".$counter;
		$chosen_item = $defaultNamespace->$itemVar;

		//clear the session
		$defaultNamespace->resourceData = null;

		$resource = List8D_Model_Resource::getResource($chosen_item);
		
		//get a list of each of the metatrons to get additional metadata
		$conf = $this->getInvokeArg('bootstrap')->getApplication()->getOptions();
		$metatronList = $conf['list8d']['metatrons'];
		
		foreach($metatronList as $metatron){
			$ns = call_user_func(array($metatron, "getNamespace"));
			$metatronInstance = new $metatron($conf['list8d'][$ns]);
			$data = $resource->getData();
			$classHint = $data['classHint'];
			$primaryKey = $data[$ns."_primaryKey"];
			$newData = $metatronInstance->getAdditionalMetadata($classHint, $data);
			$resource->setAdditionalMetadata($newData);
		}
		
		$list = new List8D_Model_List();
		$list = $list->getById($params['id']);
		$list->addItem($resource);

		return $this->_helper->redirector('view','list', null, array('id' => $params['id']));
		
	}
	*/
    /*
	public function deleteitemAction(){
		$params = $this->getRequest()->getParams();
		$list = new List8D_Model_List();
		$list = $list->getById($params['id']);
		$item = new List8D_Model_Item();
		$item = $item->getById($this->_getParam("item_id"));
		$list->removeItem($item);
	//	$list->save();
		return $this->_helper->redirector('view','list', null, array('id' => $params['id']));
		
	}*/
	public function duplicateAction() {
		
		$user = new List8D_Model_User();
		$currentUser = $user->getCurrentUser();
		
		// load list
		$list = new List8D_Model_List();
		$list = $list->getById($this->params['id']);
		
		//since the same view used for all the duplicate actions
		$this->view->theAction = "duplicate";
		
		// check list exists
		if (!($list instanceof List8D_Model_List))
			throw new Zend_Controller_Action_Exception('List with id '.$params['id'].' could not be found.',404);
	
		// check user has permission to edit	
		if ((!$currentUser->isAllowed($list,'edit-tagged') && !$currentUser->isAllowed('list','edit')) || $this->isReadOnly($list,$currentUser)) 
			throw new Zend_Controller_Action_Exception('You do not have permission to edit list with id '.$this->params['id'].'.',403);
	
		// no item to copy: render list of items you can copy
		if (!isset($this->params['copyitem']) && !isset($this->params['copylist'])) {
	
			// set template name to some thing a little simplier
			$this->_helper->getHelper('viewRenderer')->setViewScriptPathSpec('duplicate-item-select-item.:suffix');
	
			// set layout title
			$this->layoutView->title = "Duplicate item on list \"{$list->getTitle()}\"";		
		
			// quick load the list as we're going to render out items
			$list->quickLoad();
		
			// load list into the view
			$this->view->list = $list;
		
		} 
		// we have item to copy, so lets copy and redirect
		else {
		
			if (isset($this->params['copyitem'])) {
				$item = new List8D_Model_Item();
				$itemId = $this->params['copyitem'];
				$type = "Item";
			} elseif (isset($this->params['copylist'])) {
				$item = new List8D_Model_List();
				$itemId = $this->params['copylist'];
				$type = "List";
			} else {
				throw new Zend_Controller_Action_Exception('Page not found.',404);
			}
		
			$item = $item->getById($itemId);
		
			// check item exists
			if (!($item instanceof List8D_Model_List) && !($item instanceof List8D_Model_Item))
				throw new Zend_Controller_Action_Exception("$type with id '{$itemId}' could not be found.",404);
	
			// get our destination
			if (isset($this->params['destination']))
				$destination = $this->params['destination'];
			// if one isn't set redirect back the root list
			else 
				$destination = $this->view->url(array('controller'=>'list','action'=>'view','id'=>$item->getTrunkId(),'copyitem'=>null,'copylist'=>null));
		
			// we could be here for a while
			set_time_limit(600);
		
			if ($duplicate = $item->duplicate()) {
				$duplicate->setPosition($item->getList()->getLength()+1);
				$duplicate->setListId($item->getTrunkId());
				$duplicate->save(true);
				$anchor = strtolower($type)."_".$duplicate->getId();
			} else {
				$anchor = "";
			}
		
			return $this->_helper->getHelper('redirector')->gotoUrl($destination.'#'.$anchor,array('prependBase'=>false));
		
		}
		
	}
	
	public function duplicatetolistAction() {
		
		$user = new List8D_Model_User();
		$currentUser = $user->getCurrentUser();
		$this->view->user = $currentUser;
		$this->view->recentLists = $currentUser->getRecentLists();
		$this->view->myLists = $currentUser->getMyLists();
		
		//since the same view used for all the duplicate actions
		$this->view->theAction = "duplicatetolist";
		
		// load list
		$list = new List8D_Model_List();
		$list = $list->getById($this->params['id']);
		
		// check list exists
		if (!($list instanceof List8D_Model_List))
			throw new Zend_Controller_Action_Exception('List with id '.$params['id'].' could not be found.',404);
		
		// check user has permission to edit	
		if (!$currentUser->isAllowed($list,'edit-tagged') && !$currentUser->isAllowed('list','edit')) 
			throw new Zend_Controller_Action_Exception('You do not have permission to edit list with id '.$this->params['id'].'.',403);
		
		// no item to copy: render list of items you can copy
		if (!isset($this->params['copyitem']) && !isset($this->params['copylist'])) {

			// set template name to some thing a little simplier
			$this->_helper->getHelper('viewRenderer')->setViewScriptPathSpec('duplicate-item-select-item.:suffix');
		
			// set layout title
			$this->layoutView->title = "Select the item you wish to copy";		
			
			// quick load the list as we're going to render out items
			$list->quickLoad();
			
			// load list into the view
			$this->view->list = $list;
			
		} elseif (!isset($this->params['tolist'])) {

			// set template name to some thing a little simplier
			$this->_helper->getHelper('viewRenderer')->setViewScriptPathSpec('duplicate-item-select-list.:suffix');
		
			// set layout title
			$this->layoutView->title = "Select the list you wish to copy it to";
			
			if (isset($this->params['copyitem'])) {
				$item = new List8D_Model_Item();
				$itemId = $this->params['copyitem'];
				$type = "Item";
			} elseif (isset($this->params['copylist'])) {
				$item = new List8D_Model_List();
				$itemId = $this->params['copylist'];
				$type = "List";
			} else {
				throw new Zend_Controller_Action_Exception('Page not found.',404);
			}
			
			$item = $item->getById($itemId);
			
			// check item exists
			if (!($item instanceof List8D_Model_List) && !($item instanceof List8D_Model_Item))
				throw new Zend_Controller_Action_Exception("$type with id '{$itemId}' could not be found.",404);
				
			// load search form
			if ($currentUser->isAllowed("list",'search')) {
				$searchForm = new List8D_Form_TitleSearchLists();
				$searchForm->setAction(""); 
				$searchForm->setMethod("post");
				
				// We've searched for a list
				if ($this->getRequest()->isPost() && $searchForm->isValid($this->getRequest()->getPost())) {
				
					$post = $this->getRequest()->getPost();
					$this->view->searchedFor = $post['q'];
					
					$listPeer = new List8D_Model_List();		
					$dbResults = $listPeer->findByData(array('code'=>$post['q'],'title'=>$post['q'],),true,1,true);
					
					// remove lists user can't edit
					$searchResults = array();
					
					foreach ($dbResults as &$result) {
						if ($currentUser->isAllowed($result,'edit')) {
							$searchResults[] = $result;
						}
						if (count($searchResults)>10)
							break;
					}

					
					$this->view->searchResults = $searchResults;
				}
				
				
				$this->view->searchForm = $searchForm;
			}
			
			
				
			$this->view->item = $item;
			
		}
		// we have item to copy and a list to copy to, so lets copy and redirect
		else {

			if (isset($this->params['copyitem'])) {
				$item = new List8D_Model_Item();
				$itemId = $this->params['copyitem'];
				$type = "Item";
			} elseif (isset($this->params['copylist'])) {
				$item = new List8D_Model_List();
				$itemId = $this->params['copylist'];
				$type = "List";
			} else {
				throw new Zend_Controller_Action_Exception('Page not found.',404);
			}
			
			$item = $item->getById($itemId);
			// check item exists
			if (!($item instanceof List8D_Model_List) && !($item instanceof List8D_Model_Item))
				throw new Zend_Controller_Action_Exception("$type with id '{$itemId}' could not be found.",404);
			
			// get the desitnation list
			$destinationList = new List8D_Model_List();
			$destinationList = $destinationList->getById($this->params['tolist']);
			// check list exists
			if (!$destinationList instanceof List8D_Model_List)
				throw new Zend_Controller_Action_Exception("List with id '{$this->params['tolist']}' could not be found.",404);
			
			if($this->isReadOnly($destinationList,$currentUser)){
				throw new Zend_Controller_Action_Exception("Destination List is Read Only", 403);
			}
			
			$destination = $this->view->url(array('controller'=>'list','action'=>'view','id'=>$this->params['tolist'],'copyitem'=>null,'copylist'=>null,'tolist'=>null));
			
			// we could be here for a while
			set_time_limit(600);
			if ($duplicate = $item->duplicate()) {
				$duplicate->setPosition($destinationList->getLength()+1);
				$duplicate->setListId($destinationList->getId());
				$duplicate->save(true);
				$anchor = strtolower($type)."_".$duplicate->getId();
			} else {
				$anchor = "";
			}
			
			return $this->_helper->getHelper('redirector')->gotoUrl($destination.'#'.$anchor,array('prependBase'=>false));
			
		}
	}

	public function duplicatecontentsAction() {

		$user = new List8D_Model_User();
		$currentUser = $user->getCurrentUser();
		$this->view->user = $currentUser;
		$this->view->recentLists = $currentUser->getRecentLists();
		$this->view->myLists = $currentUser->getMyLists();
		
		//since the same view used for all the duplicate actions
		$this->view->theAction = "duplicatecontents";

		// load list
		$list = new List8D_Model_List();
		$list = $list->getById($this->params['id']);

		// check list exists
		if (!($list instanceof List8D_Model_List))
			throw new Zend_Controller_Action_Exception('List with id '.$params['id'].' could not be found.',404);

		// check user has permission to edit
		if (!$currentUser->isAllowed($list,'edit-tagged') && !$currentUser->isAllowed('list','edit'))
			throw new Zend_Controller_Action_Exception('You do not have permission to edit list with id '.$this->params['id'].'.',403);

		//select destination list
		if (!isset($this->params['tolist'])) {

			// set template name to some thing a little simplier
			$this->_helper->getHelper('viewRenderer')->setViewScriptPathSpec('duplicate-item-select-list.:suffix');

			// set layout title
			$this->layoutView->title = "Select the list you wish to copy the contents to";

			// load search form
			if ($currentUser->isAllowed("list",'search')) {
				$searchForm = new List8D_Form_TitleSearchLists();
				$searchForm->setAction("");

				// We've searched for a list
				if ($this->getRequest()->isPost() && $searchForm->isValid($this->getRequest()->getPost())) {

					$post = $this->getRequest()->getPost();
					$this->view->searchedFor = $post['search_list_by_title'];

					$listPeer = new List8D_Model_List();
					$dbResults = $listPeer->findByData(array('code'=>$post['search_list_by_title'],'title'=>$post['search_list_by_title'],),true,1,true);

					// remove lists user can't edit
					$searchResults = array();

					foreach ($dbResults as &$result) {
						if ($currentUser->isAllowed($result,'edit')) {
							$searchResults[] = $result;
						}
						if (count($searchResults)>10)
							break;
					}


					$this->view->searchResults = $searchResults;
				}


				$this->view->searchForm = $searchForm;
			}



			$this->view->item = $list;

		}
		// we have list to copy and a list to copy to, so lets copy and redirect
		else {

			// get the desitnation list
			$destinationList = new List8D_Model_List();
			$destinationList = $destinationList->getById($this->params['tolist']);
			// check list exists
			if (!$destinationList instanceof List8D_Model_List)
				throw new Zend_Controller_Action_Exception("List with id '{$this->params['tolist']}' could not be found.",404);
				
			if($this->isReadOnly($destinationList,$currentUser)){
				throw new Zend_Controller_Action_Exception("Destination list is read-only",403);
			}

			$destination = $this->view->url(array('controller'=>'list','action'=>'view','id'=>$this->params['tolist'],'copyitem'=>null,'copylist'=>null,'tolist'=>null));

			//pre_dump(count($destinationList->getChildren()));
			
			// we could be here for a while
			set_time_limit(600);
			$position = $destinationList->getLength()+1;
			echo "<pre>Position = {$position}</pre>";
			$first = true;
			foreach($list->getChildren() as $item) {

				if($duplicate = $item->duplicate()) {
					$duplicate->setPosition($position);
					$duplicate->setListId($destinationList->getId());
					$duplicate->save(true);

					if($first) {
						$first = false;
						if($duplicate instanceof List8D_Model_Item)
							$type = "item";
						elseif($duplicate instanceof List8D_Model_List)
							$type = "list";
						$anchor = strtolower($type)."_".$duplicate->getId();
					}

					echo "<pre>position set to: {$position}</pre>";
					$position++;
				}
			}
			//exit;
			return $this->_helper->getHelper('redirector')->gotoUrl($destination.'#'.$anchor,array('prependBase'=>false));

		}
	}


	/**
	 * this function will take an existing list and duplicate it, it will then redirect the
	 * user to the list edit page where they are able to modify the lists details
	 */
	public function duplicatetonewAction() {


		$user = new List8D_Model_User();
		$currentUser = $user->getCurrentUser();
		$this->view->user = $currentUser;
		$this->view->recentLists = $currentUser->getRecentLists();
		$this->view->myLists = $currentUser->getMyLists();
		
		//since the same view used for all the duplicate actions
		$this->view->theAction = "duplicatetonew";
		
		// load list
		$list = new List8D_Model_List();
		$list = $list->getById($this->params['id']);

		// check list exists
		if (!($list instanceof List8D_Model_List))
			throw new Zend_Controller_Action_Exception('List with id '.$params['id'].' could not be found.',404);

		// check user has permission to edit
		if (!$currentUser->isAllowed('list','create'))
			throw new Zend_Controller_Action_Exception('You do not have permission to create lists.',403);

		//offer confirmation screen.
		if(!$this->getRequest()->isPost()) {
			//is a get - first step.
			$this->_helper->getHelper('viewRenderer')->setViewScriptPathSpec('confirm.:suffix');
			$this->view->form = new Zend_Form();
			$this->view->form->setMethod('post')->addElement('submit', 'Duplicate');
			$this->layoutView->title = "Are you sure you want to duplicate {$list->getTitle()} to a new list?";

		} else {
			//its a post.
			//do duplications
			
			$id = $this->params['id'];

			if($duplicate = $list->duplicate()) {
				$duplicate->save($duplicate, true);

				//duplicate tags..?
				$tags = $list->getTagIds();
				foreach($tags as $tag) {
					$tagmap = new List8D_Model_TagMap();
					$tagmap->setTagId($tag)
						->setListId($duplicate->getId())
						->save();
				}
				//exit;

				$id=$duplicate->getId();
			}

			$url =  $this->_helper->getHelper('url')->simple('edit', 'data', null, array('listid'=>$id))
							. "?destination="
							. $this->_helper->getHelper('url')->simple('view', 'list', null, array('id'=>$id));

			return $this->_helper->getHelper('redirector')->gotoUrl($url, array('prependBase'=>false));
		}


	}

	public function moveitemAction($insertingNew=false) {
		
		
		// set template name to some thing a little simplier
		$this->_helper->getHelper('viewRenderer')->setViewScriptPathSpec('move-item.:suffix');

		$params = $this->getRequest()->getParams();

		$list = new List8D_Model_List();
		$list = $list->getById($params['id']);
		$list->quickLoad();
		if (isset($params['itemid'])) {
			$item = new List8D_Model_Item();
			$item = $item->getById($params['itemid']);
			$id = $params['itemid'];
			$type = 'Item';
		} else {
			$item = new List8D_Model_List();
			$item = $item->getById($params['listid']);
			$id = $params['listid'];
			$type = 'List (item)';
		}
		
		$user = new List8D_Model_User();
		$currentUser = $user->getCurrentUser();
		
				
		if (!($list instanceof List8D_Model_List))
			throw new Zend_Controller_Action_Exception('List with id '.$params['id'].' could not be found.',404);
		
		if ((!($item instanceof List8D_Model_List) && $type == 'List (item)') || (!($item instanceof List8D_Model_Item) && $type == 'Item'))
			throw new Zend_Controller_Action_Exception($type.' with id '.$id.' could not be found.',404);
		
		if ((!$currentUser->isAllowed($list,'edit-tagged') && !$currentUser->isAllowed('list','edit'))  || $this->isReadOnly($list,$currentUser)) 
			throw new Zend_Controller_Action_Exception('You do not have permission to edit list with id '.$params['id'].'.',403);
		
		// set layout title
		if ($insertingNew) {
    		$this->layoutView->title = "Add item \"{$item->getTitle()}\" on the list \"{$list->getTitle()}\"";
            $this->view->insertingNew = true;
        } else {
    		$this->layoutView->title = "Move item \"{$item->getTitle()}\" on the list \"{$list->getTitle()}\"";
    		$this->view->insertingNew=false;
        }
		$this->view->currentPosition = $item->getOrder();
		$this->view->currentListId = $item->getListId();
		$this->view->moving = $item;
		$this->view->list = $list;

		if (isset($params['view'])) {
			$this->view->view = $params['view'];
		} else {
			if (count($list->getChildren())>200) {
				$this->view->view = "small";	
			} else if (count($list->getChildren())>75) {
				$this->view->view = "medium";	
			} else {
				$this->view->view = "large";	
			}
		}
	}

	public function insertitemAction() {
	   	
	   	return $this->moveitemAction(true);
	    
	}
	
	public function moveitemtoAction() {
		
		$params = $this->getRequest()->getParams();

		$toPosition = $params['position'];

		$toList = $params['onlist'];

		if (isset($params['itemid'])) {
			$moveItem = $params['itemid'];
			$itemPeer = new List8D_Model_Item;
			$item = $itemPeer->getById($moveItem);
			$id = $params['itemid'];
			$type = 'Item';
		} else if (isset($params['listid'])) {
				$moveList = $params['listid'];
				$itemPeer = new List8D_Model_List;
				$item = $itemPeer->getById($moveList);
			$id = $params['listid'];
			$type = 'List(item)';
		}
	
		$user = new List8D_Model_User();
		$currentUser = $user->getCurrentUser();
			
		if ((!($item instanceof List8D_Model_List) && $type = 'List(item)') || (!($item instanceof List8D_Model_Item) && $type = 'Item'))
			throw new Zend_Controller_Action_Exception($type.' with id '.$id.' could not be found.',404);
		
		if ((!$currentUser->isAllowed($item->getList(),'edit-tagged') && !$currentUser->isAllowed('list','edit')) || $this->isReadOnly($list,$currentUser)) 
			throw new Zend_Controller_Action_Exception('You do not have permission to edit list with id '.$params['id'].'.',403);
		
		$item->moveTo($toPosition,$toList);
		$params2 = array('id' => $params['id']);
		if (isset($params['view']))
			$params2['view'] = $params['view'];
		$this->_helper->redirector('view', 'list', null, $params2);
	}

	public function createAction() {
		
		$user = new List8D_Model_User();
		$currentUser = $user->getCurrentUser();
					
		if (!$currentUser->isAllowed('list','create')) 
			throw new Zend_Controller_Action_Exception('You do not have permission to edit list with id '.$params['id'].'.',403);
			
		$this->layoutView->title = 'Create new reading list';
		$form = new List8D_Form_List();
		$request = $this->getRequest();
		if ($request->isPost()) {
			if ($form->isValid($request->getPost())) {
			//return $this->_forward('searchresults');
				$list = new List8D_Model_List();
				$list->dataLoaded = true;
				$values = $form->getValues();
				$list->setTitle($values['title']);
				$list->setCode($values['code']);
				$list->setData("year", $values['year']);
				$list->setIsPublished($values['is_published']);
				$list->setData("department", $values['department']);
				$list->setStart($values['start']);
				$list->setEnd($values['end']);
				$list->setData("public_notes", $values['public_notes']);
				$list->setData("private_notes", $values['private_notes']);
				$list->save(true);
				return $this->_helper->getHelper('redirector')->gotoSimple('view','list',null,array('id'=>$list->getId()));
			}
		}

		$this->view->viewClass = get_class($this->view);
		$this->view->form = $form;

	}

	public function searchwidgetAction() {


		$this->view->searchForm = new List8D_Form_TitleSearchLists();

	}

	public function manualpublishAction() {
	
		$user = new List8D_Model_User();
		$currentUser = $user->getCurrentUser();
		
		$list = new List8D_Model_List();
		
		if (!($list instanceof List8D_Model_List))
			// Some "not found" condition occurs.
			throw new Zend_Controller_Action_Exception('Page not found.',404);

		if (!$currentUser->isAllowed($list,'edit') || $this->isReadOnly($list,$currentUser)) 
			throw new Zend_Controller_Action_Exception('You do not have permission to view list with id '.$params['id'].'.',403);
	
		$list = $list->getById($this->params['id']);
		$list->getData();
		$list->setData('was_auto_published',false);
	
		$list->save();
		$this->flashMessenger->addMessage(array(
		  "type"=>"ok",
		  "title"=>"List published",
		  "description"=>"<p>List was successfully published.</p>",
		  'autoclose'=>3000
		));
		$redirector = $this->_helper->getHelper("Redirector");
		$redirector->setPrependBase("");
    	return $redirector->gotoUrl($this->view->url(array('controller'=>'list','action'=>'view','id'=>$list->getId())));
	
	}
	
	public function sortAction() {
	   
		$params = $this->getRequest()->getParams();
		
		$list = new List8D_Model_List();
		$list = $list->getById($params['id']);
		
		$user = new List8D_Model_User();
		$currentUser = $user->getCurrentUser();
		
		if (!($list instanceof List8D_Model_List))
			throw new Zend_Controller_Action_Exception('List with id '.$params['id'].' could not be found.',404);
			
		if (!$currentUser->isAllowed($list,'edit-tagged') && !$currentUser->isAllowed('list','edit')) 
			throw new Zend_Controller_Action_Exception('You do not have permission to edit list with id '.$params['id'].'.',403);
		
		if(!empty($params['by'])) 
		  $by = $params['by'];
        else 
            $by = 'title';
        
        if(!empty($params['direction'])) 
		  $dir = $params['direction'];
        else 
            $dir = 'asc';
        
        if(!empty($params['recursive'])) 
		  $recursive = $params['recursive'];
        else 
            $recursive = false;
        
		$list->sort(true,$by,$dir,$recursive);
		$list->save();
		return $this->_helper->redirector('view','list', null, array('id' => $params['id']));
		
	}
	
	public function addtagAction() {
		
		$this->viewRenderer->setViewScriptPathSpec('list-add-tag.:suffix');
		$this->layoutView->title = "Add tag to list";
		$tag = new List8D_Model_Tag();
	
		$list = new List8D_Model_List();
		$list = $list->getById($this->params['id']);
		
		$user = new List8D_Model_User();
		$currentUser = $user->getCurrentUser();
	
				
		if (!$list) 
			throw new Zend_Controller_Action_Exception("List with id {$this->params['id']} could not be found",404); 
	
		if ((!$currentUser->isAllowed($list,'tag-tagged') && !$currentUser->isAllowed('list','edit')) || $this->isReadOnly($list,$currentUser)) 
			throw new Zend_Controller_Action_Exception('You do not have permission to tag list with id '.$params['id'].'.',403);
	
		// we havent chosen a tag yet so display them
		if (!isset($this->params['tagid'])) {
			$this->view->tags = $tag->getRoots();
		} else {
			$tag = $tag->find($this->params['tagid']);
			if (!$tag) 
				throw new Zend_Controller_Action_Exception("Tag with id {$this->params['tagid']} could not be found",404); 
		
			$tagmap = new List8D_Model_TagMap();
			$tagmap->setTagId($tag->getId())
				->setListId($list->getId())
				->save();
			if (isset($this->params['destination'])) {
			  return $this->_helper->getHelper('redirector')->gotoUrl($this->params['destination'],array('prependBase'=>false));
			} else
			  return $this->_helper->getHelper('redirector')->gotoSimple('view','list',null,array('id'=>$list->getId()));
			   
		}
	}
}