<?php

class FrontController extends List8D_Controller {

	
	public function indexAction() {
		$this->_helper->layout->setLayout('blank');
	  $this->viewRenderer->setViewScriptPathSpec('home.:suffix');
	  $this->view->searchForm = new List8D_Form_TitleSearchLists();	 
	  $this->view->advancedSearchForm = new List8D_Form_AdvancedSearch();
	  if (isset($this->params['q']))
		  $this->view->q = $this->params['q'];
  }
   
  public function advancedsearchAction() {
  	
  	$this->viewRenderer->setViewScriptPathSpec('advanced-search.:suffix');
		$this->layoutView->title = "Advanced search";
		$this->view->advancedSearchForm = new List8D_Form_AdvancedSearch;
  	$this->view->advancedSearchForm->setAction("search");
  }
   
  public function searchAction() {
		$this->params['q'] = mysql_real_escape_string($this->params['q']);
 		$showFilters = $this->view->filters;
	  $filters = array();
	  $this->view->filters = $filters;
	  
	  $user = new List8D_Model_User();
		$currentUser = $user->getCurrentUser();
		
		$db = Zend_Registry::get('dbResource');
		$db = $db->getDbAdapter();
		
		$listPeer = new List8D_Model_List();
		$post = $this->getRequest()->getPost();
    
		$this->viewRenderer->setViewScriptPathSpec('list-search-results.:suffix');
		 

		$this->layoutView->title = "List search results";
		
	  $tagPeer = new List8D_Model_Tag();
	  
		$joins = array();
		$wheres = array();
		
		global $application;
		$currentYear = $application->getOptions();
		$currentYear = $currentYear['front']['currentYear'];
		
		$this->view->searchedFor = array();
		
    	if (isset($this->params['q'])) {
		 
			$this->view->q = $this->params['q'];
			
			if (!empty($this->params['q']))
				$search = trim($this->params['q']);
			else if (!empty($post['q'])) 
				$search = trim($post['q']);
			else 
				$search = "";
	
			$this->view->searchedFor["title or code"]=$search;
	
			$this->view->tryAgain = new List8D_Form_TitleSearchLists();
	
			$this->view->tryAgain->getElement('q')->setValue($search);
	
			$table = $listPeer->getMapper()->getDbDataTable();
	
	  		$joins[] = "JOIN `list_data` AS year ON list.id = year.row_id AND `year`.`key` = 'year' AND `year`.`value` = '".serialize($currentYear)."'";
	  		$joins[] = "JOIN `list_data` AS q ON list.id = q.row_id AND ((q.`key`='code' AND q.`value`='".serialize($search)."') OR (q.`key`='title' AND q.`value` LIKE '%{$search}%'))";
	  	
	  	} else {
	  
			if (!empty($this->params['code'])) {
				$this->view->searchedFor["code"]=$this->params['code'];
	  		$joins[] = "JOIN `list_data` AS code ON `code`.`row_id` = `list`.`id` AND `code`.`key` = 'code' AND `code`.`value` = '".serialize($this->params['code'])."'";	
			}
			
			if (!empty($this->params['title'])) {
				$this->view->searchedFor["title"]=$this->params['title'];
	  		$joins[] = "JOIN `list_data` AS title ON `title`.`row_id` = `list`.`id` AND `title`.`key` = 'title' AND `title`.`value` LIKE '%".$this->params['title']."%'";
	  	}	
	  	
			if (!empty($this->params['year'])) {
				$this->view->searchedFor["year"]=$this->params['year'];
	  		$joins[] = "JOIN `list_data` AS year ON `year`.`row_id` = `list`.`id` AND `year`.`key` = 'year' AND `year`.`value` = '".serialize($this->params['year'])."'";
				
			} else {
	  		$joins[] = "JOIN `list_data` AS year ON `year`.`row_id` = `list`.`id` AND `year`.`key` = 'year' AND `year`.`value` = '".serialize($currentYear)."'";
			}
			if (!empty($this->params['convener'])) {
				$this->view->searchedFor["convener"]=$this->params['convener'];
			
				$userIds = $db->query("SELECT `id` FROM `user` WHERE `user`.`login` = '{$this->params['convener']}' OR `user`.`displayname` LIKE '%{$this->params['convener']}%'")->fetchAll();
				if (count($userIds)) {
		  		$join = "JOIN `list_data` AS convener ON `convener`.`row_id` = `list`.`id` AND convener.`key` = 'convener' AND (";
		  		$users=array();
	  			foreach($userIds as $userId) {
	  				$users[] = "convener.`value` LIKE '%".serialize($userId['id'])."%'";
		  		}
		  		$join .= implode(" OR ",$users);
		  		$join .= ")";
		  		
		  		$joins[] = $join;
		  	}
		}		
	}

    if (!empty($this->params['page']))
    	$this->view->page = $this->params['page'];
    else 
	    $this->view->page = 1;
	  
	  $filterQuery = array();  
	  $appliedFilters = array();
	  foreach($this->params as $key => $param) {
	    if (preg_match("/^filter_(.*)/",$key,$matches) && !empty($param)) {
		  	$tag = new List8D_Model_Tag();
		  	$tag = $tag->find($param);
	    	$this->view->searchedFor[$matches[1]] = $tag->getTagName();
	    	$appliedFilters[$matches[1]] = $tag->getTagName(); 
	    	$lists = $tagPeer->getListIdsWithChildTagOf($param);
	    	if (count($lists))
		  		$filterQuery[] = "`list`.id IN (".implode(",",$lists).")";
	    }
	  }
	  if (count($filterQuery))
		  $filterQuery = "(".implode(" AND ",$filterQuery).") AND";
		else {
			if (empty($search)) {
				$this->layoutView->title = "All lists";	
			}
			$filterQuery="";
	  }
	    
		$offset = ($this->view->page-1)*20;
		$joins = implode(" ",$joins);
		
		if (count($wheres))	
			$wheres = "(".implode(" AND ",$wheres).") AND";
		else
			$wheres = "";
			
		$query = "
			SELECT `list`.`id`
			FROM `list` 
			{$joins}
			WHERE {$wheres} {$filterQuery} 
			(`list`.`list_id` is null) 
			LIMIT 20 OFFSET {$offset}";
		
	
		$fullQuery = "
			SELECT `list`.`id`
			FROM `list` 
			{$joins}
			WHERE {$wheres} {$filterQuery} 
			(`list`.`list_id` is null) ";
		//echo $query.";<br/>";echo $fullQuery; exit;
		$result = array();
	
		$result = $db->query($query)->fetchAll();
		$fullResult = $db->query($fullQuery)->fetchAll();

		if (count($result)===1) 
			return $this->_helper->getHelper('Redirector')->gotoUrl($this->view->url(array('controller'=>'Front','action'=>'list','id' => $result[0]['id']),null,false,true,true));
		

		
		$return = array();
		foreach($result as $list) {
			$return[$list['id']] = $listPeer->getById($list['id']);
		}
		$this->view->searchResults = array_slice($return,0,20);
    	$this->view->searchCount = count($fullResult);
	  	$this->view->pages = ceil($this->view->searchCount/20);
	  
	  	$listIds = array();
	  	foreach ($fullResult as $list) {
	  		$listIds[] = $list['id'];
	  	}
	  
	  	if (count($listIds)) {
	  
	  		foreach (explode(",",$showFilters) as $namespace) {
	  	
	  			$filters[$namespace] = array();
	  		
				$tags = $tagPeer->getTagsWithListCount($namespace,$listIds,11);

		  		foreach(array_slice($tags,0,10,true) as $id => $tag) {
	    			$filters[$namespace][$id] = $tag;
	    		}
	    	
	    		if (count($tags)>10) {
	    			$filters[$namespace]['limit'] = true;
	    		} else {
	    			$filters[$namespace]['limit'] = false;
	    		}
	  		
	  		}
		}
		
		
	  
	  	$this->view->appliedFilters = $appliedFilters;
	  

	  	$this->view->filters = $filters;
    
  	}
  
