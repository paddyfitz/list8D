<?php
/**
 * Description of Journal
 *
 */
class List8D_Model_Resource_Legacy extends List8D_Model_Resource {

	protected $_typeName = "Book";
	protected $_type = "Legacy";
	
	public $_data = array(
		'title' => array(
	  	"title"=>"Title",
	  	"type"=>"text",
	  ),
		'authors' => array(
	  	"title"=>"Authors",
	  	"type"=>"text",
	  ),
		'edition' => array(
	  	"title"=>"Edition",
	  	"type"=>"text",
	  ),
		'publisher' => array(
	  	"title"=>"Publisher",
	  	"type"=>"text",
	  ),
		'publication_date' => array(
	  	"title"=>"Publication date",
	  	"type"=>"text",
	  ),
	  'meta_url'=>array(
	  	'title'=>'Links to external data pages',
	  	'type'=>'link_array',
	  ),
	);
	
	function getTitle() {
		return $this->getData('title');
	}
		
	function getType() {
		return "Legacy";
	}
	
}