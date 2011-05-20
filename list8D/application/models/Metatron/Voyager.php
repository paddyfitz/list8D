<?php

class List8D_Model_Metatron_Voyager extends List8D_Model_Metatron {

	protected $_conn;
	protected $_records;

	protected $_maxrec = 10;
	
	protected $_supportedTypes = array (
	  'Book' => 'List8D_Model_Resource_Book',
	  'PhysicalMedia' => 'List8D_Model_Resource_PhysicalMedia',
	);
		
	protected $_unique = array(
		'Book' => array(
						'Voyager_rcn',
						'isbn10',
						),
		'PhysicalMedia' => 'Voyager_rcn',
	);
	/**
	 * Each metatron requires a unique namespace. We recommend this matches
	 * the class name. This will be referred to in the database, so changing
	 * it later on will cause pain.
	 *
	 * @return string Namespace for this metatron
	 */
	public static function getNamespace() {
		return "Voyager";
	}

	/**
	 * Each metatron should return a friendly name. This is used for display
	 * purposes only.
	 *
	 * @return string Friendly name
	 */
	public static function getName() {
		return "Voyager Library Catalogue";
	}

	/**
	 * Class constructor - set up important connection info here
	 */
	function __construct() {
		$this->_conn = yaz_connect("some.voyager.server:7090");
		yaz_syntax($this->_conn, "USMARC");
		yaz_database($this->_conn, "Voyager");
		yaz_range($this->_conn, 1, 10);
	}

	/**
	 * getMetadata should be used to return metadata for a specific item
	 * for which we hold a key - RCN, ISBN, etc.
	 *
	 * @param string $key should be a 'unique' identifier
	 * @param string $keyType - 'null' for rcn, 'ean' or 'isbn10' are also valid.
	 * @return array a single resourceData array (http://code.google.com/p/list8d/wiki/ResourceData)
	 */
	function getMetadata($key, $keyType=null) {

		
		if ($key == null) {
			throw new Exception ("Invalid Search Terms"); //TODO:LookupFailed
		}
		if ($keyType == null) {
			$query = "@attr 1=12 $key";
		} else if (($keyType == 'ean') || ($keyType == 'isbn10')) {
			$query = "@attr 1=7 $key";
		}

		yaz_search($this->_conn, "rpn", $query);
		yaz_wait();
		$error = yaz_error($this->_conn);
		if (!empty($error))
			throw new Exception ("Error: $error"); //TODO:ItemNotFound

		$hits = yaz_hits($this->_conn);

		if ($hits > 1)
			throw new Exception("Error: PrimaryKeyNotUniqueException"); //TODO:PKNUE

		if ($hits < 1)
			return null;

		require_once 'File/MARCXML.php';
		for ($p = 1; $p <= $hits; $p++) {
			$rec = yaz_record($this->_conn, $p, "xml;charset=marc-8,utf-8");
			if (empty($rec)) continue;

			$_tmprec = $this->formatData($rec);
		}
		return $_tmprec;
	}

	/**
	 * Take a MARCXML record and return a resourceData array.
	 * 
	 * @param string $rec MARCXML record.
	 * @return array ResourceData formatted metadata - http://code.google.com/p/list8d/wiki/ResourceData
	 */
	protected function formatData($rec) {
		$bib = new File_MARCXML($rec, File_MARC::SOURCE_STRING);
		while ($record = $bib->next()) {
			$_tmprec['namespace'] = $this->getNamespace();
			$_tmprec['primaryKey'] = $this->getRCN($record);
			$_tmprec['classHint'] = $this->getClassHint($record);
			$_tmprec['classmark'] = $this->getClassmark($record);
			$_tmprec['Voyager_rcn'] = $this->getRCN($record);
			$_tmprec['KentVoyager_title'] = $this->getTitle($record);
			$_tmprec['KentVoyager_title'] = $this->getTitle($record);
			$_tmprec['KentVoyager_authors'] = $this->getAuthor($record);
			$_tmprec['KentVoyager_edition'] = $this->getEdition($record);
			$_tmprec['KentVoyager_published'] = $this->getPublicationDate($record);
			$_tmprec['KentVoyager_publisher'] = $this->getPublisher($record);
			$_tmprec['KentVoyager_publication_location'] = $this->getPublicationLocation($record);
			$_tmprec['KentVoyager_description'] = $this->getDesc($record);
			$_tmprec['KentVoyager_url'] = $this->getCanonicalURL($record);
			$_tmprec['KentVoyager_ean'] = $this->getEAN($record);
			$_tmprec['KentVoyager_isbn10'] = $this->getISBN10($record);
      $_tmprec['KentVoyager_issn'] = $this->getISSN($record);
			$_tmprec['title'] = $_tmprec['KentVoyager_title'];
			$_tmprec['authors'] = $_tmprec['KentVoyager_authors'];
			$_tmprec['edition'] = $_tmprec['KentVoyager_edition'];
			$_tmprec['publication_date'] = $_tmprec['KentVoyager_published'];
			$_tmprec['publisher'] = $_tmprec['KentVoyager_publisher'];
			$_tmprec['publication_location'] = $_tmprec['KentVoyager_publication_location'];
			$_tmprec['description'] = $_tmprec['KentVoyager_description'];
			$_tmprec['meta_url'] = array('Voyager'=>array('text'=>'Library Catalogue','url'=>$_tmprec['KentVoyager_url']));
			$_tmprec['ean'] = $_tmprec['KentVoyager_ean'];
            $_tmprec['isbn10'] = $_tmprec['KentVoyager_isbn10'];
            $_tmprec['issn'] =  $_tmprec['KentVoyager_issn'];
			
		}

		foreach ($_tmprec as $tkey => $tval) {
			if ($tval == '')
				$_tmprec[$tkey] = null;
		}
		return $_tmprec;
	}

