<?php
/**
 * Description of Journal
 *
 */
class List8D_Model_Resource_Journal extends List8D_Model_Resource {

	protected $_typeName = "Journal";
	protected $_SerialsSolutionsJournals_groups = null;
	protected $_expanded = "link to a journal or article";
	
	//protected $_typeName = "journal";
	protected $_unique = array(
		0 => 'issn',
	);


	

	public $_data = array(
	  'issn' => array(
	  	"title"=>"ISSN",
	  	"type"=>"text",
	  ),
	  'title'=>array(
	  	'title'=>"Title",
	  	'type'=>'text',
	  ),
	  'authors'=>array(
	  	'title'=>'Publisher',
	  	'type'=>'text',
	  ),
	  'year'=>array(
	  	'title'=>'Year',
	  	'type'=>'text',
	  ),
	  'doi'=>array(
	  	'title'=>'DOI',
	  	'type'=>'text',
	  ),
	  'classmark'=>array(
	  	'title'=>'Classmark',
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
	
	function __construct() {

		parent::__construct();

		//$_data['issn'] = "";
		//$_data['title'] = "";
		//$_data['year'] = "";


	}
	
	function getTitle() {
		return $this->getData('title', true);
	}
	
	
	function getType() {
		return $this->_typeName;
	}

	public function getSearchForm() {

		$form = new List8D_Form();

		// Set the method for the display form to POST
		$form->setMethod('post');


		/*$form->addElement('text', 'atitle', array(
						'label' => 'Article title:',
						'required' => false,
						'filters' => array('StringTrim'),
			));
*/

		$form->addElement('text', 'title', array(
		  			'allowEmpty' => false,
		  			'label'      => 'Journal:',
		  			'filters' => array('StringTrim'),
		  			//'validators' => array(new List8D_Model_ValidateAcrossFields('atitle', 'Article title')),
		));

/*
		$form->addElement('text', 'title', array(
						'label' => 'Journal:',
						'required' => false,
						'filters' => array('StringTrim'),
			));
*/

		
/*$form->addElement('text', 'volume', array(
						'label' => 'Volume:',
						'required' => false,
						'filters' => array('StringTrim'),
			));

		$form->addElement('text', 'issue', array(
						'label' => 'Issue:',
						'required' => false,
						'filters' => array('StringTrim'),
			));


		$form->addElement('text', 'date', array(
						'label' => 'Date:',
						'required' => false,
						'filters' => array('StringTrim'),
			));
		

		$form->addElement('text', 'issn', array(
						'label' => 'ISSN:',
						'required' => false,
						'filters' => array('StringTrim'),
			));
*/
		$form->addElement('text', 'doi', array(
						'label' => 'DOI:',
						'required' => false,
						'filters' => array('StringTrim'),
			));

		/*
$form->addElement('text', 'aulast', array(
						'label' => 'Author (surname):',
						'required' => false,
						'filters' => array('StringTrim'),
			));

		$form->addElement('text', 'sdate', array(
						'label' => 'Year:',
						'required' => false,
						'filters' => array('StringTrim'),
			));
*/


		//need to add more.

		$form->setDescription(
			"<p>This service allows you to search for the full text of a known article.</p>" .
      "<p>You need to include at least an author name, article title and a journal title ".
			"(or ISSN, DOI, or PMID). For a direct link to an article (if available) you also ".
			"need the volume number, issue number and preferably the start page number.</p>"
		);

		return $form;

	}
	
	
	/**
	 * Get access to the array holding the groups for the journal.
	 * Commented out as data value now serialised so no need to do separatly
	public function getSSGroups($index = -1) {
		
		//cache the unseialize version
		if($this->_SerialsSolutionsJournals_groups === null) {
			$this->_SerialsSolutionsJournals_groups = unserialize($this->getDataValue('SerialsSolutionsJournals_groups'));
		}
		
		if( $index >= 0 ) {
			return $this->_SerialsSolutionsJournals_groups[$index];
		}

		return $this->_SerialsSolutionsJournals_groups;
		
	}

	/**
	 * Find out how many groups there are for this journal.
	 */
	public function countSSGroups() {
		return count($this->getSSGroups());
	}

		public function findExisting() {

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
