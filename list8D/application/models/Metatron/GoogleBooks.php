<?php
	
	class List8D_Model_Metatron_GoogleBooks extends List8D_Model_Metatron {
		
		private $_gbooks;
		
		protected $_supportedTypes  = array(
			'Book' => 'List8D_Model_Resource_Book',
		);
		
		protected $_unique = array(
			'Book' => array(
							'GoogleBooks_volumeId',
							'isbn10',
							),
		);
		
		/**
		 * Class constructor - set up important connection info here
		 */
		function __construct() {
			$this->_gbooks = new Zend_Gdata_Books();
		}

		/**
		 * Each metatron requires a unique namespace. We recommend this matches
		 * the class name. This will be referred to in the database, so changing
		 * it later on will cause pain.
		 *
		 * @return string Namespace for this metatron
		 */
		public static function getNamespace() {
			return "GoogleBooks";
		}
		
		/**
		 * Each metatron should return a friendly name. This is used for display
		 * purposes only.
		 *
		 * @return string Friendly name
		 */
		public static function getName() {
			return "Google Books";
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
		 * @param integer $page should select an offset
		 * @return array resourceData
		 */
		function findResources($terms, $type, $page=null, $max=null, $offset=1){
			$q = "";
	
			if (!empty($terms['keyword']))
				$q .= $terms['keyword'];
			if (!empty($terms['author']))
				$q .= " inauthor:".$terms['author'];
			if (!empty($terms['title']))
				$q .= " intitle:".$terms['title'];
			if (!empty($terms['isbn']))
				$q .= " isbn:".$terms['isbn'];
			if (!empty($terms['publication_date']))
				$q .= " published:".$terms['publication_date'];

			if ($q == null) {
				//throw new Exception ("Invalid Search Terms"); //TODO:LookupFailed
				return;
			}
			$query = $this->_gbooks->newVolumeQuery();
			$query->setQuery($q);
			if($page){
				$query->setStartIndex($page*10);
			}
			$feed = $this->_gbooks->getVolumeFeed($query);
			
			$_records = array();
			
			foreach ($feed as $entry) {
				$resource = new List8D_Model_Resource_Book();
				$resource->dataLoaded = true;
				$_records[] = $resource->setDataByArray($this->formatData($entry));
				
			}
			return $_records;
		}

		/**
		 * Take a MARCXML record and return a resourceData array.
		 *
		 * @param string $rec MARCXML record.
		 * @return array ResourceData formatted metadata - http://code.google.com/p/list8d/wiki/ResourceData
		 */
		private function formatData($_entry) {

			$_tmprec['primaryKey'] = $_entry->getVolumeId();
			$_tmprec['GoogleBooks_id'] = $_entry->getVolumeId();
			$_tmprec['namespace'] = $this->getNamespace();
			$_tmprec['GoogleBooks_title'] = $_entry->getTitle()->text;
			$_tmprec['GoogleBooks_url'] = array('google'=>array('text'=>'Google Books','url'=>"http://books.google.co.uk/books?id=" . $_entry->getVolumeId()));
			if($_entry->getThumbnailLink()){
				$_tmprec['GoogleBooks_thumbnailUrl'] = $_entry->getThumbnailLink()->href;
				$_tmprec['thumbnailUrl'] = $_tmprec['GoogleBooks_thumbnailUrl'];
			}
			$_tmprec['GoogleBooks_creators'] = implode(", ", $_entry->getCreators());
			$_tmprec['GoogleBooks_volumeId'] = $_entry->getVolumeId();
			$_tmprec['GoogleBooks_description'] = implode("\n ", $_entry->getDescriptions());

			$_tmprec['title'] = $_tmprec['GoogleBooks_title'];
			$_tmprec['meta_url'] = $_tmprec['GoogleBooks_url'];
			$dates = $_entry->getDates();
			$_tmprec['publication_date'] = isset( $dates[0] ) ? (string) $dates[0] : '';
			
			
			$_tmprec['authors'] = $_tmprec['GoogleBooks_creators'];
			$_tmprec['description'] = $_tmprec['GoogleBooks_description'];
			
			// Return ISBNs if they're present.
			$_idents = $_entry->getIdentifiers();
			foreach ($_idents as $ident) {
				if (strpos($ident->text, "ISBN:") !== FALSE) {
					$tmpisbn = str_replace("ISBN:", "",$ident->text);
					if (strlen($tmpisbn) == 13) {
						$_tmprec['ean'] = $tmpisbn;
					} else if (strlen($tmpisbn) == 10) {
						$_tmprec['isbn10'] = $tmpisbn;
					}
				}
			}
			
			if (!empty($_tmprec['isbn10']) && empty($_tmprec['ean']))
				$_tmprec['ean'] = $this->isbn10_to_13($tmprec);
			
			// trim empty data - no point passing back empty values
			foreach ($_tmprec as $tkey => $tval) {
				if ($tval == '')
					$_tmprec[$tkey] = null;
			}
			return $_tmprec;
		}

		/**
		 * getMetadata should be used to return metadata for a specific item
		 * for which we hold a key - RCN, ISBN, etc.
		 *
		 * @param string $key should be a 'unique' identifier.
		 * @return array a single resourceData array (http://code.google.com/p/list8d/wiki/ResourceData)
		 */
		function getMetadata($q) {
		
			/* Change to allow get by isbn
if ($q == null) {
				throw new Exception ("Invalid Search Terms"); //TODO:LookupFailed
			}

			return $this->formatData($this->_gbooks->getVolumeEntry($q));
*/
			$query = $this->_gbooks->newVolumeQuery();
			$query->setQuery($q);
			$feed = $this->_gbooks->getVolumeFeed($query);
			if(is_object($feed[0]))
				return $this->formatData($feed[0]);

		
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
						case 'GoogleBooks_primaryKey':
							return ($this->getMetadata($_val));
						case 'isbn10':
							$r = $this->findResources("isbn:" . $_val);
							if (count($r) != 1) {
								return null;
							} else {
								return $r[0];
							}
						case 'ean':
							$r = $this->findResources("isbn:" . $_val);
							if (count($r) != 1) {
								return null;
							} else {
								return $r[0];
							}
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
		 * Return a list of classes that we would normally expect to support.
		 * This prevents this metatron being queried for types of resource that
		 * it should never find, such as a URL.
		 *
		 * @return List8D_Model_Resource
		 */
		public static function getClasses () {
			return array ("List8D_Model_Resource_Book");
		}

		/**
		 * Each metatron should register a URL regexp match for future bookmarklet
		 * functionality.
		 *
		 * @return string URL regex match
		 */
		public static function registerUrl () {
			return "https?:\/\/opac.kent.ac.uk\/.*";
		}

		// isbn10 to 13 functionality thanks to
		// http://www.linein.org/blog/2007/01/05/convert-isbn10-to-isbn13-with-php/
		private function genchksum13 ($isbn) {
			$isbn = trim($isbn);
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
		private function isbn10_to_13($isbn) {
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
		 * Return an icon to represent the metatron
		 *
		 * @param string $_resolution - the requested image resolution
		 * @return string icon filename
		 */
		public static function getIcon($_resolution = null) {
			$_ns = self::getNamespace();
			foreach (array(32, 48, 64, 128, 256) as $_r) {
				if($_resolution <= $_r) {
					return self::getNamespace() . "_" . $_r . ".png";
				}
			} 
		}
		
		
	}
