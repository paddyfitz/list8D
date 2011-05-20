<?php 

class List8D_Model_Metatron_Handler {

    protected $_metatrons; 
    
    function __contruct() {
    
    }
    
    public function getApplication() {
        global $application;
        return $application;
    }
    
    public function listMetatrons() {
    
        $conf = $this->getApplication()->getOptions();
        $metatrons = $conf['list8d']['metatrons'];
        $output = array();
        foreach($metatrons as $metatron) {
            $output[$metatron] = call_user_func(array($metatron, "getNamespace"));
        }
        return $output;

    }
    
    public function getMetatrons($type=NULL) {

        if(empty($this->_metatrons)) {

            foreach($this->listMetatrons() as $class => $namespace) {
                $conf = $this->getApplication()->getOptions();
                $this->_metatrons[$class] = new $class();    
            }
        }

        $return = array();
        
        if (isset($type)) {
        	foreach ($this->_metatrons as $metatron) {
        	
        		$types = $metatron->getTypes();

        		if (!empty($types[$type])) {
        			$return[] = $metatron;
        		}
        	}
        } else {
        	$return = $this->_metatrons;
        }
        return $return;
        
    }
    
    public function findResources($q,$type = NULL,$page = NULL) {
        
        $output = array();
            
        foreach($this->getMetatrons() as $metatron) {

            try {
            	
            	$typemap = $metatron->getTypes();

	            if (isset($typemap[$type])) {
  	            $output[get_class($metatron)] = $metatron->findResources($q,$type,$page);
    	        }
    	        
    	      } catch (Exception $e) {
    	      	
    	      }
            
        }
				
        return $output;
        
    }
    
	public function getUniqueKeys($type) {
		
		$return = array();
		foreach($this->getMetatrons($type) as $metatron) {
			$unique = $metatron->getUniqueKeys($type);

			if(!empty($unique)) {
				if (is_array($unique)) {
					$return += $unique;
				} else {
					$return[] = $unique;
				}
			}
		}

		return $return;
		
	}
	
	public function loadMetadata($resource,$overwrite=false) {

		$metatrons = $this->getMetatrons($resource->getType());
		if ($overwrite)
			$metatrons = array_reverse($metatrons);
		foreach($metatrons as $metatron) {
		  $result = $metatron->loadMetadata($resource,$overwrite);
		}
		
	}
        
    /**
	 * Fetches the resource associated with the incoming Metatron data
	 *
	 * @param $metatronData - an array of metatron data to search on
	 * @param $class - if blank, creates a new resource. If populated, fetches a resource of the appropriate extending class
	 * @return resource
	 */
	public function buildResource($tempResource, $class = null,$overwrite=false){
				
		// if $tempResource doesn't exist 
		if(!$tempResource->findExisting()){

			$resource = $tempResource;
			$resource->loadMetadata($overwrite);
			$resource->save();
			
		}
		// if it exists load and return existing
		else{
			$resource = $tempResource->findExisting();
		}

		//return
		return $resource;
	}
	
	public function addMetatronSearchFields(&$form,$resource) {
		
		$metatrons = array_reverse($this->getMetatrons($resource->getType()));
		
		foreach($metatrons as $metatron) {
			
			if(method_exists($metatron,'addSearchFields')) {
				
				$form = $metatron->addSearchFields($form);
				
			}
			
		}
		
		return $form;
		
	}
	
	
	public function setRecordPage($limit) {
               
        foreach($this->getMetatrons() as $metatron) {
			
            try {
            	//currently only works for Voyager
            	if($metatron instanceof List8D_Model_Metatron_Voyager){
					$metatron->setRecordPage($limit);
    	        }
    	        
    	      } catch (Exception $e) {
    	      	
    	      }
            
        }
        
    }
               /*
 $session = new Zend_Session_Namespace('Default');
				//$session->addItemSearchResults = null;
				$session->theResults = null;

				$resourceDatas = array();
				$resourceDatasMerged = array();

				//get the resource type
				$resourceType = $form->getValue("type");
				$session->resourceType = $resourceType;

				//ask each of the metatrons for data, if they support the selected type

				foreach($metatronList as $metatron) {

					$metatronConf = null;
					$ns = call_user_func(array($metatron, "getNamespace"));
					if (isset($conf['list8d'][$ns])) {
						$metatronConf = $conf['list8d'][$ns];
					}

					$metatronInstance = new $metatron($metatronConf);

					//get the types that the metatron will deal with
					$types = $metatronInstance->getClasses();

					foreach($types as $type) {

						if ($type == $resourceType) {
							if($resourceType == "List8D_Model_Url") {
								$resourceData[0] = $metatronInstance->getMetadata($form->getValue("keyword"));

							}
							else {
								$resourceData = $metatronInstance->findResources($form->getValue("keyword"), $type);
							}
							//$resource = List8D_Model_Resource::getResource($resourceData);
							$resourceDatas[$metatron] = $resourceData;
						//	if(!empty($resourceData))
						//	$resourceDatasMerged = array_merge($resourceDatasMerged,$resourceData);

						}
					}

				}
    }
*/

}