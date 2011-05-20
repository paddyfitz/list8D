<?php

class DataController extends List8D_Controller {
	
	public function toggleAction() {
		
		$user = new List8D_Model_User();
		$currentUser = $user->getCurrentUser();
		
		$params = $this->getRequest()->getParams();
		
		if (isset($params['itemid'])) {
			$item = new List8D_Model_Item();
			$item = $item->getById($params['itemid']);
			if (!($item instanceof List8D_Model_Item))
				throw new Zend_Controller_Action_Exception('Could not find item with id '.$params['itemid'],404);
		} elseif (isset($params['listid'])) {
			$item = new List8D_Model_List();
			$item = $item->getById($params['listid']);
		} else {
			throw new Zend_Controller_Action_Exception('Could not find page.',404);
		}
		
		if (!$currentUser->isAllowed($item->getList(),'edit-tagged') && !$currentUser->isAllowed('list','edit')) 
			throw new Zend_Controller_Action_Exception('You do not have permission to edit this list.',403);
		
		if ($item->getData($params['data'],true)) {
			$value = false;
		} else {
			$value = true;
		}

		$item->setData($params['data'],$value)->save();
		
		return $this->_redirect($this->view->getDestination(),array('prependBase'=>false));

		exit;
		
	}
	
	public function toggleresourceAction() {
		
		$user = new List8D_Model_User();
		$currentUser = $user->getCurrentUser();
		
		$resource = new List8D_Model_Resource();
		$resource = $resource->getById($this->params['resourceid']);
		
		if (!($resource instanceof List8D_Model_Resource))
			throw new Zend_Controller_Action_Exception('Could not find resource with id '.$params['resourceid'],404);
		
		if (!$currentUser->isAllowed('resource','edit')) 
			throw new Zend_Controller_Action_Exception('You do not have permission to edit this resource.',403);
			
		if ($resource->getData($this->params['data'],true)) {
			$value = false;
		} else {
			$value = true;
		}

		$resource->setData($this->params['data'],$value)->save();
		
		echo $this->_redirect($this->view->getDestination(),array('prependBase'=>false));
		
	}
	
	public function editAction() {
	
		$user = new List8D_Model_User();
		$currentUser = $user->getCurrentUser();
		
		$this->viewRenderer->setViewScriptPathSpec('edit-data.:suffix');
		
		$params = $this->getRequest()->getParams();
		
		// Load the item or list we are editing the data on
		if (isset($params['itemid'])) {
			$item = new List8D_Model_Item();
			$item = $item->getById($params['itemid']);
		} else {
			$item = new List8D_Model_List();
			$item = $item->getById($params['listid']);
		}
		
		if (!($item instanceof List8D_Model_Item) && !($item instanceof List8D_Model_List))
			throw new Zend_Controller_Action_Exception('Could not find item with id '.$params['itemid'],404);
		
		$readOnly = false;
		
		if (!$item->isList() || $item->isNestedList()) {
			$list = $item->getTrunk();
			$readOnly = $this->isReadOnly($list,$currentUser);
		} else {
			$list = $item;
			$readOnly = $this->isReadOnly($list,$currentUser);
		}
		
		if ((!$currentUser->isAllowed($list,'edit-tagged') && !$currentUser->isAllowed('list','edit')) || $readOnly) 
			throw new Zend_Controller_Action_Exception('You do not have permission to edit this list.',403);
		
		// If we are editing a single data item 
		if (isset($params['data'])) {
			
			$form = $item->getEditForm($params['data']);
			
		} else {
			
			$form = $item->getEditForm();
						
		}
		
		if (isset($this->params['then']) && $this->params['then'] == 'edit') {
		  $form->addElement('submit', 'searchagain', array(
		  	'ignore'   => true,
    		'label'    => 'Save and add another item',
		  ));
		}
		
		if ($this->getRequest()->isPost() && $form->isValid($_POST)) {
			
			foreach($form->getValues() as $key => $value) {

				if (($currentUser->isAllowed($item->getAccessResourceType().'-field-'.$key,'edit') || $currentUser->isAllowed($item->getAccessResourceType().'-fields','edit')) && ($currentUser->isAllowed($item,'edit-tagged') || $currentUser->isAllowed($item,'edit'))) {
					if(!is_array($value)){
						$item->setData($key,$value);
						//echo "Key: ".$key." Value: ".$value."<br />";
					}
					else{
						//if last item is blank, remove it...
						//pre_dump($value);
						foreach($value as $key2 => $value2){
							//echo "Key: ".$key2." Value: ".$value2."<br />";
							if($value2 == ""){
								array_pop($value);
							}
						}
						//pre_dump($value);
						//exit;
						$item->setData($key,$value);
						//foreach($value as $key2 => $value2){
						//	echo "Key: ".$key2." Value: ".$value2."<br />";
						//}
					}
					
				}
			}
			//exit;
			$item->save();
			if ($this->view->getDestination() == $this->view->url()) {
				$redirector = $this->_helper->getHelper("Redirector");
				$redirector->setPrependBase("");
				$values = $form->getValues();
				
				if($form->getElement('searchagain')){
					if ($form->getElement('searchagain')->isChecked()) {
						return $redirector->gotoUrl($this->view->url(array('controller'=>'list','action'=>'additem','id'=>$item->getTrunkId()),null,false,true,true));
					} else {
						return $redirector->gotoUrl($this->view->url(array('controller'=>'list','action'=>'view','id'=>$item->getTrunkId()),null,false,true,true)."#".$item->getAccessResourceType()."_".$item->getId());
					}
				}
				else{
					return $redirector->gotoUrl($this->view->url(array('controller'=>'list','action'=>'view','id'=>$item->getTrunkId()),null,false,true,true)."#".$item->getAccessResourceType()."_".$item->getId());
				}
			} else {
				$type = $item->isList() ? "list" : "item";
				return $this->_redirect($this->view->getDestination()."#{$type}_".$item->getId(),array('prependBase'=>false));
			}
			
		} else {
			
			$this->view->editDataForm = $form;
			
			$this->view->item = $item;
			
		}
		
	}	
}