	/**
	 * Z39.50 keyword search formatter
	 *
	 * This function takes a keyword search string, passes it into
	 * the tokenizer and then formats as a Z39.50 search string.
	 * For example:
	 *
	 * - "roman art" walker = @and "roman art" "walker"
	 * - java barnes programming paperback = @and @and @and "java" "barnes" "programming" "paperback"
	 * - 0980455227 = "0980455227"
	 * 
	 * @return string valid Z39.50 search string
	 * @param string $string search string
	 */
	protected function zSearchString($terms) {
		
		$return = "";

		$loopRun = false;
		foreach($this->tokenizeQuoted($terms['keyword']) as $keyword) {
			if ($loopRun) {
				$return = "@and ".$return;
			} else {
				$loopRun = true;
			}	
			$return = $return." \"$keyword\"";	
		}
		
		unset($terms['keyword']);
		
		foreach($terms as $key => $term) {
			if (!empty($term) && $this->getBib1Attr($key)) {
				if ($loopRun) {
					$return = "@and ".$return;
				} else {
					$loopRun = true;
				}	
				$bib1 = $this->getBib1Attr($key);
				$return = $return." @attr 1=$bib1 \"$term\"";	
			}			
		}

		return $return;
	}
	

	/**
	 * String tokenizer - splits a string into an array, but is
	 * quote aware.
	 *
	 * @param string to be split
	 * @return array of string terms
	 */
	protected function tokenizeQuoted($string) {
		$tokens=array();
		for( $nextToken=strtok($string, ' '); $nextToken!==false; $nextToken=strtok(' ')) {
			if($nextToken{0}=='"')
				$nextToken = $nextToken{strlen($nextToken)-1}=='"' ?
					substr($nextToken, 1, -1) : substr($nextToken, 1) . ' ' . strtok('"');
			$tokens[] = $nextToken;
		}
		return $tokens;
	}

	/**
	 * Return additional metadata for a resource that we already know about.
	 * It is up to the List8D_Model_Resource object to do anything with this
	 * data. A null result should be returned if we do not hold any metadata
	 * for that item.
	 *
	 * @param string $_type object type hint
	 * @param array $_resource resourceData array
	 * @return array resourceData
	 */
	public function getAdditionalMetadata($_type, $_resource) {
		try {
			foreach ((array) $_resource as $_key => $_val) {
				switch ($_key) {
					case 'KentVoyager_primaryKey':
						return ($this->getMetadata($_val));
					case 'isbn10':
						return ($this->getMetadata($_val, "isbn10"));
					case 'ean':
						return ($this->getMetadata($_val, "ean"));
				}
			}
		} catch (Exception $e) {
			// Key wasn't unique, so we have no response for additional metadata
			// *BUT* we don't care - it's optional
			error_log("$e->getMessage()");
			return null;
		}
		return null;
	}

