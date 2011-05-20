<?php
/**
 * Description of Url
 *
 */
class List8D_Model_Resource_Url extends List8D_Model_Resource {

	protected $_typeName = "URL";
	protected $_type = "Url";
	protected $_expanded = "link to a web page/online resource";
	
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
	
	
	function findByUrl($url) {
	   $this->getMapper()->findByUrl($url);
	   return $this;
	}
	
	public function getSearchForm() {
        
        $form = new List8D_Form();
        
	    $form->addElement('text', 'url', array(
	        'label' => 'Web address:',
	        'required' => true,
	        'filters' => array('StringTrim'),
	    ));
	    
	    return $form;
	   
	}
	
	function getType() {
		return "Url";
	}
	
}