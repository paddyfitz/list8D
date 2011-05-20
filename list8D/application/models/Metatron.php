<?php

/**
 * List8D
 *
 * LICENCE
 *
 * Copyright (c) 2009 University of Kent (http://www.kent.ac.uk)
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 */
 
/**
* Abstract class to describe a metatron
*
* @copyright  Copyright (c) 2009 University of Kent (http://www.kent.ac.uk)
* @license    http://www.gnu.org/licenses/gpl-2.0.txt     GNU General Public License, version 2
* @author list8d
*/

abstract class List8D_Model_Metatron {

	/**
	 * getMetadata should be used to return metadata for a specific item
	 *
	 * @param string $key should be a 'unique' identifier
	 * @return array a single resourceData array (http://code.google.com/p/list8d/wiki/ResourceData)
	 */
	public function loadMetadata($resource,$overwrite=false) {

		$uniques = array($this->getUniqueKeys($resource->getType()));

		foreach($uniques as $unique) {

			if($overwrite) {
				//if we have an array of values...
				
				if (sizeof($unique) > 1) {
					
					foreach ($unique as $unique_item){
					
						if ($resource->getDataValue($unique_item) && $unique_item ) {
							
							//horrible, horrible hack - bear with me for now
							$resource_type = $resource->getType();
							
							if(get_class($resource) == "List8D_Model_Resource_JournalArticle" || get_class($resource) == "List8D_Model_Resource_Journal"){
								$resource->setDataByArray($this->getMetaData($resource->getDataValue($unique_item),$unique_item),$resource);
							}
							else{
								$resource->setDataByArray($this->getMetaData($resource->getDataValue($unique_item)),$resource);
							}
						}
						
					}
				}
				else{
				
					if ($resource->getDataValue($unique) && $unique ) {
						
						//horrible, horrible hack - bear with me for now
						$resource_type = $resource->getType();
						if($resource_type == "List8D_Model_Resource_JournalArticle" || $resource_type == "List8D_Model_Resource_Journal"){
							$resource->setDataByArray($this->getMetaData($resource->getDataValue($unique),$unique),$resource);
						}
						else{
							$resource->setDataByArray($this->getMetaData($resource->getDataValue($unique)),$resource);
						}
					}
				}	
			
			} else {
				//if we have an array of values...
				if (sizeof($unique) > 1) {
					foreach ($unique as $unique_item){
						if ($resource->getDataValue($unique_item) && $unique_item ) {
							
							//horrible, horrible hack - bear with me for now
							$resource_type = $resource->getType();
							if($resource_type == "List8D_Model_Resource_JournalArticle" || $resource_type == "List8D_Model_Resource_Journal"){
								$resource->getMapper()->setAdditionalMetadata($this->getMetaData($resource->getDataValue($unique_item),$unique_item),$resource);
							}
							else{						
								$resource->getMapper()->setAdditionalMetadata($this->getMetaData($resource->getDataValue($unique_item)),$resource);
							}
						}
						
					}
				}
				else{
								
					if ($resource->getDataValue($unique) && $unique ) {
						
						//horrible, horrible hack - bear with me for now
						$resource_type = $resource->getType();
						if($resource_type == "List8D_Model_Resource_JournalArticle" || $resource_type == "List8D_Model_Resource_Journal"){
							$resource->getMapper()->setAdditionalMetadata($this->getMetaData($resource->getDataValue($unique),$unique),$resource);
						}
						else{
							$resource->getMapper()->setAdditionalMetadata($this->getMetaData($resource->getDataValue($unique)),$resource);
						}
					}
				}		
			}
		}

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
	abstract function findResources($terms, $type, $page=null, $max=null, $offset=1);

	/**
	 * Each metatron requires a unique namespace. We recommend this matches
	 * the class name. This will be referred to in the database, so changing
	 * it later on will cause pain.
	 *
	 * @return string Namespace for this metatron
	 */
	abstract public static function getNamespace();

	/**
	 * Each metatron should return a friendly name. This is used for display
	 * purposes only.
	 *
	 * @return string Friendly name
	 */
	abstract public static function getName();

	/**
	 * Return a list of classes that we would normally expect to support.
	 * This prevents this metatron being queried for types of resource that
	 * it should never find, such as a URL.
	 *
	 * @return List8D_Model_Resource
	 */
	abstract public static function getClasses();

	/**
	 * Each metatron should register a URI regexp match for future bookmarklet
	 * functionality.
	 *
	 * @return string URI regex match
	 */
	abstract public static function registerUrl();
    
    
    public function getTypes() {
        return $this->_supportedTypes;
    }
	
	public function getUniqueKeys($type = null) {
		if ($type && isset($this->_unique[$type]))
			$return = $this->_unique[$type];
		else 
			$return = $this->_unique;
			
/*
		if (!is_array($return)) 
			$return = array($return);
*/
			
		return $return;
	}
	
	public function supports ($type) {
		
		if (isset($this->_supportedTypes[$type])) {
			return true;
		} else {
			return false;
		}
	}
}
	
	