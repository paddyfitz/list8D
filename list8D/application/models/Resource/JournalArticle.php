<?php
/**
 * Description of Journal
 *
 */
class List8D_Model_Resource_JournalArticle extends List8D_Model_Resource_Journal {

	protected $_typeName = "Article";
	protected $_expanded = "link to an online journal article";
	
	//protected $_typeName = "journal";
	protected $_unique = array(
		0 => 'doi',
	);

	private $_SSMap = array(
		'issn' => 'rft.issn',
		'title' => 'rft.atitle',
		'authors' => 'rft.au',
		'year' => 'rft.date',
		'journal' => 'rft.title',
		'volume' => 'rft.volume',
		'issue' => 'rft.issue',
		'doi' => 'SS_doi',
	);

	public $_data = array(
	  'issn' => array(
	  	"title"=>"ISBN",
	  	"type"=>"text",
	  ),
	  'title'=>array(
	  	'title'=>"Title",
	  	'type'=>'text',
	  ),
	  'authors'=>array(
	  	'title'=>'Author',
	  	'type'=>'text',
	  ),
	  'year'=>array(
	  	'title'=>'Year',
	  	'type'=>'text',
	  ),
	  'journal'=>array(
	  	'title'=>'Journal',
	  	'type'=>'text',
	  ),
	  'volume'=>array(
	  	'title'=>'Volume',
	  	'type'=>'text',
	  ),
	  'issue'=>array(
	  	'title'=>'Issue',
	  	'type'=>'text',
	  ),
	  'doi'=>array(
	  	'title'=>'DOI',
	  	'type'=>'text',
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

	public function findExisting() {

		if( $this->_data['doi']['value'] === "" ) {
			return false;
		} else {
			$uniques = $this->getUniqueKeys();

		$data = array();

		foreach($uniques as $unique) {
			if ($this->getDataValue($unique))
				$data[$unique] = $this->getDataValue($unique);
		}

		if (empty($data))
			return false;

		return $this->findByData($data);
		}

	}

}
