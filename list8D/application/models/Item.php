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
* Class to describe a list item
*
* @copyright  Copyright (c) 2009 University of Kent (http://www.kent.ac.uk)
* @license    http://www.gnu.org/licenses/gpl-2.0.txt     GNU General Public License, version 2
* @author list8d
*/
class List8D_Model_Item extends List8D_Model_Branch {

	public $_list;
	protected $_resource_id;
	protected $_resource;
	protected $_order;
	protected $_start;
	protected $_end;
	protected $_created;
	protected $_updated;
	protected $_author;
	
	public $_data = array(
  	'publication_date'=>array(
  		'title'=>'Publication date',
  		'type'=>'text',
  		'for'=>array(
  			'List8D_Model_Resource_Journal',
  			'List8D_Model_Resource_LegacyJournal',
  		),
  	),
  	'volume'=>array(
  		'title'=>'Volume',
  		'type'=>'text',
  		'for'=>array(
  			'List8D_Model_Resource_Journal',
  			'List8D_Model_Resource_LegacyJournal',
  		),
  	),
  	'issue'=>array(
  		'title'=>'Issue',
  		'type'=>'text',
  		'for'=>array(
  			'List8D_Model_Resource_Journal',
  			'List8D_Model_Resource_LegacyJournal',
  		),
  	),
  	'part'=>array(
  		'title'=>'Pages to Read',
  		'type'=>'text',
  	),
  	'part_title'=>array(
  		'title'=>'Chapter/Article Title',
  		'type'=>'text',
  	),
  	'part_author'=>array(
  		'title'=>'Chapter/Article Author',
  		'type'=>'text',
  	),
  	'private_notes'=>array(
  	  'title'=>'Instructions for librarians',
  	  'type'=>'multiline',
  	),
  	'public_notes'=>array(
  		'title'=>'Notes for students',
  		'type'=>'multiline',
  	),
  	'start'=>array(
  	  'title'=>'Start date',
  	  'type'=>'date',
  	),
  	'end'=>array(
  	  'title'=>'End date',
  	  'type'=>'date',
  	),
  	'is_published'=>array(
  	  'title'=>'Published',
  	  'type'=>'boolean',
  	),
  	'core_text'=>array(
  	  'title'=>'Core text',
  	  'type'=>'boolean',
  	),
  	'recommended_for_purchase'=>array(
  	  'title'=>'Recommended for purchase',
  	  'type'=>'boolean',
  	),
	'needs_updating' => array(
		'title'=> 'Needs Updating',
		'type' => 'boolean',
	),
	'scan_request' => array(
		'title' => 'Request Scan',
		'type' => 'radio',
		'options' => array(
			"blank" => "None required",
			"scan_requested" => "Scan requested",
			"scan_in_progress" => "Scan in progress",
			"scan_available" => "Scan available on Moodle",
		),
	),
	);

	function __construct($form_data=null){
  
		parent::__construct();
		
		
		/*
		$this->_data['id'] = $form_data['id'];
		$this->_data['class'] = "Item";
		$this->_data['resource_id'] = $form_data['resource_id'];
		$this->_data['order'] = $form_data['order'];
		$this->_data['start'] = $form_data['start'];
		$this->_data['end'] = $form_data['end'];
		$this->_data['created'] = $form_data['created'];
		$this->_data['updated'] = $form_data['updated'];
		$this->_data['author'] = $form_data['author'];
		*/
	}
  
  	/**
 	 * Saves an item to the database
   	 *
   	 */
  	function save() {
    
    	//$this->saveData();
    	$this->getMapper()->save($this);
		
  	}
  
  	/**
   	 * Returns the resource with which an item is associated
   	 *
   	 * @return object the resource
   	 */
  	public function getResource($disregardPart=false) {
	
		
			if ($this->_resource==null) {
				
				$resourcePeer = new List8D_Model_Resource();
				$this->_resource = $resourcePeer->getById($this->getResourceId());
				
			}
			
			if (!$disregardPart && ($this->getDataValue("part") || $this->getDataValue("part_author") || $this->getDataValue("part_title"))) {
				$this->_resource->partSet = true;
			} else {
				$this->_resource->partSet = false;
			}
			
			return $this->_resource;
		
  	}
  	
  	public function setResource($resource) {
  	     $this->_resource = $resource;
  	}

	
  	/**
   	 * Sets the local reference of the id for the resource with which this item is associated
   	 *
   	 * @return object itself
   	 */
  	public function setResourceId($resourceId) {
		$this->_resource_id = $resourceId;
		return $this;
	}
	
	/**
	 * Utility function, same as setResourceId
	 *
	 * @return object itself
	 */
	public function setResource_id($resourceId) {
		$this->_resource_id = $resourceId;
		return $this;
	}

	/**
	 * Returns the local reference to the id of the resource associated with this item
	 *
	 * @return integer the resource id
	 */
	public function getResourceId() {
		return $this->_resource_id;
	}
	
	/**
	 * Sets the position of this item in an ordered list
	 *
	 * @param integer $order - the order position to set
	 * @return object itself
	 */
	public function setOrder($order) {
		$this->_order = $order;
		return $this;
	}

	/**
	 * Returns the current order position of this item in an ordered list
	 *
	 * @return integer the order position
	 */
	public function getOrder() {
		return $this->_order;
	}
	
	/**
	 * Sets the created date of the item
	 *
	 * @param timestamp $created - the date to set
	 * @return object itself
	 */
	public function setCreated($created) {
		$this->_created = $created;
		return $this;
	}
	
	/**
	 * Returns the created date of the item
	 *
	 * @return timestamp the created date
	 */
	public function getCreated() {
		return $this->_created;
	}

	/**
	 * Sets the date at which the item was updated
	 *
	 * @param timestamp $updated - the date to set
	 * @return object itself
	 */
	public function setUpdated($updated) {
		$this->_updated = $updated;
		return $this;
	}
	
	/**
	 * Returns the date at which the item was updated
	 *
	 * @return timestamp the updated date
	 */
	public function getUpdated() {
		return $this->_updated;
	}

	/**
	 * Sets the author of the item
	 *
	 * @param string $author - the author name
	 * @return object itself
	 */
	public function setAuthor($author) {
		$this->_author = $author;
		return $this;
	}
	
	/**
	 * Returns the author of the item
	 *
	 * @return string the author name
	 */
	public function getAuthor() {
		return $this->_author;
	}

	
	/*
	public function getTitle() {
		return $this->getRawData('title');
	}
	
	public function getCode() {
		return $this->getRawData('code');
	}
	*/
	
	/**
	 * Returns the private notes associated with the item
	 *
	 * @return string the notes
	 */
	public function getPrivateNotes() {
		return $this->getRawData('private_notes');
	}
	
	/**
	 * Returns the core text field of the item
	 *
	 * @return boolean whether the item is a core text or not
	 */
	public function getCoreText() {
		return $this->getRawData('core_text');
	}
	
	/**
	 * Returns whether or not the item is recommended for purchase
	 *
	 * @return boolean whether the item is recommended for purchase or not
	 */
	public function getRecommendedForPurchase() {
		return $this->getRawData('recommended_for_purchase');
	}
	
	/**
	 * Sets the private notes field of the item
	 *
	 * @param string $private_notes - the text to put in private notes
	 * @return object itself
	 */
	public function setPrivateNotes($private_notes) {
		return $this->setData('private_notes',$private_notes);
	}
	
	/**
	 * Sets the core text field of the item
	 *
	 * @param boolean $core_text - whether or not this is a core text item
	 * @return object itself
	 */
	public function setCoreText($core_text) {
		return $this->setData('core_text',$core_text);
	}
	
	/**
	 * Sets whether or not this item is recommended for purchase
	 *
	 * @param boolean $recommended_for_purchase - whether or not this item is recommended for purchase
	 * @return object itself
 	 */
	public function setRecommendedForPurchase($recommended_for_purchase) {
		return $this->setData('recommended_for_purchase',$recommended_for_purchase);
	}
	
  	/**
	 * Returns the title of the resource associated with the item
	 * 
	 * @return string the title
	 */
	function getTitle() {
		return $this->getResource()->getDataValue('title');
	}  
	
	/**
	 * Returns the type of the resource associated with the item
	 *
	 * @return string the type
	 */
	function getType() {
		return $this->getResource()->getType();
	} 
	
	/**
	 * Returns the icon url of the resource associated with the item
	 *
	 * @return string the icon url
	 */
	function getIcon() {
		return $this->getResource()->getIcon();
	}
	
  /*
function getItems($id=null) {
    
    if (!$id) {
    	$id = $this->_id;
    }
    
    
    $this->_db->setFetchMode(Zend_Db::FETCH_OBJ);
    $select = $this->_db->select();
    $select->from('item')
    			 ->where('id = ?',$id);
    $item = $select->query()->fetch();
		
		foreach($item as $key => $value) {
			$this->$key = $value;
		}
    
    return $this;
    
  }
*/
	/**
	 * Returns all the items for a particular list
	 *
	 * @param integer $id - the list id
	 * @return array of items as objects
	 */
	function getItems($id) { 
		return $this->getMapper()->getItems($id);
	}
	
	/**
	 * Returns a specific item given by id
	 *
	 * @param integer $id - the id of the item to fetch from the database table
	 * @return object itself, populated with data
	 */
	function getItem($id) {
		$this->getMapper()->find($id, $this);
		return $this;
	}
	
	/**
	 * Returns whether or not this is the first item in a list
	 *
	 * @return boolen
	 */	
	function isFirst() {

		if ((int) $this->_order === 0) {
			return true;
		} else {
			return false;
		}
		
	}
	
	function getData($namespace = null,$returnValue=false) {

		//if there's nothing in the _data array, fetch from the db
		if(!$this->dataLoaded){

			//fetch
			$this->_dataValues = $this->getMapper()->getData($this);
			
			foreach ($this->_data as $key => $structure) {

				if (isset($this->_dataValues[$key])) {
				
					$this->_data[$key]['value'] = $this->_dataValues[$key];
				} else {
					$this->_data[$key]['value'] = null;
				}
			}

		}
		
		
		foreach($this->_data as $key => $data) {
		  if (!empty($data['for']) && !in_array(get_class($this->getResource(1)),$data['for'])) {
		  	unset($this->_data[$key]);
		  }
		}
		$data = $this->_data;
		//return from the _data array
		if ($namespace==null) {
			return $this->_data;
		} else if (!empty($data[$namespace])) {
			if ($returnValue) {	
				if (!empty($this->_data[$namespace]['value']))
					return $this->_data[$namespace]['value'];			
				else 
					return false;
			} else {
				return $this->_data[$namespace];
			}
		} else {
			return false;
		}
	}
	
	public function getAccessResourceType() {
		return 'item';
	}
	
	public function getAccessResource() {
		return $this->getTrunk();
	}
	
	/**
	 * Returns the name of the Item
	 *
	 * @return string of title
	 */
 	function __toString() {
 		if (is_string($this->getTitle()))
	 		return $this->getTitle();
	 	else
	 		return "";
 	}
 	
 	public function duplicateRow(&$duplicate) {
 		
 		$duplicate->setListId($this->getListId())
				->setClass(get_class($this))
				->setOrder($this->getOrder())
				->setResourceId($this->getResourceId())
				->setStart($this->getStart())
				->setEnd($this->getEnd())
				->setAuthor($this->getAuthor());
 		
 		return $this;
 		
 	}

	/**
	 * Sets a new value, needs_updating
	 */
	function needsUpdate(){
		$this->getMapper()->setData("needs_updating",true);
		
	}
	
	/**
	 * Updates the resource associated with this item
	 */
	function updateResource(){
		$resource = $this->getResource()->update();
	}

}

