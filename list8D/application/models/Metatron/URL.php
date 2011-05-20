<?php

	class List8D_Model_Metatron_URL extends List8D_Model_Metatron {
	
		private static $_namespace = "Url";
		
		public static function getNamespace() {
			return self::$_namespace;
		}
		
		protected $_supportedTypes = array (
		  'Url' => 'List8D_Model_Resource_Url',
		);
		
		protected $_unique = array(
			'Url' => 'url',
		);
		
		private function get_title ($html) {
			if (preg_match("/\<title\>(.*)\<\/title\>/Us", $html, $title)) {
				return trim(strip_tags($title[1]));
			} 
			return null;
		}
		
		
		
		public static function getName() {
		  return "The World Wide Web";
		}
		
		// returns a formatted array for returning to Metatron
		private function fmt_data($_data) {
			// pre_dump ($data);
			
			$_ret = array (
						  'title' => (!empty($_data['title'])) ? $_data['title'] : $_data['url'],
						  'url' => $_data['url'],
						  'description' => $_data['url'],						  
						  // stuff below here isn't specified in the Metatron API and should be prefixed with our namespace string
						  );
			// pre_dump($ret);
			return $_ret;
		}
		
		
		public function getMetadata($_url) {
		
			/* Process:
			 *   split $url to grab the protocol
			 *   create Zend_Uri object for protocol
			 *   perform http lookup
			 *   check return codes
			 *   extract useful data and pass to fmt_data
			 */

			// Bodge, but probably covers most users...
			if (!preg_match('/^(https?|ftp)\:\/\//', $_url)) {
				$_url = "http://" . $_url;
			}

			
			
			
			if (! Zend_Uri::check($_url)) {
				// FIXME: throw exception!
				return array(
					'uri'=>$_url,
					'title'=>$_url
				);
			}

						
			// $URL = Zend_Uri::factory($url);

			$_client = new Zend_Http_Client($_url,array('httpversion' => Zend_Http_Client::HTTP_0));
			
			try {
				$_response = $_client->request();
			} catch (Exception $e) {
				return array(
					'uri'=>$_url,
					'title'=>$_url
				);
			}
			
			// pre_dump($response);
			
			$headers =  Zend_Http_Response::extractHeaders($_response);
			$oldHtml = Zend_Http_Response::extractBody($_response);
			if (isset($headers['content-encoding']) && $headers['content-encoding'] == 'gzip' && function_exists("gzuncompress") && $html = @Zend_Http_Response::decodeGzip($oldHtml)) {
				
			} else {
				$html = $oldHtml;
			}
			
			return $this->fmt_data(array (
										  'url' => $_url,
										  'title' => $this->get_title($html),
										  )
			);
		}
		

		public function findResources($terms, $type, $page=null, $max=null, $offset=1) {
		
			$type = strtolower($type);
				switch($type) {
                case "url":
                	$resource = new List8D_Model_Resource_URL();
                    $result[] = $resource->setDataByArray($this->getMetadata($terms['url']));
                    $resource->dataLoaded = true;
                    return $result;
                    break;
                default:
                    return false;
                    break;
			}
			
		}
		
		public function getAdditionalMetadata($_type, $_res) {
			if($_type != 'List8D_Model_Url') return null;
			$_prikey = $_res['url'];
			return $this->getMetadata($_prikey);
		}
		
		public static function getClasses() {
			return array ('List8D_Model_Url');
		}
		
		public static function registerURL() {
			return null;
		}
		
		public static function getIcon($_resolution = null) {
			$_ns = self::getNamespace();
			foreach (array(32, 48, 64) as $_r) {
				if($_resolution <= $_r) {
					return self::getNamespace() . "_" . $_r . ".png";
				}
			} 
		}
		
	}