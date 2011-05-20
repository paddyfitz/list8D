<?php

class ItemController extends List8D_Controller
{

    public function init() {
						parent::init();
		}

    public function indexAction()
    {
    }

    public function viewAction()
    {
    
    	$this->viewRenderer->setViewScriptPathSpec('item.:suffix');
    	
			$user = new List8D_Model_User();
			$currentUser = $user->getCurrentUser();
			
      $params = $this->getRequest()->getParams();
     	$this->view->readOnly = false;

			// get the item
			if (isset($params['id'])) {
				$item = new List8D_Model_Item();
				$item = $item->getById($params['id']);
				$type='item';
				$id = $params['id'];
				$listPeer = new List8D_Model_List;
				$list = $listPeer->getById($item->getListId());
				if($this->isReadOnly($list,$currentUser)){
					$this->view->readOnly = true;
				}
			} elseif (isset($params['listid'])) {
				$item = new List8D_Model_List();
				$item = $item->getById($params['listid']);	
				$type='list';
				$id = $params['listid'];
				if($this->isReadOnly($item,$currentUser)){
					$this->view->readOnly = true;
				}	
	    		$this->viewRenderer->setViewScriptPathSpec('item-list.:suffix');	
			}
			
			if (!($item instanceof List8D_Model_Item) && !($item instanceof List8D_Model_NestedList))
				// Some "not found" condition occurs.
				throw new Zend_Controller_Action_Exception("Could not find $type with id $id",404);
				
				$this->view->item = $item;
				
				//$this->layoutView->title = $item->getTitle();
				
			
			if (!$currentUser->isAllowed($item->getTrunk(),'edit-tagged') && !$currentUser->isAllowed('list','edit')) 
				throw new Zend_Controller_Action_Exception('You do not have permission to view this item.',403);
		}
    
      
    public function removeAction() {
    
		$user = new List8D_Model_User();
		$currentUser = $user->getCurrentUser();

    	$params = $this->getRequest()->getParams();

    	if (isset($params['itemid'])) {
    	  $moveItem = $params['itemid'];
    	  $itemPeer = new List8D_Model_Item;
	      $item = $itemPeer->getById($moveItem);
	      $type="item";
	      $id = $params['itemid'];
		  if(isset($params['id'])){
			$listPeer = new List8D_Model_List;
			$list = $listPeer->getById($params['id']);
			if($this->isReadOnly($list,$currentUser)){
				throw new Zend_Controller_Action_Exception('You do not have permission to edit this list.',403);
			}
		  }
	    } else if (isset($params['listid'])) {
    	  $moveList = $params['listid'];
	      $itemPeer = new List8D_Model_List;
	      $item = $itemPeer->getById($moveList);
	      $type="list";
	      $id = $params['listid'];
		  //need parent list:
		  //$parentListPeer = new List8D_Model_List;
		  //$parentList = $parentListPeer->getById($item->getListId());
		  //if($this->isReadOnly($parentList,$currentUser)){
		  if($this->isReadOnly($item,$currentUser)){
			throw new Zend_Controller_Action_Exception('You do not have permission to edit this list.',403);
		  }
	    }
			
			if (!($item instanceof List8D_Model_Item) && !($item instanceof List8D_Model_List) && !($item instanceof List8D_Model_NestedList))
				// Some "not found" condition occurs.
				throw new Zend_Controller_Action_Exception("Could not find {$type} with id '{$id}'.",404);
			if (!$currentUser->isAllowed($item->getList(),'edit-tagged') && !$currentUser->isAllowed('list','edit')) 
				throw new Zend_Controller_Action_Exception('You do not have permission to edit this list.',403);
			
			$this->layoutView->title = "Removing $type \"{$item->getTitle()}\" from the list \"{$item->getList()->getTitle()}\"";
			$this->view->item = $item;
				
    	if (!isset($params['confirmed'])) {
				
				$this->viewRenderer->setViewScriptPathSpec('remove-item-confirm.:suffix');
				
    	} else {
				$item->delete();
				return $this->_helper->getHelper('Redirector')->setGotoRoute(array('controller'=>'list','action'=>'view','id'=>$params['id'],'itemid'=>null));
				
    	}
    	
		}
		
}