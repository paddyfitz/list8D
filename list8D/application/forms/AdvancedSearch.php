<?php
	class List8D_Form_AdvancedSearch extends List8D_Form {
	
		public function init() {
		
			$currentUser = new List8D_Model_User;
			$currentUser->getCurrentUser();
			
			global $application;
			$options = $application->getOptions();
			$currentYear = $options['front']['currentYear'];
		
			$this->setMethod('get');
			
			// Code
      $this->addElement('text', 'code', array(
      	'label'      => 'Module code:',
      	'required'   => false,
      	'filters'    => array('StringTrim'),
      	'validators' => array(
      		array('validator' => 'StringLength', 'options' => array(0, 500)),
      	),
      ));
      
			// Title
      $this->addElement('text', 'title', array(
      	'label'      => 'Module title:',
      	'required'   => false,
      	'filters'    => array('StringTrim'),
      	'validators' => array(
      		array('validator' => 'StringLength', 'options' => array(0, 500)),
      	),
      ));
			
			// convener
		  $this->addElement('text', 'convener', array(
				'label' => 'Convener:',
				'required' => false,
				'filters' => array('StringTrim'),
				'validators' => array(
        	array('validator' => 'StringLength', 'options' => array(0, 128))
     		)
     	));
     	
			$themeSettings = List8D_Config_Ini::getThemeSettings();
     	$showFilters = $this->getFilters();
			foreach (explode(",",$showFilters) as $namespace) {
				
				$tagPeer = new List8D_Model_Tag();
				$tags = array(''=>"<Select $namespace>");
	     	foreach ($tagPeer->fetchByNamespace($namespace) as $tag) {
	     		$tags[$tag->getId()] = $tag->getTagName();
	     	}
				$select = $this->addElement('select', "filter_$namespace", array(
					'label' => "$namespace:",
					'required' => false,
					'multiOptions'=>$tags,
	     	));
	     	
			}
			
			$listPeer = new List8D_Model_List();
			$years = array(''=>"All years");
	  	foreach ($listPeer->getAllYears() as $year) {
	  		if ($year<=$currentYear  || $currentUser->getRole() != "guest" ) 
    	  	$years[$year] = $year."/".((substr($year,2)+1<10) ? "0".(substr($year,2)+1) : substr($year,2)+1);    
	  	}
			$select = $this->addElement('select', "year", array(
			  'label' => "Year:",
			  'required' => false,
			  'multiOptions'=>$years,
			  'value'=>$currentYear
	  	));
	  	     	
     	$this->addElement('submit', 'submit', array(
      	'ignore'   => true,
        'label'    => 'Search',
      ));
		
		}
		
		public function getFilters() {
			$themeSettings = List8D_Config_Ini::getThemeSettings();
			return $themeSettings->front->variables->filters;
		}
	
	}