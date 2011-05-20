<?php

class List8D_Theme_Root_Helper_RenderLists extends List8D_ViewHelper
{
    public function renderLists($listOfLists,$options=array())
    {
    	$output=array();
    	foreach($listOfLists as $list) {
    		$text = $list->getTitle();
    		$output[] = array(
    			'value'=>$list->getTitle(),
    			'options'=>array(
    				'link'=>array(
    					'href'=>array(
    				  	'controller'=>'list',
    				  	'action'=>'view',
    				  	'id'=>$list->getId(),
    					)
    				)
    			)
    		);
    	}
    	
    	return $this->view->ul($output,$options);
    
    }
}