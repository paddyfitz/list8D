<?php

class List8D_Model_Resource_Handler {
    
    public function getApplication() {
        global $application;
        return $application;
    }
    
    public function getTypeClass($type) {
    	//echo $type;exit;
		$types = $this->getResourceTypes();
    	if (!empty($types[$type])) {
    		return $types[$type];
    	} else {
    		return false;
    	}
    }
    
    public function getResourceTypes() {
    
      $conf = $this->getApplication()->getOptions();
      $resourceTypesClasses = $conf['list8d']['resourcetypes'];

			$output = array();
			foreach ($resourceTypesClasses as $class) {
			    $t = new $class;
			    $output[$t->getType()] = $t;    
			}
			return $output;
			
    }
   
    public function getResourceType($t) {
        $types = $this->getResourceTypes();
        if(!empty($types[$t])) {
            return $types[$t];
        } else if(!empty($types[strtolower($t)])) {
            return $types[strtolower($t)];
        } else {
            return false;
        }
    }
}