	/**
	 * find a list of resources by keyword search to allow the user to 
	 * select a specific item.
	 *
	 * @param string $terms is a keyword search string
	 * @param mixed $type should be a spcific List8D_Model_Resource - e.g.
	 *		List8D_Model_Resource_Book, or List8D_Model_PhysicalMedia.  Ideally this
	 *			is the only type of resource that should be returned.
	 *			The metatron can ignore it if this does not make any sense.
	 * @return array resourceData
	 */
	//function findResources($terms, $type,$max=null,$offset=1) {
	function findResources($terms, $type, $page=null, $max=null, $offset=1){
		
		if($page){
			$this->setRecordPage($page);
		}
		
		if ($terms == null) {
			throw new Exception ("Invalid Search Terms"); //TODO:LookupFailed
		}
		$searchstr = $this->zSearchString($terms);

		yaz_search($this->_conn, "rpn", $searchstr);
		yaz_wait();
		$error = yaz_error($this->_conn);
		if (!empty($error)) 
			throw new Exception ("Error: $error"); //TODO:ItemNotFound
		
		$hits = yaz_hits($this->_conn);

		require_once 'File/MARCXML.php';

		$upto = $hits;

		if ($hits > $this->_maxrec) {
			if ($max == null) {
				$upto = $this->_maxrec;
			} else {
				$upto = $max;
			}
		}
		
		
		$records = array();
		$empty = 0;
		for ($p = $offset; $p <= $upto; $p++) {
			$rec = yaz_record($this->_conn, $p, "xml;charset=marc-8,utf-8");
			$data = $this->formatData($rec);
			if (empty($rec)) {
				continue;
			} else if (empty($data['title'])) {
				$empty++;
				continue;
			}
			$records[] = $rec;
		}
				
		$return = array();
		foreach($records as $record) {
			$data = $this->formatData($record);
			$class = $data['classHint'];
			$resource = new $class();
			$resource->setDataByArray($data);
			$resource->dataLoaded = true;
			$return[] = $resource;
		}
		
		return $return;
	}

	/**
	 * Return the voyager RCN
	 * @param File_MARC_Record $_record MARCXML object
	 * @return string rcn
	 */
	protected function getRCN ($_record) {
		return $this->sfAsString($_record->getField('001'));
	}

	/**
	 * Return link to the opac
	 * @param File_MARC_Record $_record MARCXML object
	 * @return string url
	 */
	protected function getCanonicalURL ($_record) {
		return "http://some.voyager.server/cgi-bin/Pwebrecon.cgi?BBID=" . $this->getRCN($_record) .  "&SL=None&DB=local";
	}

	/**
	 * Return description
	 * @param File_MARC_Record $_record MARCXML object
	 * @return string description
	 */
	protected function getDesc ($_record) {
	// 300a
		return $this->sfAsString($_record->getField('300'), 'a');
	}

	/**
	 * Get a records classmark
	 * @param File_MARC_Record $_record MARCXML object
	 * @return string class mark
	 */
	protected function getClassmark ($_record) {
		// 50a
		return $this->sfAsString($_record->getField('050'), 'a');
	}
	/**
	 * Return a 'hint' as to what kind of resource this is
	 * @param File_MARC_Record $_record MARCXML object
	 * @return List8D_Model_Resource
	 */
	protected function getClassHint($_record) {
		$desc = $this->getDesc($_record);
		$journal = $this->sfAsString($_record->getField('362'));
		$classmark = $this->getClassmark($_record);

		if (strpos($desc, "DVD") !== false) {
			return "List8D_Model_Resource_PhysicalMedia";
		} else if (strpos($desc, "VHS") !== false || stripos($desc, "videocassette") !== false || stripos($desc, "video cassette") !== false ) {
			return "List8D_Model_Resource_PhysicalMedia";
		} else if (strpos($desc, "compact disc") !== false) {
			return "List8D_Model_Resource_PhysicalMedia";
		} else if ($this->getEAN($_record) != '') {
			return "List8D_Model_Resource_Book";
		} else if ($this->getISSN($_record) != '' || !empty($journal) || preg_match("/^W[AX]/",$classmark)) {
			return "List8D_Model_Resource_Journal";
		} else {
			return "List8D_Model_Resource_Book";
		}

	}

	/**
	 * Return title
	 * @param File_MARC_Record $_record MARCXML object
	 * @return string title
	 */
	protected function getTitle ($_record) {
	// return a, n, and p subfields for proper title.
		$a = $this->sfAsString($_record->getField('245'), 'a');
		$bnp = $this->sfAsString($_record->getField('245'), 'bnp');
		if(!empty($bnp))
			return $a . ": " . $bnp;
		return $a;
	}

	/**
	 * Return author
	 * @param File_MARC_Record $_record MARCXML object
	 * @return string author
	 */
	protected function getAuthor ($_record) {
	// any of the 100, 110, and 111 fields
		return $this->sfAsString($_record->getField('(100|110|111|700|710|711)', true));
	}

