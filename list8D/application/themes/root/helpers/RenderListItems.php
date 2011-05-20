<?php

class List8D_Theme_Root_Helper_RenderListItems extends List8D_ViewHelper
{
    public function renderListItems($list,$options=array())
    {
    	$this->view->headLink()->appendStylesheet('list-items.css'); 
        

        $listId=$list->getId();
        
    	$listItems = $list->getChildren();
            
			
    	$output=array();
    	
    	   $listTitle = $list->getTitle();
    	
    	foreach($listItems as $item) {
    
    		$text = $item->getTitle();
    		$outputItem = array(
    		  'options'=>array(
    		  	'attributes'=>array(
    		  		'class'=>array('sortable-item'),
    		  	)
    		  )
    		);
            
    		//$outputItem['value'] =  $this->view->a($item->getTitle(),array('controller'=>'list','action'=>'view','id'=>$item->getId()));
    		if ($item->isList())
    			$outputItem['value'] =  $item->getTitle();
    		else 
    			$outputItem['value'] =  $this->view->a($item->getTitle(),array('controller'=>'item','action'=>'view','id'=>$item->getId()));	
    			
    		if ($item->isList()) {
   		 		$outputItem['value'] .= "<br/>".$this->view->a("move \"{$item->getTitle()}\"",array('controller'=>'list','action'=>'moveitem','listid'=>$item->getId()),array('attributes'=>array('class'=>array('move'))));
    		} else {
    			$outputItem['value'] .= "<br/>".$this->view->a("move \"{$item->getTitle()}\"",array('controller'=>'list','action'=>'moveitem','itemid'=>$item->getId()),array('attributes'=>array('class'=>array('move'))));
    		}
    		
    		if ($item->isList()) {
   		 		$outputItem['value'] .= "<br/>".$this->view->a("remove \"{$item->getTitle()}\" from \"$listTitle\"",array('controller'=>'item','action'=>'remove','listid'=>$item->getId()));
    		} else {
    			$outputItem['value'] .= "<br/>".$this->view->a("remove \"{$item->getTitle()}\" from \"$listTitle\"",array('controller'=>'item','action'=>'remove','itemid'=>$item->getId()));
    		}
    		
    		if ($item->isList()) {
    			$branchOptions = $options;
    			$branchOptions['branch']=true;
    			$outputItem['value'] .= $this->view->renderListItems($item,$branchOptions);
    			$outputItem['options']['attributes']['class'][]='list';
					$outputItem['options']['attributes']['id']="list_".$item->getId();
    		} else {
    			$outputItem['options']['attributes']['class'][]='not-list';
					$outputItem['options']['attributes']['id']="item_".$item->getId();
    		}
            $outputItem['value'] .= "<br/>".$this->view->a("add item after \"{$item->getTitle()}\"",  array('controller'=>'list','action'=>"additem",'position'=>$item->getPosition()+1,'onlist'=>$item->getListId()));

    		$output[] = $outputItem;
    		
    	}
			
			if (empty($options['branch']) || !$options['branch']) {
				$options['attributes']['id'] = 'list-items';
			}
			
			$options['attributes']['class'][] = 'list-items';
        $output2 = "<br/>";	
        $output2 .= $this->view->a("add item to the start of \"{$this->view->list->getTitle()}\"",  array('controller'=>'list','action'=>"additem",'onlist'=>$listId,'position'=>0));
        $output2 .= "<br/>";
    	$output2 .= $this->view->ul($output,$options);
        $output2 .= "<br/>";
        $output2 .= $this->view->a("add item to the end of \"{$this->view->list->getTitle()}\"",  array('controller'=>'list','action'=>"additem",'onlist'=>$listId,'position'=>$list->getLength()));
        $output2 .= "<br/>";
    	
    	return $output2;
    
    }
}