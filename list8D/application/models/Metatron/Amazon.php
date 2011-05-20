<?php

	class List8D_Model_Metatron_Amazon extends List8D_Model_Metatron {
	
		private $_amazon;
		private static $_namespace = "Amazon";
		
		protected $_supportedTypes = array (
		  'Book' => 'List8D_Model_Resource_Book',
		  'PhysicalMedia' => 'List8D_Model_PhysicalMedia',
		);
		
		protected $_unique = array(
			'Book' => array(
						'Amazon_asin',
						'isbn10',
						),
			'PhysicalMedia' => 'Amazon_asin',
		);
		
		// maps amazon ProductGroup value to List8D Resource implementation class
		protected $_typemap = array (
								   'Book' => 'List8D_Model_Resource_Book',
								   'DVD'  => 'List8D_Model_Resource_PhysicalMedia',
								   'VHS'  => 'List8D_Model_Resource_PhysicalMedia',
								   'Music' => 'List8D_Model_Resource_PhysicalMedia', // Music CD
		);
		
		private $_classmap = array (
									'List8D_Model_Resource_Book' => array('Books'),
									'List8D_Model_Resource_PhysicalMedia' => array('DVD', 'VHS', 'Music'),
									);
		
		// Takes a List8D_Model_Resource and turns it into an Amazon type using $_typemap
		private function resclass_to_types ($_class) {
			$_ret = array();
			foreach ($this->_typemap as $_type => $_mclass) {
				if($_mclass == $_class) $_ret[$_type] = 1;
			}
			return array_keys($_ret);
		}
	
		
		public function __construct($_config = array()) {
            global $application;
            $conf = $application->getOptions();
            $_config += $conf['list8d'][self::$_namespace];
            
			//TODO: sanity check key and secret
			if ($_config['key'] == '')
				throw new Exception("Missing Amazon Key");
			if ($_config['secret'] == '')
				throw new Exception("Missing Amazon Secret");
			if ($_config['country'] == '')
				throw new Exception("Missing Amazon Country code");
			
			$this->_amazon = new Zend_Service_Amazon(
													 $_config['key'],
													 $_config['country'],
													 $_config['secret']
													 );
			// echo "Initialised amazon";
			// pre_dump($this->_amazon);
		}
		
		public static function getNamespace() {
			return self::$_namespace;
		}
		
		public static function getName() {
		  return "Amazon";
		}
		
		private function fmt_description ($_data) {
			$_ret = array();
			
			if(!empty($_data->Binding)) {
				array_push ($_ret, $_data->Binding);
			}
			
			if(!empty($_data->RunningTime)) {
				array_push ($_ret, $_data->RunningTime . " minutes");
			}
			
			if(!empty($_data->AudienceRating)) {
				array_push ($_ret, $_data->AudienceRating);	
			}
			
			if(!empty($_data->RegionCode)) {
				array_push ($_ret, "Region Code " . $_data->RegionCode);
			}
			
			if (!empty($_data->NumberOfPages)) {
				array_push($_ret, $_data->NumberOfPages . " pages");
			} 
			
			return implode (", ", $_ret);
		}
		
		// returns a formatted array for returning to Metatron
		private function formatData($data) {

			$return = array (
						   'title' => !empty($data->Title) ? $data->Title : null,
						   'primaryKey' => !empty($data->ASIN) ? $data->ASIN : null,
						   'thumbnailUrl' => !empty($data->SmallImage->Url) ? $data->SmallImage->Url->getUri() : null,
						   'meta_url' => !empty($data->DetailPageURL) ? array('amazon'=>array('text'=>'Amazon','url'=>$data->DetailPageURL)) : null,
						   'authors' => !empty($data->Author) ? (is_array($data->Author) ? implode (", ", $data->Author) : $data->Author) : null,
						   'publisher' => !empty($data->Publisher) ? $data->Publisher : null, 
						   'edition' => !empty($data->Edition) ? $data->Edition : null,
						   'publication_date' => !empty($data->PublicationDate) ? $data->PublicationDate : null,
						   'description' => $this->fmt_description($data),
						   'isbn10' => !empty($data->ISBN) ? $data->ISBN : null,
						   'ean' => !empty($data->EAN) ? $data->EAN : null,
						   //'issn' => null,
						   'namespace' => self::$_namespace,
						   'classHint' => !empty($this->_typemap[$data->ProductGroup]) ? $this->_typemap[$data->ProductGroup] : null,
						   'Amazon_thumbnailUrl_small' => !empty($data->SmallImage->Url) ? $data->SmallImage->Url->getUri() : null,
						   'Amazon_thumbnailUrl_medium' => !empty($data->MediumImage->Url) ? $data->MediumImage->Url->getUri() : null,
						   'Amazon_thumbnailUrl_large' => !empty($data->LargeImage->Url) ? $data->LargeImage->Url->getUri() : null,
						   'Amazon_upc' => !empty($data->UPC) ? $data->UPC : null,
						   'Amazon_price' => !empty($data->FormattedPrice) ? $data->FormattedPrice : null,
						   'Amazon_mpn' => !empty($data->MPN) ? $data->MPN : null,
						   'Amazon_asin' => !empty($data->ASIN) ? $data->ASIN : null,
						   'Amazon_url' => !empty($data->DetailPageURL) ? $data->DetailPageURL : null,
						   
						   );
						   

			return $return;
		}
		
		public function getMetadata($_asin) {
			
			// TODO: exception throwing
			try{
			
				$_results = $this->_amazon->itemlookup ($_asin, 
					array('ResponseGroup' => "Small,ItemAttributes,Images")
					);
			}
			catch(Exception $e){
				
			}
			
			// pre_dump ($results);
			return $this->formatData($_results);
		}
		
		public function findResources($terms, $type, $page=null, $max=null, $offset=1) {

			// echo "<pre> Searching for $keywords... </pre>";
			$ret = array();
			$resourceHandler = new List8D_Model_Resource_Handler();
			$class = get_class($resourceHandler->getTypeClass($type));
			$indexes = $this->_classmap[$class];

			$query = array('ResponseGroup' => "Small,ItemAttributes,Images");
			
			if($page){
				$query['ItemPage'] = $page;
			}
			
			$query['Keywords'] = array();
			if(!empty($terms['keyword']))
				$query['Keywords'][] = $terms['keyword'];
			if(!empty($terms['title']))
				$query['Title'] = $terms['title'];
			if(!empty($terms['author']))
				$query['Author'] = $terms['author'];
			if(!empty($terms['publication_date']))
				$query['Power'] = "pubdate: during ".$terms['publication_date'];
			if(!empty($terms['isbn']))
				$query['Keywords'][] = $terms['isbn'];
			$query['Keywords'] = implode(" ", $query['Keywords']);

			if (empty($terms['keyword']) && empty($terms['isbn']) && empty($terms['title']) && empty($terms['author']) && empty($terms['publication_date'])) {
				return;
			}
			
			$return = array();			
			foreach ($indexes as $index) {
				$query['SearchIndex'] = $index;
				
				$results = $this->_amazon->itemSearch($query);  

				foreach ($results as $result) {
					
					if ($type=="Book") {
						$resource = new List8D_Model_Resource_Book();
					} elseif ($type=="PhysicalMedia") {
						$resource = new List8D_Model_Resource_PhysicalMedia();
					}
				
					$data = $this->formatData($result);
					$return[] = $resource->setDataByArray($data);			
					$resource->dataLoaded = true;
					
				}
				
			}

			return $return;
		}
		
		public function getAdditionalMetadata($_type, $_res) {
			//$_rdata = $_res->getData();
			$_prikey = null;

			foreach ((array) $_res as $_key => $_val) {
				switch ($_key)  {
					case 'Amazon_asin':
						return $this->getMetadata($_val);
					case 'isbn10':
						$r = $this->findResources($_val);
						if (count($r) != 1) {
							return null;
						} else {
							return $r[0];
						}
					case 'ean':
						$r = $this->findResources($_val);
						if (count($r) != 1) {
							return null;
						} else {
							return $r[0];
						}
					}
				}
			return null;
		}
		
		
		public static function getClasses() {
			// TODO: this should probably iterate over the $_typemap array and return a distinct list of the entries
			return array(
						 'List8D_Model_Resource_Book',
						 'List8D_Model_PhysicalMedia',
						 );
		}
		
		public static function registerUrl() {
			return null;
		}
		
		public static function getIcon($_resolution = null) {
			$_ns = self::getNamespace();
			if($_resolution <= 32) {
				return "{$_ns}_32.png";
			} else {
				return "{$_ns}_orig.png";
			}
		}
		
	}