	/**
	 * Return edition
	 * @param File_MARC_Record $_record MARCXML object
	 * @return string edition
	 */
	protected function getEdition ($_record) {
	// 250a
		return $this->sfAsString($_record->getField('250'), 'a');
	}

	/**
	 * Return publication date
	 * @param File_MARC_Record $_record MARCXML object
	 * @return string publication date
	 */
	protected function getPublicationDate ($_record) {
	// 260c
		return $this->sfAsString($_record->getField('260'), 'c');
	}
	
	protected function getPublisher ($_record) {
		// 260ab
		return $this->sfAsArray($_record->getField('260'), 'b');
	}
	
	protected function getPublicationLocation ($_record) {
		// 260ab
		return $this->sfAsArray($_record->getField('260'), 'a');
	}
	
	/**
	 * Return EAN
	 * @param File_MARC_Record $_record MARCXML object
	 * @return string EAN
	 */
	protected function getEAN ($_record) {
	// This is ISBN13.
	// 20a
		$tisbn = $this->sfAsString($_record->getField('20'), 'a');
		$tisbn = preg_replace("/^([0-9Xx]+).*/", "$1", $tisbn);
		if ( strlen($tisbn) == 10 )
			$tisbn = $this->isbn10_to_13($tisbn);
		return $tisbn;
	}

	/**
	 * Return isbn10
	 * @param File_MARC_Record $_record MARCXML object
	 * @return string isbn10
	 */
	protected function getISBN10 ($_record) {
		$tisbn = $this->sfAsString($_record->getField('20'), 'a');
		$tisbn = preg_replace("/^([0-9Xx]+).*/", "$1", $tisbn);
		if ( strlen($tisbn) == 10 )
			return $tisbn;
		return null;
	}

	/**
	 * Return issn
	 * @param File_MARC_Record $_record MARCXML object
	 * @return string issn
	 */
	protected function getISSN ($_record) {
	// 22a
		return $this->sfAsString($_record->getField('22'), 'a');
	}

	/**
	 * Format specific MARC subfields as a string
	 *
	 * @param File_MARC_Field $_field - the MARC Field object
	 * @param string $_wanted - the subfields you want, for example: 'abn'
	 * @return string subfield contents
	 */
	protected function sfAsString ($_field, $_wanted = null) {
		
		return implode(" ",$this->sfAsArray($_field, $_wanted));
		
	}

	protected function sfAsArray ($_field, $_wanted = null) {
		if ($_field == null)
			return array();
		if ($_field->isControlField())
			return array($_field->getData());

		$_rtn = array();
		if ($_field->getSubfields() != false) {

			foreach ($_field->getSubfields() as $code => $value) {
				$sfdata = $value->getData();
				$sfdata = trim(preg_replace('/[^a-zA-Z0-9\)]$/', "", $sfdata));

				if (($_wanted == null) || (strpos($_wanted, $code) !== false))
					$_rtn[] = $sfdata;
			}
			return $_rtn;
		} else {
			return array(trim($_field));
		}
		return array();
	}


	/** 
	 * Generate checksum for a 13 digit ISBN
	 * 
	 * @param string $isbn
	 * @return string isbn13
	 */
	protected function genchksum13($isbn) {
		$isbn = trim($isbn);
		$tb = 0;
		for ($i = 0; $i <= 12; $i++) {
			$tc = substr($isbn, -1, 1);
			$isbn = substr($isbn, 0, -1);
			$ta = ($tc*3);
			$tci = substr($isbn, -1, 1);
			$isbn = substr($isbn, 0, -1);
			$tb = $tb + $ta + $tci;
		}
		$tg = ($tb / 10);
		$tint = intval($tg);
		if ($tint == $tg) { return 0; }
		$ts = substr($tg, -1, 1);
		$tsum = (10 - $ts);
		return $tsum;
	}

	/**
	 * Turn an ISBN10 into an ISBN13 - functionality thanks to
	 * http://www.linein.org/blog/2007/01/05/convert-isbn10-to-isbn13-with-php/
	 *
	 * @param string $isbn
	 * @return string $isbn
	 */
	protected function isbn10_to_13($isbn) {
		$isbn = trim($isbn);
		if(strlen($isbn) == 12) {
			$isbn13 = '0'.$isbn;}
		else {
			$isbn2 = substr("978" . trim($isbn), 0, -1);
			$sum13 = $this->genchksum13($isbn2);
			$isbn13 = "$isbn2$sum13";
		}
		return ($isbn13);
	}