	public function listAction() {
		
		$params = $this->getRequest()->getParams();
		$list = new List8D_Model_List();
		$listId = $params['id'];
		
		
		if(!isset($params['getBy']))
			$list = $list->getById($params['id']);
		else if (isset($params['year']))
			$list = $list->findByData(array($params['getBy']=>$params['id'],'year'=>$params['year']));
		else 
			$list = $list->findByData($params['getBy'],$params['id']);
		
		if (!($list instanceof List8D_Model_List))
			// Some "not found" condition occurs.
			throw new Zend_Controller_Action_Exception("List with {$params['getBy']} {$params['id']} not found",404);

		$list->quickLoad();
		
		if ($list->getDocumentUrls()) {
			
			$description = "<p>The reading list for this module has been submitted to the library, and is being reviewed for stock availability. The process of entering all the data into this system is therefore not yet complete.</p><p>
The following link" . (count($list->getDocumentUrls())>1 ? "s" : "") ." provide access to the working documentation held by the Library, and may provide additional up-to-date information.</p>";
			
			foreach ($list->getDocumentUrls() as $url => $text) {
				$description .= "<a style='clear:both' href='{$url}' class='download-button'>Download $text</a>";
			}
				
			$this->flashMessenger->addMessage(array(
				"type"=>"info",
				//"title"=>"This list is available as a downloadable document",
				"description"=>$description,
				'closable'=>false,
				'actions'=>array(
				
				),
			));
		} else
                //send moodle link
                if (!$list->getChildren()) {
                    $desc = "<p>Your module convenor/lecturer may have provided alternative information in <a href=\"https://moodle.kent.ac.uk\">Moodle</a>";
                    if ($list->getDataValue('convener')){
                        $desc .= " If not, please contact your course convener, ";
                        $user = new List8D_Model_User(); $i=0;
                        foreach($list->getDataValue('convener',false) as $convener) {
                            $i++; $user->find($convener);
                            $desc .= "<a href=\"mailto:".$user->getEmail()."\">".$user->getDisplayName()."</a>";
                            if ($i!=1 && $i!=count($list->getDataValue('convener',false)) && $i!=count($list->getDataValue('convener',false))-1) {
                                $desc .= ", ";
                            } else if ($i==count($list->getDataValue('convener',false))-1) {
                                $desc .= " or ";
                            }
                        }
                        $desc .= "</p>";
                    }
                    $this->flashMessenger->addMessage(array(
                        "type"=>"info",
                        "title"=>"This Reading List website does not have any details for this list.",
                        "description"=>$desc,
                        "closable"=>false,
                    ));
                } else
		// send auto publish message
		if ($list->getDataValue('was_auto_published')) {
			$this->flashMessenger->addMessage(array(
				"type"=>"info",
				"title"=>"This list has been automatically published",
				"description"=>"<p>This list has been automatically copied over from last year and the convener for this course has not yet confirmed it is the finalised list for this year. </p>",
				'closable'=>false,
			));
			
		}
		
    	// set template name to some thing a little simplier
		$this->viewRenderer->setViewScriptPathSpec('list.:suffix');

		// set layout title
		if ($list->getDataValue('code')) {
			$title = $list->getDataValue('code')." - ";
		} else {
			$title = "";
		}
		
		$this->layoutView->title = $title.$list->getTitle();
		$this->view->list = $list;
		$this->layoutView->list = $list;
		
		$this->view->inlineScript()->appendFile('print.js');
		

		/*// populate tag form
		$tagform = new List8D_Form_AddTagList();
		$tagform->setAction($this->view->baseUrl() . '/tag/addlist');
		$this->view->tagform = $tagform;

		$tagform->getElement("listid")->setValue($list->getId());*/

	}
	
