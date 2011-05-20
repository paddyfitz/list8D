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
 * Function for sorting lists
 */
function List8D_SortBy_Order($a,$b) {

	if ($a->getOrder()==$b->getOrder()) {
		return 0;
	}
	
	if ($a->getOrder()>$b->getOrder()) {
		return 1;
	} else {
		return 0;
	}
	
}
 
/**
* Class to describe a list
*
* @copyright  Copyright (c) 2009 University of Kent (http://www.kent.ac.uk)
* @license    http://www.gnu.org/licenses/gpl-2.0.txt     GNU General Public License, version 2
* @author list8d
*/

/**
 * Description of List
 *
 * @author list8d
 */
class List8D_Model_List extends List8D_Model_Branch {
    
    protected $_itemsArray;
    protected $_itemsLoaded = false;
    public $orderChanged = false;
	protected $_items;
	protected $_childLists;
	protected $_removedChildLists;
	protected $_removedItems;
	protected $_author;
	
	public $_data = array(
    'title'=>array(
	  	'title'=>'Title',
	  	'type'=>'text',
	  ),
    'code'=>array(
	  	'title'=>'Code',
	  	'type'=>'text',
	  ),
    'year'=>array(
	  	'title'=>'Year',
	  	'type'=>'int',
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
    'private_notes'=>array(
	  	'title'=>'Instructions for librarians',
	  	'type'=>'multiline',
	  ),
    'public_notes'=>array(
	  	'title'=>'Notes for students',
	  	'type'=>'multiline',
	  ),/*

		'week_beginning' => array(
			'title' => 'Week Beginning',
			'type' => 'int',
		),
*//*

    'module_version'=>array(
	  	'title'=>'Module version',
	  	'type'=>'text',
	  ),
*/
    /*
'campus'=>array(
	  	'title'=>'Campus',
	  	'type'=>'text',
	  ),
    'faculty'=>array(
	  	'title'=>'Faculty',
	  	'type'=>'text',
	  ),
*/
    'department'=>array(
	  	'title'=>'Department',
	  	'type'=>'text',
	  ),
	  'convener' => array(
	  	'title'=>'Convener',
	  	'type'=>'users',
	  ),
		'student_numbers' => array(
			'title' => 'Number of Students',
			'type' => 'int',
		),/*

    'sds_id'=>array(
	  	'title'=>'SDS ID',
	  	'type'=>'text',
	  ),
*/
		'stage'=>array(
			'title'=>'Stage',
			'type'=>'text',
		),
		'credits'=>array(
			'title'=>'Credits',
			'type'=>'text',
		),/*

		'faculty'=>array(
			'title'=>'Faculty',
			'type'=>'text',
		),
*//*

		'institution'=>array(
			'title'=>'Institution',
			'type'=>'text',
		),
*/
  );
	
	function __construct(){
		parent::__construct();
	}
	
	function setAuthor($v) {
		$this->_author = $v;
		return $this;
	}
	
	function getAuthor() {
		return $this->_author;
	}
	
	/**
	 * Sets or returns the title field of a list
	 * @param string $value - if not null, the value to set.
	 * @return either itself, if value set, or the title of the list, if value not given
	 */
	function title($value = null) {
		
		if( $value != null ) {
			$this->_data['title'] = $value;
			return $this;
		} else {
			return $this->_data['title'];
		}

	}
	
	/**
	 * Sets or returns the code field of a list
	 * @param string $value - if not null, the value to set.
	 * @return either itself, if value set, or the code of the list, if value not given
	 */ 
	function code($value = null) {
		if( $value != null ) {
			$this->_data['code'] = $value;
			return $this;
		} else {
			return $this->_data['code'];
		}	
	}
	
	/**
	 * Sets or returns the is_published field of a list
	 * @param string $value - if not null, the value to set.
	 * @return either itself, if value set, or the is_published value of the list, if value not given
	 */
	function is_published($value = null) {
		
		if( $value != null ) {
			$this->_data['is_published'] = $value;
			return $this;
		} else {
			return $this->_data['is_published'];
		}

	}
	
	/**
	 * Saves the list and its metadata to the database
	 *
	 * @param array $data the metadata to persist
	 */
	function save($saveChildren=false) {

		//remove any items from 'items' table where they're in the removedItems array
		if ($this->_removedItems != null) {
			foreach($this->_removedItems as $removedItem){
				$listItem = new List8D_Model_Item();
				$listItem = $listItem->getItem($removedItem->getId());
				$listItem->delete($listItem);
			}
		}
		$this->getMapper()->save($this, $saveChildren);

	}
	
	/**
	 * Searches for a list with the given title
	 * @param string $title - the title to search for
	 * @return object a populated list object with the data of the list item with the given title
	 */
	function searchTitle($title) {
		
		return $this->getMapper()->searchTitle($title,$this);
		
	}
	
	/**
	 * Searches for a list which has the given data criteria in its metadata
	 *
	 * @param array $criteria the search criteria
	 * @return object a populated list object
	 */
	function searchData($criteria){
		return $this->getMapper()->searchData($criteria);
	}
	
	/**
	 * Returns all lists who have no parent
	 *
	 * @return array of list object
	 */
 	public function getTrunks() {
 			
 			return $this->getMapper()->getTrunks();
 			
 	}
	
	/**
	 * Returns a sorted list of the items on this list
	 *
	 * @return array of items sorted by the List8D Sort Order
	 */
	public function getItems() {

		if(empty($this->_items)) {
			$this->_items = $this->getMapper()->getItems($this);
		}
		uasort($this->_items,'List8D_SortBy_Order');
  	return $this->_items;
    
	}
	
	/**
	 * Returns an array of the list's structure with out initiating any objects or having any access to the items' data.
	 *
	 * @return array representing the list
	 */
    public function getItemsArray() {
        
        if ($this->_itemsArray === null) {
            $this->_itemsArray = $this->getmapper()->getItemsArray($this->getId());
        }
        if(!function_exists('List8D_SortArrayByOrder')) {
            function List8D_SortArrayByOrder($a,$b) {
            	if ($a['order']==$b['order']) {
            		return 0;
            	}
            	
            	if ($a['order']>$b['order']) {
            		return 1;
            	} else {
            		return 0;
            	}
            	
            }
        }
        uasort($this->_itemsArray,'List8D_SortArrayByOrder');
        return $this->_itemsArray;
            
    }
	
 	/**
	 * Returns the list's internal array of removed items
	 *
	 * @return array detailing the removed items
	 */
	function getRemovedItems() {
		return $this->_removedItems;
	}
  	
	/**
	 * Returns the title of the list
	 *
	 * @return string the title of the list
	 */
	public function getTitle() {

  		return $this->getData('title',true);
  	
  	}
  
  	/**
	 * Sets the title of the list
	 *
	 * @param string $title - the title to set
	 * @return object itself
	 */
 	public function setTitle($title) {
  
  		return $this->setData('title',$title);
  
  	}
  
	/**
	 * Returns the module code that this list relates to
	 * 
	 * @return string the module code
	 */
	public function getCode() {
  		return $this->getData('code',true);
  	}
  	
	/**
	 * Sets the module code that this list relates to
	 *
	 * @param string $value - the module code to set
	 * @return object itself
	 */
 	public function setCode($value) {
  		return $this->setData('code',$value);
  	}
  	
	/**
	 * Returns the year that this list is for
	 * 
	 * @return int the module's delivery year
	 */
	public function getYear() {
  		return $this->getData('year',true);
  	}
  	
	/**
	 * Sets the year that this list if for
	 *
	 * @param string $value - the module year to set
	 * @return object itself
	 */
 	public function setYear($value) {
  		return $this->setData('year',$value);
  	}
  	
	/**
	 * Returns whether or not this list is published
	 *
	 * @return boolean value
	 */
  	public function getIsPublished() {

  		return $this->getData('is_published',true);
  	
  	}
  	
	/**
	 * Sets whether or not this list is published
	 *
	 * @param boolean $is_published - value for whether or not this list is published
	 * @return object itself
	 */
  	public function setIsPublished($is_published) {
  
  		return $this->setData('is_published',$is_published);
  
  	}

	/**
	 * Returns the type of this object, in this case, list
	 * 
	 * @return string simple string - "list"
	 */
	function getType() {
		return "List";
	}
	
	/**
	 * Returns all the items on the list described by the given id
	 * 
	 * @param integer $list_id the db table id of the list for which we wish to get all items
	 * @return array of item objects
	 */
	public function getItemsForList($list_id) {
		return $this->getMapper()->getItems($list_id);
	}
	
	/**
	 * Returns any child lists that might be attached to this list
	 *
	 * @return array of child lists
	 */
	function getChildLists() {
	  	if ($this->_childLists==null) {
	  		$this->_childLists = $this->getMapper()->getChildLists($this);
	  	}
		uasort($this->_childLists,'List8D_SortBy_Order');
		return $this->_childLists;
	}
	
	/**
	 * Returns an array of child lists that this list has internally kept track of
	 *
	 * @return array of removed child lists
	 */
	function getRemovedChildLists() {
		return $this->_removedChildLists;
	}
	
	/**
	 * Returns a sorted, merged array of items and child lists belonging to this list
	 *
	 * @return array of items and child lists
	 */
	function getChildren() {
		
		if (!isset($this->_children) || $this->_children===null) {
			$items = $this->getItems();
			$lists = $this->getChildLists();
			$this->_children = array_merge($items,$lists);
			uasort($this->_children,'List8D_SortBy_Order');
			$i=0;
			
			foreach($this->_children as $child) {
				if ($i!=$child->getPosition()) {
					$child->setPosition($i);
					$child->save();
				}
				$i++;
			}
		}
		
		return $this->_children;
		
	}
	
	/**
	 * Returns the number of items and child lists belonging to this list
	 *
	 * @return integer count of number of items and child lists
	 */
	function getLength() {
		return count($this->getChildren());
	}
	
	/**
	 * Adds an item to a list
	 * @param object $resource - the resource that the item pertains to
	 */
	function addItem($resource,$position=null){
		
		$item = $resource->createItem();
		$item->setListId($this->getId());
		
		$item->setPosition($this->getLength()+1);
		if ($position!==null) {
			$item->moveTo($position);
		}

		$item->save();
		
		return $item;
	}
	
	/**
	 * Returns whether or not this object is a list (which it is)
	 *
	 * @return true
	 */
	function isList() {
		return true;
	}

	/**
	 * Returns whether or not this list has a parent
	 *
	 * @return boolean true or false
	 */
	function isNested() {
		if ($this->getListId()){
			return true;
		} else {
			return false;
		}
		
	}
	
	/**
	 * Returns whether or not this list contains the item with the given id
	 * 
	 * @param integer $id - the id of the list item to check for
	 * @return boolean
	 */
	public function hasItem($id) {
		if ($id instanceof List8D_Model_Item) {
			$id = $id->getId();
		}
		foreach($this->getItems() as $item) {
			if ($item->getId() == $id) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Removes the item (given by the id) from the list
	 *
	 * @param integer $id - the id of the item to remove from the list
	 * @return object itself
	 */
	public function removeItem($id) {
		if ($id instanceof List8D_Model_Item) {
			$id = $id->getId();
		}
		$_items = $this->getItems();
		$_removedItems = array();
		$this->_removedItems[$id] = $this->_items[$id];
		unset($this->_items[$id]);
		$this->save();
		return $this;
	}
	
	/**
	 * Returns whether or not this list contains the child list with the id given
	 *
	 * @param integer $id - the id of the list to check for
	 * @return boolean
	 */
	public function hasChildList($id) {
		if ($id instanceof List8D_Model_List) {
			$id = $id->getId();
		}
		foreach($this->getChildLists() as $item) {
			if ($item->getId() == $id) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Removes the child list with the specified id from this list
	 *
	 * @param integer $id - the id of the child list to remove
	 * @return object itself
	 */
	public function removeChildList($id) {
		if ($id instanceof List8D_Model_List) {
			$id = $id->getId();
		}
		$this->_removedChildLists[$id] = $this->_childLists[$id];
		unset($this->_childLists[$id]);
		return $this;
	}
	
	/**
	 * Adds a list to this one as a child
	 *
	 * @param object $list - the list to add
	 * @return object itself
	 */
	public function addChildList($list) {
		
		if (!($list instanceof List8D_Model_Item) && is_numeric($list)) {
			$list = new List8D_Model_List();
			$list = $list->getById($list);
		}
		
		$this->_childLists[$list->getId()] = $list;
		uasort($this->_childLists,'List8D_SortBy_Order');
		unset($this->_childLists[$list->getId()]);
		return $this;
		
	}
 
	function getTags($direction='none') {
		
		if (!isset($this->_tagCache[$direction])) {	
			$this->_tagCache[$direction] = array();
			foreach($this->getMapper()->getTags($this,$direction) as $id => $namespace) {
				$this->_tagCache[$direction][$id] = new List8D_Model_Tag();
				$this->_tagCache[$direction][$id]->find($id);		
			}		
		} 
		
		return $this->_tagCache[$direction];

	}
	
	/**
	 * Returns the ids of the tags attached to this list
	 *
	 * @return array of tag ids
	 */
	function getTagIds(){
		$t = new List8D_Model_TagMap;
		return $t->getMapper()->getTagIdsByListId($this->_id);
	}
	
	/**
	 * Gets all tags for this list, including those of child lists
	 *
	 * @return array of tags
 	 */
	function getAllTags(){
		$t = new List8D_Model_TagMap;
		return $t->getAllTags($this->_id);
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

	/**
	 * Returns the (should be) single list matching the code, start and end dates
	 *
	 * @param string $code the module code
	 * @param timestamp $start the start date
	 * @param timestamp $end the end date
	 * @return object the found list
	 */
	function getByCodeStartEnd($code, $start, $end){
		return $this->getMapper()->getByCodeStartEnd($code,$start,$end);
	}
	
    /**
	 * Determine whether the _items property has been populated yet.
	 *
	 * @return boolean true if _items property has been loaded with item objects.
	 */
    public function itemsLoaded() {
        return $this->_itemsLoaded;
    }	
    
    /**
	 * Sorts items on a list, by field.
	 * List will not be sorted in the database, a query will be saved for the save() function to execute later.
	 *
	 * @param boolean $resource set to true if you would like to sort by a field from the item's resource
	 * @param string $by the index of the field to sort by 
	 * @param string $direction which way to sort 'asc' or 'desc'
	 * @param boolean $recursive set to true if you would like to sort child lists
	 * @return the list object
	 */
	public function sort($resource=true,$by="title",$direction="asc",$recursive=false) {
       
        $db = Zend_Registry::get('dbResource');
				$db = $db->getDbAdapter();

        // get the items on this list
       $items = $this->getItemsArray();
       
       // load the needed field in
       foreach($items as &$item) {

            if ((!isset($item[$by]) || $resource) && !isset($item['children'])) {
                $query = $db->query("SELECT value FROM resource_data WHERE `key`='$by' AND `row_id`='{$item['id']}'");
                $result = $query->fetch();
                $item[$by]=$result['value']; 
            } else if (!isset($item[$by]) && !isset($item['children'])) {
                $query = $db->query("SELECT value FROM item_data WHERE `key`='$by' AND `row_id`='{$item['id']}'");
                $result = $query->fetch();
                $item[$by]=$result['value']; 
            } else if (!isset($item[$by])) {
                $query = $db->query("SELECT value FROM list_data WHERE `key`='$by' AND `row_id`='{$item['id']}'");
                $result = $query->fetch();
                $item[$by]=$result['value']; 
            }
            $item['_sortBy']=$item[$by];
        }    

       function _sort_list_asc_by($a,$b) {
            if ($a['_sortBy']==$b['_sortBy']) {
        		return 0;
        	}
        	if ($a['_sortBy']>$b['_sortBy']) {
        		return 1;
        	} else {
        		return -1;
        	}
       }
       function _sort_list_desc_by($a,$b) {
            if ($a['_sortBy']==$b['_sortBy']) {
        		return 0;
        	}
        	if ($a['_sortBy']>$b['_sortBy']) {
        		return -1;
        	} else {
        		return 1;
        	}
       }
       
       uasort($items,'_sort_list_'.$direction.'_by');
        if (!function_exists("_sort_list_change_order")) {
	        function _sort_list_change_order ($items,$recursive) {
	        
	            $i=0;
            	   foreach($items as &$item) {
	               $item['order']=$i;
	                if(isset($item['children']) && $recursive)
                         $item['children']=_sort_list_change_order($item['children'],$recursive);
	                $i++;
	                // build query
	            	   
                 }
                 return $items;
             }
       }
       $items = _sort_list_change_order($items,$recursive);
       $this->orderChanged = true;
       $this->_itemsArray = $items;
       
       if ($this->_itemsLoaded) {
            foreach($this->_items as $itemObject) {
                $itemObject->setPosition($items[$itemObject->getid()]['order']);
            }
       }
       
	}

	function quickLoad($select=false,$limit=0,$offset=0) {
		return $this->quickLoadNew($select,$limit=0,$offset=0);
	}
	
	function quickLoadNew($select=false,$limit=0,$offset=0) {

		// we need to do this the old fashion way (ie with out pdo)
		// because of bugs in pdo that cause problems with the "uber query"
		global $application;
		$options = $application->getOptions();
		
		$lists = array();
		$items = array();
		$resources = array();
		
		$link = mysql_connect($options['resources']['db']['params']['host'], $options['resources']['db']['params']['username'], $options['resources']['db']['params']['password']);
		mysql_set_charset('utf8');
		if (!$link) {
    	throw new Zend_Db_Exception('Could not connect to MySQL database: ' . mysql_error());
		}
		if (!mysql_select_db($options['resources']['db']['params']['dbname'],$link)) 
			throw new Zend_Db_Exception("Could not select database {$options['resources']['db']['params']['dbname']}: " . mysql_error(),mysql_errno());
		
		$result = mysql_query("DROP PROCEDURE IF EXISTS quick_load");
		if (!$result) {
    	throw new Zend_Db_Statement_Exception('Invalid query: ' . mysql_error(),mysql_errno());
		}
		
    if ($select) {
			foreach($select as $key) {
				$selectWhere[] = "`key`='$key'";  
			}
			$selectWhere = " AND (".implode(" OR ",$selectWhere).")";
		} else {
			$selectWhere = "";
		}
		
		$result = mysql_query("CREATE PROCEDURE quick_load()
BEGIN
  DROP TABLE IF EXISTS quick_load_list;
  CREATE TEMPORARY TABLE quick_load_list CHARACTER SET utf8
    SELECT *, 1 AS resource_id
    FROM list
    WHERE id = '{$this->getId()}'
  	ORDER BY `order`;
  ALTER TABLE quick_load_list ADD PRIMARY KEY(id);
  DROP TABLE IF EXISTS quick_load_item;
  CREATE TEMPORARY TABLE quick_load_item CHARACTER SET utf8
  	SELECT id, list_id, `order`, class, created, updated, author, `start`, `end`, resource_id
  	FROM item
  	WHERE list_id = '{$this->getId()}'
  	ORDER BY item.`order`;
  REPEAT
  	DROP TABLE IF EXISTS quick_load_list2;
  	CREATE TEMPORARY TABLE quick_load_list2 CHARACTER SET utf8
  		SELECT * FROM quick_load_list;
    INSERT IGNORE INTO quick_load_list
  	  SELECT l.*, 1 AS resource_id
      FROM `list` AS l
      JOIN quick_load_list2 AS q ON l.list_id = q.id
  		ORDER BY l.`order`;
  UNTIL Row_Count() = 0 END REPEAT; 
  INSERT IGNORE INTO quick_load_item
  	SELECT l.id, l.list_id, l.`order`, l.class, l.created, l.updated, l.author, l.`start`, l.`end`, l.resource_id
    FROM `item` AS l
    JOIN quick_load_list AS q ON l.list_id = q.id;
  DROP TABLE IF EXISTS quick_load_list_data;
  CREATE TEMPORARY TABLE quick_load_list_data CHARACTER SET utf8
  	SELECT d.*
  	  FROM list_data as d
  	  JOIN quick_load_list AS q ON d.row_id = q.id
  	  WHERE 1{$selectWhere};
  DROP TABLE IF EXISTS quick_load_item_data;
  CREATE TEMPORARY TABLE quick_load_item_data CHARACTER SET utf8
  	SELECT d.*
  	  FROM item_data as d
  	  JOIN quick_load_item AS q ON d.row_id = q.id
  	  WHERE 1{$selectWhere};
  DROP TABLE IF EXISTS quick_load_resource;
  CREATE TEMPORARY TABLE quick_load_resource CHARACTER SET utf8
  	SELECT r.*
  	  FROM resource AS r 
  	  JOIN quick_load_item as i ON r.id = i.resource_id;
  DROP TABLE IF EXISTS quick_load_resource_data;
  CREATE TEMPORARY TABLE quick_load_resource_data CHARACTER SET utf8
  	SELECT d.*
  	  FROM resource_data as d
  	  JOIN quick_load_resource AS r ON d.row_id = r.id;	
END");	
		if (!$result) {
    	throw new Zend_Db_Statement_Exception('Invalid query: ' . mysql_error(),mysql_errno());
		}
		
		$result = mysql_query("CALL quick_load()");
		if (!$result) {
    	throw new Zend_Db_Statement_Exception('Invalid query: ' . mysql_error(),mysql_errno());
		}
		
		// get resources
		$result = mysql_query("SELECT * FROM quick_load_resource");
		if (!$result) {
    	throw new Zend_Db_Statement_Exception('Invalid query: ' . mysql_error(),mysql_errno());
		}
		while ($data = mysql_fetch_assoc($result)) {
			$resource = new $data['class'];
			$resource->setId($data['id']);
			$resource->setId($data['id']);
      $resource->setCreated($data['created']);
      $resource->setUpdated($data['updated']);
      $resource->dataLoaded=true;
			$resources[$data['id']] = $data;
			$resources[$data['id']]['ref'] = $resource;
		}

		
		// get lists
		$result = mysql_query("SELECT * FROM quick_load_list UNION SELECT * FROM quick_load_item");
		if (!$result) {
    	throw new Zend_Db_Statement_Exception('Invalid query: ' . mysql_error(),mysql_errno());
		}
		$this->_children = array(); $i=0;
		while ($data = mysql_fetch_assoc($result)) {
			$i++;
			$thisDone = false;
			if (!$thisDone && $data['id'] == $this->getId()) {
				$child = &$this;
				$thisDone = true;
			} else {
				$child = new $data['class'];
			}
			$data['ref'] = $child;
      $child->setId($data['id']);
      $child->setListId($data['list_id']);
      $child->setOrder($data['order']);
      $child->setCreated($data['created']);
      $child->setUpdated($data['updated']);
      $child->setStart($data['start']);
      $child->setEnd($data['end']);
      $child->setAuthor($data['author']);
      $child->setListId($data['list_id']);
      $child->dataLoaded=true;
      $child->_trunk = $this;

			if ($child instanceof List8D_Model_List) {
	      $child->_children = array();
	      $child->_items = array();
	      $child->_childLists = array();
	      if ($data['list_id']) 
	      	$lists[$data['list_id']]['ref']->_childLists[$data['order']][] = $child;
      	$lists[$data['id']] = $data;
			} elseif ($child instanceof List8D_Model_Item) {
      	$child->setResourceId($data['resource_id']);
	      $child->setResource($resources[$data['resource_id']]['ref']);
	      if ($data['list_id'])
	      	$lists[$data['list_id']]['ref']->_items[$data['order']][] = $child;
	      $items[$data['id']] = $data;
      	// load resource into item
      }
			if ($data['list_id']) {
				$child->_list = $lists[$data['list_id']]['ref'];
				$lists[$data['list_id']]['ref']->_children[$data['order']][] = $child;
			}
			
		}
		
		$this->setData('size',$i);
		 
		// merge child stuff 
		foreach($lists as &$list) {
			
			if (count($list['ref']->_children)) {
				ksort($list['ref']->_children);
				$list['ref']->_children = call_user_func_array('array_merge',$list['ref']->_children);
			}
			if (count($list['ref']->_childLists)) {
				ksort($list['ref']->_childLists);
				$list['ref']->_childLists = call_user_func_array('array_merge',$list['ref']->_childLists);
			}
			if (count($list['ref']->_items)) {
				ksort($list['ref']->_items);
				$list['ref']->_items = call_user_func_array('array_merge',$list['ref']->_items);
			}

		}
		
		// get list data
		$result = mysql_query("SELECT * FROM quick_load_list_data");
		if (!$result) {
    	throw new Zend_Db_Statement_Exception('Invalid query: ' . mysql_error(),mysql_errno());
		}
		while ($data = mysql_fetch_assoc($result)) {
		
			$value = s_unserialize($data['value']);
			$lists[$data['row_id']]['ref']->_data[$data['key']]['value'] = $value;
			$lists[$data['row_id']]['ref']->_dataValues[$data['key']] = $value;
		}

		
		// get item data
		$result = mysql_query("SELECT * FROM quick_load_item_data");
		if (!$result) {
    	throw new Zend_Db_Statement_Exception('Invalid query: ' . mysql_error(),mysql_errno());
		}
		while ($data = mysql_fetch_assoc($result)) {
			$value = s_unserialize($data['value']);
			$items[$data['row_id']]['ref']->_data[$data['key']]['value'] = $value;
			$items[$data['row_id']]['ref']->_dataValues[$data['key']] = $value;
		}
		
		// get resource data
		$result = mysql_query("SELECT * FROM quick_load_resource_data");
		if (!$result) {
    	throw new Zend_Db_Statement_Exception('Invalid query: ' . mysql_error(),mysql_errno());
		}
		while ($data = mysql_fetch_assoc($result)) {

			@$value = s_unserialize($data['value']);
			
			$resources[$data['row_id']]['ref']->_data[$data['key']]['value'] = $value;
			$resources[$data['row_id']]['ref']->_dataValues[$data['key']] = $value;
		}

		mysql_close($link);
    
    return;
		
		
	}
	
	function quickLoadOld($select=false,$limit=0,$offset=0) {

		$db = Zend_Registry::get('dbResource');
		$db = $db->getDbAdapter();
        
       /*
 // check for cache
		$query = $db->query("select cache from list_cache where id = {$this->getId()}");

		
        if ($list = $query->fetch()) {
        
            eval('$this->i'.substr($list['cache'],strpos($list['cache'],'::')+2).';');

		    return;
        }
*/
        
		// load this lists data
		if ($select) {
			foreach($select as $key) {
				$selectWhere[] = "`key`='$key'";  
			}
			$selectWhere = " AND (".implode(" OR ",$selectWhere).")";
		} else {
			$selectWhere = "";
		}

		$result = $db->query("select `key`, `value` from list_data where row_id='{$this->getId()}' and value is not null$selectWhere");
		while($data = $result->fetch()) {
		  if ($data['value'] == serialize(false) || @unserialize($data['value']) !== false)
				$value = unserialize($data['value']);
			else 
				$value = $data['value'];
			$this->setData($data['key'],$value);
			
		}
		
		$this->dataLoaded = true;
		
		
        // load the list's items
        $result = $db->query("select * from item where list_id='{$this->getId()}'");
        $this->_children = array();
        $itemPeer = new List8D_Model_Item();
        $resourceIds = array();
        while($item = $result->fetch()) {
          $itemObject = new $item['class'];
          $itemObject->setId($item['id']);
          $itemObject->setListId($item['list_id']);
          $itemObject->setResourceId($item['resource_id']);
          $resourceIds[$item['resource_id']][] = $item['id'];
          //$itemObject->setResource($resources[$item['resource_id']]);
            $itemObject->setOrder($item['order']);
            $itemObject->setCreated($item['created']);
            $itemObject->setUpdated($item['updated']);
            $itemObject->setStart($item['start']);
            $itemObject->setEnd($item['end']);
            $itemObject->setAuthor($item['author']);
            $itemObject->setList($this);
            $itemObject->dataLoaded=true;
            $this->_items[$item['id']] = $itemObject;
            //$this->_children[$item['order']] = $itemObject;
            $this->_children[$item['order']][] = $itemObject;
        }
		
		if (count($this->_items)) {
		  // load the items' data
		  //$result = $db->query("select row_id, `key`,`value` from item_data where row_id in (select id from item where list_id='{$this->getId()}')");
		  $result = $db->query("select row_id, `key`,`value` from item_data where row_id in (".implode(",",array_keys($this->_items)).")$selectWhere");
          
		  while($data = $result->fetch()) {
		  	if ($data['value'] == serialize(false) || @unserialize($data['value']) !== false)
					$value = unserialize($data['value']);
				else 
					$value = $data['value'];
		    $this->_items[$data['row_id']]->setData($data['key'],$value);
		  }
		}
		
		
		// load resources
		//$result = $db->query("select * from resource where id in (select resource_id from item where list_id='{$this->getId()}')");
		if (count($resourceIds)) {
		  $result = $db->query("select * from resource where id in (".implode(",",array_keys($resourceIds)).")");
		  $resources = array();
		  while($resource = $result->fetch()) {
		  	$resourceObject = new $resource['class'];
		  	$resourceObject->setId($resource['id']);
		  	$resourceObject->dataLoaded=true;
		  	$resources[$resource['id']] = $resourceObject;
		  	foreach($resourceIds[$resource['id']] as $itemId) {
		  	 $this->_items[$itemId]->setResource($resourceObject);
		  	}
		  }
		  // load resources data
		  //$result = $db->query("select row_id,`key`,`value` from resource_data where row_id in (select id from resource where id in (select resource_id from item where list_id='{$this->getId()}')) and value is not null");
		  if (count(array_keys($resources))) {
		    $result = $db->query("select row_id,`key`,`value` from resource_data where row_id in (".implode(",",array_keys($resourceIds)).") and value is not null$selectWhere");
		  	
		    while ($data = $result->fetch()) {
		  		if ($data['value'] == serialize(false) || @unserialize($data['value']) !== false)
						$value = unserialize($data['value']);
					else 
						$value = $data['value'];
		      $resources[$data['row_id']]->setData($data['key'],$value);
		    }
		  }

		}

		// load the list's sublists
		$result = $db->query("select * from list where list_id='{$this->getId()}'");
		while($list = $result->fetch()) {

			$listObject = new $list['class'];
      $listObject->setId($list['id']);
			$listObject->setListId($list['list_id']);
      $listObject->setOrder($list['order']);
			//$listObject->setCreated($list['created']);
			//$listObject->setUpdated($list['updated']);
      $listObject->setOrder($list['order']);
      $listObject->setStart($list['start']);
      $listObject->setEnd($list['end']);
      $listObject->setAuthor($list['author']);
      $listObject->quickLoad();
			$this->_childLists[$list['id']] = $listObject;
	    //$this->_children[$list['order']] = $listObject;
	    $this->_children[$list['order']][] = $listObject;

		}	

		ksort($this->_children);

		$tempChildren = array();
		foreach($this->_children as $child) {
			foreach($child as $item){
				$tempChildren[] = $item;
			}
		}
		$this->_children = $tempChildren;
		
		if ($limit) {
			$this->_children = array_slice($this->_children,$offset,$limit);
			$newChildLists = array();
			$newChildItems = array();
			foreach($this->_children as $child) {
				if($child->isList()) {
					$newChildLists[$child->getId()] = $this->_childLists[$child->getId()]; 
				} else {
					$newChildItems[$child->getId()] = $this->_items[$child->getId()]; 
				}
			}
			$this->_items = $newChildItems;
			$this->_childLists = $newChildLists;
		}

/*

			$listCache = var_export($this,1);

			$listCache = str_replace("\'","\\\'",$listCache);
			$db->query("replace into list_cache (id,cache) values ({$this->getId()},\"".$listCache."\")");
*/

            return;
		
		
	}
	
	public function quickLoadReturnArray($select=array(),$limit=0,$offset=0) {

		$db = Zend_Registry::get('dbResource');
		$db = $db->getDbAdapter();
        
    $return = array();
               
		// load this lists data
		if ($select) {
			foreach($select as $key) {
				$selectWhere[] = "`key`='$key'";  
			}
			$selectWhere = " AND (".implode(" OR ",$selectWhere).")";
		} else {
			$selectWhere = "";
		}

		$result = $db->query("select `key`, `value` from list_data where row_id='{$this->getId()}' $selectWhere");
		while($data = $result->fetch()) {

			$return[$data['key']] = $data['value'];
			
		}
		
    // load the list's items
    $result = $db->query("select * from item where list_id='{$this->getId()}'");
    $return['children'] = array();
    $return['isList'] = true;
        
        
    

        
        $itemPeer = new List8D_Model_Item();
        $resourceIds = array();
        while($item = $result->fetch()) {
          /*
$itemObject = new $item['class'];
          $itemObject->setId($item['id']);
          $itemObject->setListId($item['list_id']);
          $itemObject->setResourceId($item['resource_id']);
          
          //$itemObject->setResource($resources[$item['resource_id']]);
            $itemObject->setOrder($item['order']);
            $itemObject->setCreated($item['created']);
            $itemObject->setUpdated($item['updated']);
            $itemObject->setStart($item['start']);
            $itemObject->setEnd($item['end']);
            $itemObject->setAuthor($item['author']);
            $itemObject->dataLoaded=true;
            
*/					
						$resourceIds[$item['resource_id']][] = $item['id'];
						$item['isList']=false;
            foreach($data[$item['id']] as $key => $value) {
            	$item[$value] = $value;
            }
            $return['children'][$item['order']] = $item;
            $itemIds[] = $item['id'];
        }
		
		
		// load the items' data
		//$result = $db->query("select row_id, `key`,`value` from item_data where row_id in (select id from item where list_id='{$this->getId()}')");
		if (count($itemIds)) {
			
			$result = $db->query("select row_id, `key`,`value` from item_data where row_id in (".implode(",",$itemIds).") $selectWhere");
    	$dataArray = array();      
			while($data = $result->fetch()) {
			  $dataArray[$data['row_id']][$data['key']] = $data['value'];
			}	
		
			foreach($return['children'] as &$child) {
				if(is_array($dataArray[$child['id']]))
					$child += $dataArray[$child['id']];
			}
		}
		
		// load resources
		//$result = $db->query("select * from resource where id in (select resource_id from item where list_id='{$this->getId()}')");
		if (count($resourceIds)) {
		  $result = $db->query("select * from resource where id in (".implode(",",array_keys($resourceIds)).")");
		  $resources = array();
		  while($resource = $result->fetch()) {
		  	$resources[$resource['id']] = $resource;
		  	
		  }
		  // load resources data
		  //$result = $db->query("select row_id,`key`,`value` from resource_data where row_id in (select id from resource where id in (select resource_id from item where list_id='{$this->getId()}')) and value is not null");
		  if (count(array_keys($resources))) {
		    $result = $db->query("select row_id,`key`,`value` from resource_data where row_id in (".implode(",",array_keys($resourceIds)).") $selectWhere");
		    while ($data = $result->fetch()) {
		      $resources[$data['row_id']][$data['key']] = $data['value'];
		    }
		  }
		  
		  
		  foreach($return['children'] as &$child) {
		  	if (!$child['isList']) {
		  	
		  		$child['resource']=$resources[$child['resource_id']];
            $class = $child['resource']['class'];
            $child['type'] = call_user_func(array($class, 'getType'));
		  	} else {
		  		$item['type'] = 'List';
		  	}
		  }

		}

		// load the list's sublists
		$result = $db->query("select * from list where list_id='{$this->getId()}'");
		while($l = $result->fetch()) {
			
			$list = new List8D_Model_List();
			$list = $list->getById($l['id']);
			$list = $list->quickLoadReturnArray($select);		
			$list['isList']=true;	
			$list['type'] = 'List';
	    $return['children'][$l['order']] = $list;
		
		}	
		
    $return['length'] = count($return['children']);
		ksort($return['children']);
		if ($limit) {
			$return['children'] = array_slice($return['children'],$offset,$limit);
		}
		
		return $return;		
	}

	
	/*
function i__set_state($data) {
		  $this->dataLoaded = true;
		  foreach($data as $key=>$value) {
	  		$this->$key = $value;
			}
	}
*/

  public function isNestedList() {
  	return false;
  }	
  
  public function findLists($find=array(),$limit=null,$offset=0) {
  	
  	 $query = $this->getMapper()->getDbTable()->select()->where("`list_id` is null");
  	 if ($limit) {
  	 	$query->limit($limit,$offset);
  	 }
  	 $rows = $this->getMapper()->getDbDataTable()->fetchAll($query);
  	 $return = array();
  	 foreach($rows as $row) {
  	 	$list = $this->getById($row->id);
  	 	if ($list)
	  	 	$return[] = $list;
  	 }
  	return $return;
  }
  
  public function listCount() {
  	
		$db = Zend_Registry::get('dbResource');
		$db = $db->getDbAdapter();
		$query = $db->query('select count(*) from list where list_id is null');
  	$row = $query->fetch();
  	return $row['count(*)'];
  }
	
	public function getAccessResourceType() {
		return 'list';
	}
	
	
	public function duplicateRow(&$duplicate) {
 		
 		$duplicate->setListId($this->getListId())
				->setClass(get_class($this))
				->setOrder($this->getOrder())
				->setStart($this->getStart())
				->setEnd($this->getEnd())
				->setAuthor($this->getAuthor());
 		
 		return $this;
 		
 	}
 	
 	public function duplicateChildren(&$duplicate, $append = FALSE) {
 		
 		$newChildren = array();
 		foreach($this->getChildren() as $child) {
			//pre_dump($child);
 			$childDuplicate = $child->duplicate();
 			$childDuplicate->setListId($duplicate->getId());		 
 			$newChildren[] = $childDuplicate;
 		}
		if($append) {
			$duplicate->appendChildren($newChildren);
		} else {
	 		$duplicate->setChildren($newChildren);
		}

		//exit;
 	}
 	
 	public function setChildren($children) {
 		
 		unset($this->_children);
 		unset($this->_childLists);
 		unset($this->_items);
 		
 		foreach ($children as $child) {
 			$this->_children[] = $child;
 			if ($child->isList()) 
 				$this->childLists[] = $child;
 			else
 				$this->_items[] = $child;
 		} 
 		
 		return $this;
 		
 	}

	public function appendChildren($children) {

		echo "<pre>Children count before = ".count($this->_children)."</pre>";
		foreach ($children as $child) {
			echo "<pre>appending {$child->getTitle()}</pre>";
 			$this->_children[] = $child;
 			if ($child->isList())
 				$this->childLists[] = $child;
 			else
 				$this->_items[] = $child;
 		}
		echo "<pre>Children count after = ".count($this->_children)."</pre>";
		//exit;
 		return $this;
	}
 	
	/**
	 * Iterate over the lists children and make sure their positions run from 0 to the lists length.
	 *
	 * @return object $this
	 */
	public function fixPositions() {
		
		$i=0;
		foreach($this->getChildren() as $child) {
			
			$child->setPosition($i);
			$i++;	
			$child->save();
			
		
		}
		
		return $this;
		
	}


	/**
	 * This function will search the database for lists that have occurances in multiple years.
	 * It will return an array where the array index is the id of the list and the vale is the
	 * year that the lists applies to. The results will be returned in ascending order of years.
	 *
	 * eg:
	 *
	 * array {
	 *	[123] => "2009"
	 *	[456] => "2010"
	 *	[789] => "2011"
	 * }
	 */
	public function getAlternateYears() {
		//need to fetch the id's of lists with the same code and sds id

		$db = Zend_Registry::get('dbResource')->getDbAdapter();
		$query = $db->select()->from(array('l' => 'list_data'), array('l.row_id', 'l.value'))
		->join(array('l2' => 'list_data'), 'l.row_id = l2.row_id', null)
		->join(array('l3' => 'list_data'), 'l.row_id = l3.row_id', null)
		->where("l.key = 'year'")
		->where("l2.key = 'code'")
		->where("l2.value = '".serialize($this->getDataValue("code"))."'")
		->where("l3.key = 'sds_id'")
		->where("l3.value = '".serialize($this->getDataValue("sds_id"))."'")
		->order("l.value");

		//echo $query;
		//exit;

		$results = $db->fetchAll($query);

		$return = array();
		foreach($results as $result) {
			$return[$result['row_id']] = s_unserialize($result['value']);
		}

		return $return;
	}
	
 	public function setCreated($date) {
 		$this->_created = $date;
 		return $this;
 	}
 	
 	public function setUpdated($date) {
 		$this->_updated = $date;
 		return $this;
 	}
 	
 	public function getSize() {
 		
 		if (!$this->getDataValue("size")) {
 		
 			global $application;
			$options = $application->getOptions();
			
			
			$link = mysql_connect($options['resources']['db']['params']['host'], $options['resources']['db']['params']['username'], $options['resources']['db']['params']['password']);
			if (!$link) {
    		throw new Zend_Db_Exception('Could not connect to MySQL database: ' . mysql_error());
			}
			if (!mysql_select_db($options['resources']['db']['params']['dbname'],$link)) 
				throw new Zend_Db_Exception("Could not select database {$options['resources']['db']['params']['dbname']}: " . mysql_error(),mysql_errno());
			
			$result = mysql_query("DROP PROCEDURE IF EXISTS quick_load");
			if (!$result) {
    		throw new Zend_Db_Statement_Exception('Invalid query: ' . mysql_error(),mysql_errno());
			}
			
    	$result = mysql_query("CREATE PROCEDURE quick_load()
BEGIN
  DROP TABLE IF EXISTS quick_load_list;
  CREATE TEMPORARY TABLE quick_load_list
    SELECT *, 1 AS resource_id
    FROM list
    WHERE id = '{$this->getId()}'
  	ORDER BY `order`;
  ALTER TABLE quick_load_list ADD PRIMARY KEY(id);
  DROP TABLE IF EXISTS quick_load_item;
  CREATE TEMPORARY TABLE quick_load_item
  	SELECT id, list_id, `order`, class, created, updated, author, `start`, `end`, resource_id
  	FROM item
  	WHERE list_id = '{$this->getId()}'
  	ORDER BY item.`order`;
  REPEAT
  	DROP TABLE IF EXISTS quick_load_list2;
  	CREATE TEMPORARY TABLE quick_load_list2
  		SELECT * FROM quick_load_list;
    INSERT IGNORE INTO quick_load_list
  	  SELECT l.*, 1 AS resource_id
      FROM `list` AS l
      JOIN quick_load_list2 AS q ON l.list_id = q.id
  		ORDER BY l.`order`;
  UNTIL Row_Count() = 0 END REPEAT; 
  	DROP TABLE IF EXISTS quick_load_list2;
  CREATE TEMPORARY TABLE quick_load_list2
  		SELECT * FROM quick_load_list;
  INSERT IGNORE INTO quick_load_list
  	SELECT l.id, l.list_id, l.`order`, l.class, l.created, l.updated, l.author, l.`start`, l.`end`, l.resource_id
    FROM `item` AS l
    JOIN quick_load_list2 AS q ON l.list_id = q.id;
  DROP TABLE IF EXISTS quick_load_list_data;	
END");	
			if (!$result) {
    		throw new Zend_Db_Statement_Exception('Invalid query: ' . mysql_error(),mysql_errno());
			}
			
			$result = mysql_query("CALL quick_load()");
			if (!$result) {
    		throw new Zend_Db_Statement_Exception('Invalid query: ' . mysql_error(),mysql_errno());
			}
			
			// get resources
			$result = mysql_query("SELECT count(id) as count FROM quick_load_list");
			if (!$result) {
    		throw new Zend_Db_Statement_Exception('Invalid query: ' . mysql_error(),mysql_errno());
			}
			while ($data = mysql_fetch_assoc($result)) {
				$this->setData('size',$data['count']);
			}	 		
			
			mysql_close($link);	
			
 		}
		 		
 		return $this->getDataValue("size");
 		
 	}
 	
 	public function getAllYears() {
 		return $this->getMapper()->getAllYears();
 	}
 	
 	function setData($key,$value) {
		if ($key == 'is_published' && $value  != $this->getDataValue('is_published')) {
 			$this->setData('was_auto_published',false); 		
 		}
 		return parent::setData($key,$value);
 	}
 	
 	function getDocumentUrls() {
 		if (isset($this->documentUrls))
 			return $this->documentsUrls;
 			
 		global $application;
		$config = $application->getOptions();
		$config = $config['list8d']['documentStore'];
		$url_prefix = $config['url_prefix'];
		$location = $config['location'];
		$code = $this->getCode();
		
		#ssh rea8@castor ls /www/shared/wwwroot/rldocs >> /www/list8d/list8D/data/ls.txt

		if ($handle = fopen($location, "r")) {
			if (filesize($location))
				$contents = fread($handle, filesize($location));
			fclose($handle);
			$output = preg_split("/[\r\n]/",$contents);
		} else {
			throw new Zend_Db_Exception("There was a problem checking \"{$host}\" for reading list documents.");
		}
				
#		var_dump($cli); 
#		var_dump($location);
#		var_dump($output);
#		var_dump($return);
#		exit;
		
		
		$return = array();
		foreach($output as $filename) {
			if (preg_match("/^$code\..+$/",$filename)) {
				if (preg_match("/(?<=^$code\.).+(?=\..+$)/",$filename,$matches)) {
					$return["{$url_prefix}/{$filename}"] = "{$matches[0]} list document";
				} else {
					$return["{$url_prefix}/{$filename}"] = "reading list document";
				}
			}
			
		}
		
		$this->documentsUrls = $return;
		return $return;
		
	}
}