	/**
	 * Return a list of classes that we would normally expect to support.
	 * This prevents this metatron being queried for types of resource that
	 * it should never find, such as a URL.
	 *
	 * @return List8D_Model_Resource
	 */
	public static function getClasses () {
		return array ("List8D_Model_Resource_Book",
		"List8D_Model_PhysicalMedia",
		"List8D_Model_Journal");
	}

	/**
	 * Each metatron should register a URI regexp match for future bookmarklet
	 * functionality.
	 *
	 * @return string URI regex match
	 */
	public static function registerUrl () {
		return "https?:\/\/some.opac.server\/.*";
	}

	/**
	 * Return an icon to represent the metatron
	 *
	 * @param string $_resolution - the requested image resolution
	 * @return string icon filename
	 */
	public static function getIcon($_resolution = null) {
		$_ns = self::getNamespace();
		if($_resolution <= 32) {
			return "{$_ns}_32.png";
		} else {
			return "{$_ns}_orig.png";
		}
	}
	
	public function getBib1Map() {
		return array(
			1=>'personal_name',
			2=>'corporate_name',
			3=>'conference_name',
			4=>'title',
			5=>'title_series',
			6=>'title_uniform',
			7=>'isbn',
			8=>'issn',
			9=>'lc_card_number',
			10=>'bnb_card_no',
			11=>'bgf_number',
			12=>'local_number',
			13=>'Dewey classification	13',
			14=>'udc_classification',
			15=>'bliss_classification',
			16=>'lc_call_number',
			17=>'nlm_call_number',
			18=>'nal_call_number',
			19=>'mos_call_number',
			20=>'local_classification',
			21=>'subject_heading',
			22=>'subject_rameau',
			23=>'bdi_index_subject',
			24=>'inspec_subject',
			25=>'mesh_subject',
			26=>'pa_subject',
			27=>'lc_subject_heading',
			28=>'rvm_subject_heading',
			29=>'local_subject_index',
			30=>'date',
			31=>'publication_date',
			32=>'date_of_acquisition',
			33=>'title_key',
			34=>'title_collective',
			35=>'title_parallel',
			36=>'title_cover',
			37=>'title_added_title_page',
			38=>'title_caption',
			39=>'title_running',
			40=>'title_spine',
			41=>'title_other_variant',
			42=>'title_former',
			43=>'title_abbreviated',
			44=>'title_expanded',
			45=>'subject_precis',
			46=>'subject_rswk',
			47=>'subject_subdivision',
			48=>'no_natl_biblio',
			49=>'no_legal_deposit',
			50=>'no_govt_pub',
			51=>'no_music_publisher',
			52=>'number_db',
			53=>'number_local_call',
			54=>'code_language',
			55=>'code_geographic_area',
			56=>'code_institution',
			57=>'name_and_title',
			58=>'name_geographic',
			59=>'place_publication',
			60=>'coden',
			61=>'microform_generation',
			62=>'abstract',
			63=>'note',
			100=>'author_title',
			1001=>'record_type',
			1002=>'name',
			1003=>'author',
			1004=>'author_name_personal',
			1005=>'author_name_corporate',
			1006=>'author_name_conference',
			1007=>'identifier_standard',
			1008=>'subject_lc_childrens',
			1009=>'subject_name_personal',
			1010=>'body_of_text',
			1011=>'date_time_added_to_db',
			1012=>'date_time_last_modified',
			1013=>'authority_format_id',
			1014=>'concept_text',
			1015=>'concept_reference',
			1016=>'any',
			1017=>'server_choice',
			1018=>'publisher',
			1019=>'record_source',
			1020=>'editor',
			1021=>'bib_level',
			1022=>'geographic_class',
			1023=>'indexed_by',
			1024=>'map_scale',
			1025=>'music_key',
			1026=>'related_periodical',
			1027=>'report_number',
			1028=>'stock_number',
			1030=>'thematic_number',
			1031=>'material_type',
			1032=>'doc_id',
			1033=>'host_item',
			1034=>'content_type',
			1035=>'anywhere',
			1036=>'author_title_subject',
			1223=>'edition',
		);
	}
	
	public function getBib1Attr($attr) {
		
		$map = array_flip($this->getBib1Map());		
		if (!empty($map[$attr])) {
			return $map[$attr];
		} else {
			return false;
		}
		
	}
	
	public function addSearchFields(&$form) {
		
		 $form->addElement('text', 'local_number', array(
	       'label' => 'Voyager id:',
	       'required' => false,
	       'filters' => array('StringTrim'),
	   ));
		
		return $form;
		
	}
	
	public function setRecordPage($value){
		$this->_maxrec = $value * 10;
	}
}
	