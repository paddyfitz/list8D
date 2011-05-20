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
* api class for accessing list, item, and resource data
* also allows new list creation
*
* @copyright  Copyright (c) 2009 University of Kent (http://www.kent.ac.uk)
* @license    http://www.gnu.org/licenses/gpl-2.0.txt     GNU General Public License, version 2
* @author list8d
*/
class List8D_API {
	
	/**
  *
  * gets a list object from a list id
  *
  * @param integer $listId
  * @return object
  */
 	function fetchList($listId){
		$list = new List8D_Model_List();
		$list = $list->getById($listId);
		return $list;
	}
	
	public function sayHello($who,$when) {
		return "Hello $who, good $when!";
	}
	
	/**
  *
  * gets an item object from an item id
  *
  * @param integer $itemId
  * @return object
  */
	function fetchItem($itemId){
		$item = new List8D_Model_Item();
		$item = $item->getById($itemId);
		return $item;
	}
	
	/**
  *
  * gets a resource object from a resource id
  *
  * @param integer $resourceId
  * @return object
  */
	function fetchResource($resourceId){
		$resource = new List8D_Model_Resource();
		$resource = $resource->getById($resourceId);
		return $resource;
	}

	/**
  *
  * returns metadata and items on a list
  *
  * 
  * Modes can be as follows:
  * min	 -1	 returns only list of relevant ids with no recursion
	* normal	 0	 returns useful metadata about requested item with list of IDs of sub-items if applicable
	* extra	 1	 returns all available metadata about requested item with list of IDs for one level of sub-items if applicable
	* max	 2	 returns all available metadata about requested item with expanded list of sub-items if applicable
 	*
 	* If no mode is specified, normal is assumed
  *
  * @param integer $listId
  * @param array $select
  * @param integer $limit
  * @param integer $offset
  * @return array
  */
	public function getListById($listId, $select=false, $limit=0,$offset=0 ) {
		$list = new List8D_Model_List;	
		$list = $list->getByID($listId);
		return $list->quickLoadReturnArray($select,$limit,$offset);	
	}
	
	public function getListByData($key,$value,$select=false, $limit=0,$offset=0) {
		$list = new List8D_Model_List;	
		$list = $list->findByData($key,$value,false);
		if ($list instanceof List8D_Model_List)
			return $list->quickLoadReturnArray($select,$limit,$offset);
		else 
			return false;
	}
	
	/**
  *
  * returns metadata about an item
  *
  * @param integer $itemId
  * @return array
  */
	public function getItemById($itemId){
		$item = $this->fetchItem($itemId);
		return $item->getResource()->getData();
	}
	
	/**
  *
  * returns metadata about a resource
  *
  * @param integer $resourceId
  * @return array
  */
	public function getResourceById($resourceId){
		$resource = $this->fetchResource($resourceId);
		return $resource->getData();
	}
	
	/**
  *
  * returns metadata from a list's module code
  *
  * @param string $code
  * @return array
  */
	public function getListByCode($code){
		$list = new List8D_Model_List();
		$list = $list->searchData(array("code"=>array("`value` = '".$code."'")));
		return $list;
	}
	
	/**
  *
  * returns ids of items in a list
  *
  * @param string $listId
  * @return array
  */
	public function getListItemsById($listId){
		$list = $this->fetchList($listId);
		
		$items = $list->getItems();
		
		$item_ids = array();
		foreach($items as $item){
			array_push($item_ids, $item->getId());
		}
		return $item_ids;
	}
	
	/**
  *
  * generates a new list given list info, and returns XML detailing new list id, success, failure, etc
  *
  * @param string $key authentication key
  * @param string $title
  * @param string $code
  * @param string $private_notes
  * @param string $start
  * @param string $end
  * @return string
  */
	public function setList($key, $title, $code, $private_notes, $start, $end){
			
			if ($this->authenticate($key)) {
			
				$list = new List8D_Model_List();
				
				//does a list with this code, start and end date already exist?
				$list = $list->getByCodeStartEnd($code,$start,$end);
				
				//if not, we have to initialise a new list
				if(!$list){
					$list = new List8D_Model_List();
				}
				
				$data = array(
						"title" => $title,
						"code" => $code,
						"is_published" => 0,
						"private_notes" => $private_notes,
					);
				
				// set start and end date for main list object
				$list->setStart($start);
				$list->setEnd($end);
				
				// set all the data for the data object
				$list->setDataByArray($data);
				// means db not accessed later
				$list->dataLoaded = true;
				
				// save the list, which also saves all the data object stuff to the db
				$list->save();
				
				// output xml version
				return $this->generateXML(array('id'=>$list->getId()), 'setList', true);
		}
		return $this->generateXML(array(), 'setList', false);
	}
	
	/**
  *
  * simple authentication. checks api keys in the config file
  *
  * @param string $key authentication key
  */
	private function authenticate($key) {
		$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', APPLICATION_ENV);
		$keys = $config->list8d->apikey;
		if (in_array($key, $keys->toArray())) {
			return true;
		}
		return false;
	}
	
	/**
  *
  * generates status level attribute in top level element in xml
  *
  * @param boolean $status success or fail level
  * @param boolean $cached whether cached or not
  */
  private function appendStatus($status=true){
    if ($status === true)
      $this->_xml->addChild('status', 'success');
    else if ($status === false)
      $this->_xml->addChild('status', 'failed');
    else
      throw new Exception('Invalid response status');
  }
  
  /**
  *
  * generates xml tree
  *
  * @param object $xml simplexml object
  * @param array $response input array
  * @param string $index_type head xml element 
  */
  private function appendResponse($xml, $response, $index_type) {
    foreach ($response as $key => $val) {
      if (is_numeric($key))
      {
        $key = $index_type;
      }
      if (is_array($val)) {
        $child = $xml->addChild($key);
        $this->appendResponse($child, $val, $index_type);
      }
      else {
        $xml->addChild($key, trim(htmlentities($val)));
      }
    }
  }
	
  /**
  *
  * generateXML - generates xml output given an array of data
  *
  * @param array $response response array as input for method
  * @param string $index_type head xml element
  * @param boolean $status succeed or fail marker
  * @return array
  */
  private function generateXML($response, $index_type, $status=true) {
    $this->_xml = simplexml_load_string('<?xml version="1.0" encoding="UTF-8"?><response></response>');
    $this->appendStatus($status);
    if (count($response) > 0)
      $this->appendResponse($this->_xml, $response, $index_type);
    return $this->_xml;
  }
	
	public function reorderList($listId,$listStructure) {
		$list = new List8D_Model_List();
		$list = $list->getById($listId);
		if (is_object($list)) {
			$db = Zend_Registry::get('dbResource');
			$db->getDbAdapter()->beginTransaction();
			try {
				$this->_reorderList($listId,$listStructure['list-items'],$db);
				$db->getDbAdapter()->commit();	
			} catch (Exception $e) {
				$db->getDbAdapter()->rollback();
				throw new Exception($e->getMessage());
			}		
		} else {
			throw new Exception("Could not find list with id $listId");
		}
		
	}
	
	protected function _reorderList($listId,$listStructure,$db) {
		
		$position=0;
		foreach($listStructure as $item) {
			if(preg_match("/^(list|item)_(\d+)/",$item['id'],$matches)) {
				$type = $matches[1];
				$id = $matches[2];
				$db->getDbAdapter()->query("UPDATE $type SET `order`=$position, list_id=$listId WHERE id=$id");
				if ($type=='list' && !empty($item['children']) && is_array($item['children'])) {
					$this->_reorderList($id,$item['children'],$db);
				}
				$position++;
	
			}
			
		}
		
	}
		  
	
}