<?php
/**
 * Description of Book
 *
 * @author list8d
 */
class List8D_Model_Resource_Book extends List8D_Model_Resource {
	
	protected $_typeName = "Book";
	protected $_type = "Book";
	protected $_expanded = "book or chapter";
		
	public $_data = array(
	  'isbn' => array(
	  	"title"=>"ISBN",
	  	"type"=>"text",
	  	"validators"=>array(
	  		array('regex', false, array('messages'=>'Not a valid ISBN, must 10 or 13 integers.', '/^([0-9]{10}|[0-9]{13})$/')),
	  	),
	  ),
	  'title'=>array(
	  	'title'=>"Title",
	  	'type'=>'text',
	  	'required'=>true,		
	  ),
	  'authors'=>array(
	  	'title'=>'Authors',
	  	'type'=>'text',
	  ),
	  'edition'=>array(
	  	'title'=>'Edition',
	  	'type'=>'text',
	  ),
	  'publisher'=>array(
	  	'title'=>'Publisher',
	  	'type'=>'text',
	  ),
	  'publication_date'=>array(
	  	'title'=>'Publication date',
	  	'type'=>'text',
	  	"validators"=>array(
	  		array('regex', false, array('messages'=>'Dates should be in the format YYYY-MM-DD, YYYY-MM or YYYY', '/^([0-9]{4})(-[0-9]{1,2})?(-[0-9]{1,2})?$/')),
	  	),
	  ),
	  'publisher'=>array(
	  	'title'=>'Publisher',
	  	'type'=>'text',
	  ),
	  'classmark'=>array(
	  	'title'=>'Classmark',
	  	'type'=>'text',
	  ),
	  'meta_url'=>array(
	  	'title'=>'Links to external data pages',
	  	'type'=>'link',
	  ),
	);
	
	protected $_unique = array(
		0 => 'isbn10',
	);
	
	function __construct() {

		parent::__construct();

	}
	
	/**
	 * Returns an array of metadata organised into subarrays. If namespace is also given, returns metadata pertaining to that metatron only
	 *
	 * @param string $namespace - nullable. If set, only returns metadata pertaining to the metatron owning the namespace
	 * @param boolean $returnValue - defaults to false, if given, returns that specific entry pertaining to the metatron owning the namespace given in $namespace
	 * @return array of metadata
	 *
	function getData($namespace = null,$returnValue=false) {

		//if there's nothing in the _data array, fetch from the db
		if(!$this->dataLoaded){

			//fetch
			$this->getMapper()->getData($this);
			
			foreach ($this->_data as $key => $structure) {

				if (isset($this->_dataValues[$key])) {
				
					$this->_data[$key]['value'] = $this->_dataValues[$key];
				} else {
					$this->_data[$key]['value'] = null;
				}
			}
			
			if(empty($this->_data['isbn']['value'])) {
				$this->_data['isbn']['value'] = "";
				if (!empty($this->_data['isbn10']['value'])) 
					$this->_data['isbn']['value'] .= $this->_data['isbn10']['value'];	
				if (!empty($this->_data['isbn10']['value']) && !empty($this->_data['isbn13']['value'])) 
					$this->_data['isbn']['value'] .= ", ";
				if (!empty($this->_data['isbn13']['value']))
					$this->_data['isbn']['value'] .= $this->_data['isbn13']['value'];
			}

		}
		$data = $this->_data;
		//return from the _data array
		if ($namespace==null) {
			return $this->_data;
		} else if (isset($data[$namespace])) {
			if ($returnValue) {	
				if (!empty($this->_data[$namespace]['value']))
					return $this->_data[$namespace]['value'];			
				else 
					return false;			
			} else {
				return $this->_data[$namespace];
			}
		} else {
			return false;
		}
	}*/
	
	function getTitle() {
		return $this->getData('title',true);
	}
	
	
	function getType() {
		if ($this->partSet) 
			return "BookChapter";
		else
			return "Book";
	}
	
	function getTypeName() {
		if ($this->partSet) 
			return "Book";
		else
			return "Book";
	}
	
	function getAuthors() {
		return $this->getData("authors",true);
	}
	
	public function getSearchForm() {
        
      $form = new List8D_Form();
        
	    // Set the method for the display form to POST
      $form->setMethod('post');
      	$form->addElement('text', 'author', array(
	        'label' => 'Author:',
	        'required' => false,
	        'filters' => array('StringTrim'),
	    ));
		$form->addElement('text', 'title', array(
	        'label' => 'Title:',
	        'required' => false,
	        'filters' => array('StringTrim'),
	    ));
	    $form->addElement('text', 'keyword', array(
	        'label' => 'Keywords:',
	        'required' => false,
	        'filters' => array('StringTrim'),
	    ));       
	    
	    $form->addElement('text', 'isbn', array(
	        'label' => 'ISBN:',
	        'required' => false,
	        'filters' => array('StringTrim'),
	    ));
	    
	    $form->addElement('text', 'publication_date', array(
	        'label' => 'Publication date:',
	        'required' => false,
	        'filters' => array('StringTrim'),
	    ));
	    
	    
	    $this->addMetatronSearchFields($form);
	    
	    return $form;
	   
	}
	
}