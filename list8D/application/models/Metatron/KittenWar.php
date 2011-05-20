<?php

	class List8D_Model_Metatron_KittenWar extends List8D_Model_Metatron {
	
		private static $_namespace = "Kittenwar";
		
		public static function getNamespace() {
			return self::$_namespace;
		}
		
		protected $_supportedTypes = array (
		  'Kitten' => 'List8D_Model_Resource_Kitten',
		);
		
		protected $_unique = array(
                        'Url' => 'url',
		);
		
		private function get_title ($html) {
			if (preg_match("/\<h4\>(.*?)\<\/h4\>/Us", $html, $title)) {
				return trim(strip_tags($title[1]));
			} 
			return null;
		}
		
		
		public static function getName() {
		  return "KittenWar";
		}
		
		public function getMetadata($_url) {
			// This wouldn't normally be appropriate, but in this case we can make do with just the metadata from the initial find.	
		        return null;	
		}
		

		public function findResources($terms, $type, $page=null, $max=null, $offset=1) {
			$type = strtolower($type);
			switch($type) {
			case "kitten":
				$ch = curl_init();

				// set URL and other appropriate options
				curl_setopt($ch, CURLOPT_URL, "http://kittenwar.com/view.php?search=" . $terms['kittensearch']);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

				// grab URL content and parse
				$output = curl_exec($ch);
				$o_array = explode("\n", $output);
				$result = array();
				foreach ($o_array as $line) {
					// Match image
					if (preg_match('/<img src="\/c_images\/([^"]+)" width="125" border="0" \/><\/a>/', $line, $m)) {
						$img = "http://www.kittenwar.com/c_images/" . $m[1];
					}
					// Match link and caption
					if (preg_match('/<a href="(\/kittens\/[0-9]+\/)"\>([^<]+)<\/a><\/li>/', $line, $m)) {
						$link = "http://www.kittenwar.com" . $m[1];
						$caption = $m[2];

						// Image links are a bit of a bodge to work around lack of template support for Kittens :)
						$_ret = array (
								'title' => $caption,
								'url' => $link,
								'description' => $link,
								'GoogleBooks_thumbnailUrl' => $img,
								'Amazon_thumbnailUrl_large' => $img
							      );

						// Create a resource, populate and add to the array of results
						$resource = new List8D_Model_Resource_Kitten();

						// Quick way of populating resource data
						$resource->setDataByArray($_ret);
						$resource->dataLoaded = true;
						$result[] = $resource;
					}
				}

				curl_close($ch);

				return $result;
				break;

			default:
				return false;
				break;
			}

		}
		
		public function getAdditionalMetadata($_type, $_res) {
			return null;
		}
		
		public static function getClasses() {
			return null;
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
