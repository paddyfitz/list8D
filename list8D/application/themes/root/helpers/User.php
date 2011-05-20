<?php

class List8D_Theme_Root_Helper_User extends List8D_ViewHelper {
	
    
    public function user($user_id) {
			$user = new List8D_Model_User();
			$currentUser = $user->getCurrentUser();
			$user->getById($user_id);
			$return = "";
			//if($currentUser->isAllowed('user','view')) 
			//	$return .= "<a href='{$this->view->url(array('controller'=>'user','action'=>'view','id'=>$user_id))}'>";
			$return .= "{$user->getDisplayName()}";
			//if($currentUser->isAllowed('user','view')) 
			//	$return .= "</a>";
			return $return;
		}	
		
}