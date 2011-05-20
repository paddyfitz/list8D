<?php

class List8D_Theme_Root_Helper_RenderAddItem extends List8D_ViewHelper
{
  public function renderAddItem($listOfMetatronResources,$options=array())
  {
    $output=array();
    
    if ($options['merge metatrons']) {
    
      $listOfResources = array();
    	
    	foreach($listOfMetatronResources as $listOfMetatronResource) {
        $listOfResources = array_merge($listOfResources,$listOfMetatronResource);
      }
      
      foreach($listOfResources as $resource) {

        $output[] = array(
          'value'=>"add \"{$resource->getTitle()}\"",
     	    'options'=>array(
            'link'=>array(
              'href'=>array(
                'controller'=>'list',
     		  	    'action'=>'itemselected',
              ) 
     	      )
          )
        );
      }
      
    } else {
		foreach($listOfMetatronResources as $metatronName => $resources){
			
			if ($resources && count($resources) > 0) {
			
				$output[$metatronName]['value'] = "From: ".$ns = call_user_func(array($metatronName, "getName"));
			
				foreach($resources as $key => $resource){
				
					$output[$metatronName]['children'][] = array(
		  	          'value'=>"add \"{$resource->getDataValue('title')}\"",
		  	   	      'options'=>array(
		  	            'link'=>array(
		  	              'href'=>array(
		  	                'controller'=>'list',
		  	   		    	    'action'=>'additem',
		  	   		    	    'resourceid'=>$key,
									'metatron'=>$metatronName,
		  	              ) 
		  	   	        )
		  	          )
		  	        );
					
				}
			}
			
			//foreach($resources as $resource){
			//	pre_dump($output[$metatronName]);
				
			//}
			//exit;
		}
		//exit;
      //$i=0;
      /*foreach($listOfMetatronResources as $metatronName => $listOfResources) {
        
        $output[$metatronName]['value'] = "From: ".$ns = call_user_func(array($metatronName, "getName")); 
        foreach($listOfResources as $resource) {

          $output[$metatronName]['children'][] = array(
            'value'=>"add \"{$resource['title']}\"",
     	      'options'=>array(
              'link'=>array(
                'href'=>array(
                  'controller'=>'list',
     		    	    'action'=>'additem',
     		    	    'resourceid'=>$i,
						'metatron'=>$metatronName,
                ) 
     	        )
            )
          );
          //$i++;
        } 
        
      }*/
      
    }
    return $this->view->ul($output,$options);
    
  }
}