	function browseAction() {
		
	  	$this->viewRenderer->setViewScriptPathSpec('browse.:suffix');
		$this->layoutView->title = "Browse for lists";
	  
	  	$tagPeer = new List8D_Model_Tag();
		$showFilters = $this->view->filters;
	  	$filters = array();
		foreach (explode(",",$showFilters) as $namespace) {
	  	
	    	$filters[$namespace] = array();
	    
	    	$tags = $tagPeer->getTagsWithListCount($namespace,false,26);

		  	foreach(array_slice($tags,0,25,true) as $id => $tag) {
	    		$filters[$namespace][$id] = $tag;
	    	}
	    
	    	if (count($tags)>20) {
	    		$filters[$namespace]['limit'] = true;
	    	} else {
	    		$filters[$namespace]['limit'] = false;
	    	}
	    
	  	}
	  	$this->view->filters = $filters;
		
	}
	
	function viewalltagsAction() {
	
		$this->viewRenderer->setViewScriptPathSpec('view-all-tags.:suffix');
		$this->layoutView->title = "Viewing all tags for {$this->params['namespace']}";
		
	  	$tagPeer = new List8D_Model_Tag();
	  	$tags = $tagPeer->getTagsWithListCount($this->params['namespace']);
		
		$this->view->tags = $tags;	
		$this->view->namespace = $this->params['namespace'];	
	}

}



