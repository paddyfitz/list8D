<?php
/**
 * Description of Kitten
 *
 */
class List8D_Model_Resource_Kitten extends List8D_Model_Resource {

	protected $_typeName = "Kitten";
	protected $_type = "Kitten";
	protected $_expanded = "Search for a Kitten";
	
	public $_data = array(
	  'title' => array(
	  	"title"=>"Title",
	  	"type"=>"text",
	  ),
	  'url'=>array(
	  	'title'=>"URL",
	  	'type'=>'link',		
	  ),
	  
	);
	
	protected $_unique = array(
		0 => 'url',
	);
	
	
	
	function __construct() {
		parent::__construct();
		$_data['url'] = "";
	}
	
	function getTitle() {
		return $this->getData('title', true);
	}
	
	
	function findByKitten($url) {
	   $this->getMapper()->findByKitten($url);
	   return $this;
	}
	
	public function getSearchForm() {
        
        $form = new List8D_Form();
        
	    $form->addElement('text', 'kittensearch', array(
	        'label' => 'Kitten Search:',
	        'required' => true,
	        'filters' => array('StringTrim'),
	    ));
	    
	    return $form;
	   
	}
	
	function getType() {
		return "Kitten";
	}
	
}
