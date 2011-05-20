<?php
/**
 * Description of Journal
 *
 */
class List8D_Model_Resource_LegacyJournal extends List8D_Model_Resource {

	protected $_typeName = "Legacy Journal";
	protected $_type = "LegacyJournal";
	
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
		'edition' => array(
	  	"title"=>"Edition",
	  	"type"=>"text",
	  ),
		'publication_date' => array(
	  	"title"=>"Publication date",
	  	"type"=>"text",
	  ),
		'journal_title' => array(
	  	"title"=>"Journal",
	  	"type"=>"text",
	  ),
		'volume' => array(
	  	"title"=>"Volume",
	  	"type"=>"text",
	  ),
		'issue' => array(
	  	"title"=>"Issue",
	  	"type"=>"text",
	  ),
	  'url'=>array(
	  	'title'=>'Links to electronic resources',
	  	'type'=>'link',
	  ),
	  'meta_url'=>array(
	  	'title'=>'Links to external data pages',
	  	'type'=>'link',
	  ),
	);
	
	function getTitle() {
		return $this->getData('title');
	}
		
	function getType() {
		return "LegacyJournal";
	}
	
	function useTypeReference() {
		return "Journal";
	}
}