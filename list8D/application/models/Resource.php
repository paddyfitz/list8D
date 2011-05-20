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
* Class to describe a resource
*
* @copyright  Copyright (c) 2009 University of Kent (http://www.kent.ac.uk)
* @license    http://www.gnu.org/licenses/gpl-2.0.txt     GNU General Public License, version 2
* @author list8d
*/
class List8D_Model_Resource extends List8D_Model_Root {
  

  const TABLE_NAME = "List8D_Models_DbTable_Resource";
  const DATA_TABLE_NAME = "List8D_Models_DbTable_Resource_Data";
  
  protected $_type = "Resource";
  protected $_typeName = "Resource";
 	public $partSet = false;
 	protected $_unique = array();
 	
  public $_data = array(
  	'title'=>array(
  		'title'=>'Title',
  		'type'=>'text',
  	),
  );
  	
	function __construct($form_data=null){

		parent::__construct();
	
	}
	
	/**
	 * Retrieve the type of this object
	 *
	 * @return string "resource"
	 */
	function getType() {
		return "Resource";
	}
	
	/**
	 * Saves the resource
	 */	
	function save() {
		$this->getMapper()->save($this);
  }
	
	/**
	 * Sets additional metadata, extra to that from the primary metatron
	 *
	 * @param array $data - the data to set
	 */
	public function setAdditionalMetadata($data){
		//don't overwrite anything we already have
		$this->getMapper()->setAdditionalMetaData($data, $this);
		
		//if key is null, enter it. If it's an empty string, something has done that, so leave it alone
		
		
		
	}
	
	/**
	 * Creates a new Item based on the current Resource
	 *
	 * @return object the item
	 */
	function createItem(){
		$item = new List8D_Model_Item();
		$item->setResourceId($this->getId());

		return $item;	
	}
	
	
	/**
	 * Fetches the title of the resource
	 *
	 * @return string title
	 */
	public function getTitle() {
		
		return $this->getData('title',true);
		
	}
	
	
	public function getTypeName() {
	   return $this->_typeName;
	}
	
	public function getSearchForm() {
        
      $form = new List8D_Form();
        
	    // Set the method for the display form to POST
      $form->setMethod('post');
        
	    	        
	    $form->addElement('text', 'keyword', array(
	        'label' => 'Search Keywords:',
	        'filters' => array('StringTrim'),
	    ));
	    
	    $this->addMetatronSearchFields($form);
	    
	    return $form;
	   
	}
	
	public function findExisting() {

		$uniques = $this->getUniqueKeys();

		$data = array();
		
		foreach($uniques as $key =>$unique) {
			if ((is_int($key) || $key == get_class($this)) && $this->getDataValue($unique))
				$data[$unique] = $this->getDataValue($unique);
		}

		if (empty($data))
			return false;
			
		return $this->findByData($data);
		
	}
	
	public function getUniqueKeys() {
	  	
	  $metatronHandler = new List8D_Model_Metatron_Handler();
		$return = array_merge($this->_unique,$metatronHandler->getUniqueKeys($this->getType()));
		
		return $return;
	
	}
	
	public function loadMetadata($overwrite=false) {
		
		$metatronHandler = new List8D_Model_Metatron_Handler();

		$metatronHandler->loadMetadata($this,$overwrite);
		
	}
	
	public function addMetatronSearchFields(&$form) {
		
		$metatronHandler = new List8D_Model_Metatron_Handler();
		
		$form = $metatronHandler->addMetatronSearchFields($form,$this);
		
		return $form;
		
	}
	
	
	public function getExpanded() {
		return $this->_expanded;
	}
	
	public function getItems($exclude=array()) {
	
		$db = Zend_Registry::get('dbResource');
		$db = $db->getDbAdapter();
		
		$items = array();
		$queryString = "SELECT * FROM item WHERE resource_id={$this->getId()}";
		if (count($exclude))
			$queryString .= " AND id NOT IN (".implode(",",$exclude).")";
		$query = $db->query($queryString);
		while($row = $query->fetch()) {
			$item = new List8D_Model_Item();
			if ($item->find($row['id']))
				$items[] = $item;
		}
		
		return $items;
		
	}
	
	public function getLists($exclude=array()) {
		
		$lists = array();
		foreach($this->getItems() as $item) {
			if (!isset($lists[$item->getListId()]) && !in_array($item->getListId(),$exclude)) {
				$lists[$item->getListId()] = $item->getList();
			}
		}
		
		return $lists;
		
	}
	
	public function useTypeReference() {
		return false;
	}	
	
	public function getAccessResourceType() {
		return 'resource';
	}
	
	/**
	 * Returns the name of the list
	 *
	 * @return string of title
	 */
 	function __toString() {
 		if (is_string($this->getTitle()))
	 		return $this->getTitle();
	 	else
	 		return "";
 	}
 	
 	public function setCreated($date) {
 		$this->_created = $date;
 		return $this;
 	}
 	public function setUpdated($date) {
 		$this->_updated = $date;
 		return $this;
 	} 	
 	
 	public function update() {
 		
 		$this->loadMetadata(true);
 	
 	}
}