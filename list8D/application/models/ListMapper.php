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
* Model class to map lists to their table
*
* @copyright  Copyright (c) 2009 University of Kent (http://www.kent.ac.uk)
* @license    http://www.gnu.org/licenses/gpl-2.0.txt     GNU General Public License, version 2
* @author list8d
*/

class List8D_Model_ListMapper extends List8D_Model_BranchMapper
{

	protected $_data_table_name = "List8D_Model_DbTable_ListData";
	protected $_table_name = "List8D_Model_DbTable_List";
	
	/**
	 * Saves the list and its data
	 *
	 * @param List8D_Model_List $list - the list to save
	 * @param array $data - the data array to save
	 */	 
	function save(List8D_Model_List $list, $saveChildren=false) {
		
		
		
		//need to adjust this to cope with updates as well as new
		
		//$this->_db->setFetchMode(Zend_Db::FETCH_OBJ);
		
		if (isset($_SERVER['REMOTE_USER']))
			$user = $_SERVER['REMOTE_USER'];
		else 
			$user = null;
		
		//pre_dump($list->getTitle());
		//exit;
		//if id is null, we are doing an insert
		if(null === $list->getId()){
			$data = array(
						'class' => get_class($list),
						'order' => $list->getPosition(),
						'created' => date("Y-m-d H:i:s"),
						'author' => $user,
						//'author' => '',
						'start' => $list->getStart(),
						'end' => $list->getEnd(),
						'list_id' => $list->getListId(),
			);
			//insert
			$this->getDbTable()->insert($data);
			$list->setId( $this->getDbTable()->getAdapter()->lastInsertId() );
			if($list instanceof List8D_Model_NestedList){
				$this->saveData($list);
			}
			
			// log the insert/duplicate
			if ($list->getDuplicate()) {
				$list->log(array('action'=>'duplicate', 'table'=>$this->getDbTable()->info('name'), 'id'=>$list->getId(), 'column'=>'', 'value_from'=>$list->getDuplicate(), 'value_to'=>''));
			}
			else {
				$list->log(array('action'=>'insert', 'table'=>$this->getDbTable()->info('name'), 'id'=>$list->getId()));
			}

		}
		else{

			$data = array(
				'class'=>$list->getClass(),
				'order'=>$list->getPosition(),
				//'created'=>$list->getCreated(),
				'updated'=>date("Y-m-d H:i:s"),
				'author'=>$list->getAuthor(),
				'start'=>$list->getStart(),
				'end'=>$list->getEnd(),
				'list_id' => $list->getListId(),
			);

			
			// we have to do a select first to find out what the current values are for logging (see below)
			$existingData = $this->getDbTable()->fetchAll($this->getDbTable()->select()->where( 'id = ?', $list->getId()));
			
			//update
			$this->getDbTable()->update($data, "id = ".$list->getId());
			
			// log changes
			// go through every piece of data for the object and if it's changed, make a separate log entry for it
			foreach ($data as $key=>$value) {
				if ($data[$key] != $existingData[0][$key]) {
					$list->log(array('action'=>'update', 'table'=>$this->getDbTable()->info('name'), 'id'=>$list->getId(), 'column'=>$key, 'value_from'=>$existingData[0][$key], 'value_to'=>$data[$key]));
				}
			}			
			
		}
		
		$this->saveData($list);
		
		// if the order has be changed save that
		if ($list->orderChanged) {
		  $items = $list->getItemsArray();
		  if (!function_exists("_list8d_listmapper_save_order")) {
		      function _list8d_listmapper_save_order($items) {
	   	           $db = Zend_Registry::get('dbResource');
           		$db = $db->getDbAdapter();
		          foreach($items as $item) {
		              if (!isset($item['children'])) {
                            $table = 'item';
                        } else {
                            $table = 'list';
                            _list8d_listmapper_save_order($items['children']);
                        } 
		              $db->query("UPDATE $table SET `order`={$item['order']} WHERE id={$item['id']}");
		          }
		      }
		  }
		  _list8d_listmapper_save_order($items);
		}
		
		// save the children
		if ($saveChildren) {
			foreach ($list->getChildren() as $child) {
				$child->setListId($list->getId());
				$child->save(true);
			}
		}
		

	}
	
	/**
	 * Searches lists for a given title, returning a found list
	 *
	 * @param string $title - the string title of the list
	 * @param List8D_Model_Root $root - superclass instance of the list
	 * @return List8D_Model_List list
	 */
	 
	function searchTitle($title,$root) {
		
		$list = $this->searchData(array("title"=>array("value LIKE '%$title%'")));

		return $this->getById($list,$root);
		
	}
	
	
	/**
	 * Returns all lists with no parents
	 *
	 * @return array of list objects
	 */
	function getTrunks() {

		$rows = $this->getDbTable()->fetchTrunks();
    
    	$return = array();

    	foreach ($rows as $row) {

    		$class = $row->class;
    		$row_object = new $class;
    		$row_object->getById($row->id);
      		$return[] = $row_object;
    
    	}
    
    	return $return;
    
	}
	
	/**
	 * Returns the array of items which are attached to this list
	 *
	 * @param List8D_Model_List $list the list for which we wish to get items
	 * @return array of List8D_Model_Item items
	 */
	function getItems(List8D_Model_List $list) {

		$peerItem = new List8D_Model_Item();
		$items = $peerItem->getItems($list->getId());
		
		return $items;
		
  	}
    
    /**
	 * Returns an array of the list's structure with out initiating any objects or having any access to the items' data.
	 *
	 * @param int $listId the is for the list we wish to get items for
	 * @return array representing the list
	 */
    public function getItemsArray($listId) {
    
        $db = Zend_Registry::get('dbResource');
		$db = $db->getDbAdapter();
		
		$items = array();
		$query = $db->query("SELECT * FROM item WHERE list_id=$listId");
		while($item = $query->fetch()) {
		  $items[$item['id']] = $item;
		}
		$query = $db->query("SELECT * FROM list WHERE list_id=$listId");
		while($item = $query->fetch()) {
          $item['children']=$this->getItemsArray($item['id']);
		  $items[$item['id']] = $item;
		}
		return $items;
    }
    
	/**
	 * Returns the child lists attached to this list
	 * 
	 * @param List8D_Model_List $list the list for which we wish to get the child lists
	 * @return array of List8D_Model_List child lists
	 */
  	function getChildLists($list) {
		
		$listsResultSet = $this->getDbTable()->fetchAll(
			$this->getDbTable()->select()
				->where("list_id = ?",$list->getId())
				->order('order ASC')
		);
		$lists=array();

		//for each result returned in resultSet create Item object and fill
		foreach($listsResultSet as $listResult) {
			$class = $listResult->class;
			$list = new	$class();
			$list->setId($listResult->id)
				->setListId($listResult->list_id)
				->setClass($listResult->class)
				->setPosition($listResult->order)
				->setOrder($listResult->order);
			//fill with stuff from its item_data table.
 
			$list->getData();

			$lists[] = $list;
		}

		return $lists;
		  
  	}
  	
	/**
	 * Deletes a list and all its children
	 *
	 * @param List8D_Model_Root $root - reference to superclass, the item/list to delete
	 */
  	public function delete(List8D_Model_Root $root) {
  
  		// delete child elements
		foreach($root->getChildren() as $item) {
			$item->delete();
		}
		
  		// delete list and its data
  		parent::delete($root);
  
  	}
	
	/**
	 * Utility function. Alias for searchCodeStartEnd
	 */
	public function getByCodeStartEnd($code,$start,$end){
		
		$list = new List8D_Model_List();
		
		//urgh, ok, let's be a bit manual about this...
		$list = $this->searchCodeStartEnd($code,$start,$end);

		return $list;
	}
	
	/**
	 * Finds a specific list based on its module code, start date, end date
	 *
	 * @param string $code - the module code
	 * @param timestamp $start - the start date
	 * @param timestamp $end - the end date
	 * @return List8D_Model_List $list - the list object matching the criteria
	 */
	function searchCodeStartEnd($code,$start,$end){
		
		$data = $this->getDbDataTable()->fetchAll(
			$this->getDbDataTable()->select()->where( '`key` = \'code\' and `value` = ?',$code)
		);
		
		$list_ids = array();
		
		//loop through the data pulled from the data table
		foreach($data as $dataRow){
			//if the key is found then end loop
			$list_ids[] = $dataRow->row_id;
		}
		
		$list_to_return = new List8D_Model_List();

		
		//now loop through list_ids and check start and end dates
		foreach($list_ids as $id){
			$list_to_return = $list_to_return->getById($id);
			if(($list_to_return->getStart() == $start) && ($list_to_return->getEnd() == $end)){
				return $list_to_return;
			}
		}
		return null;
	}
	
	public function getTags($list,$direction) {
		
		// Get tags for user
		$tm = new List8D_Model_TagMap;
		$tmDbTable = $tm->getMapper()->getDbTable();

		$tag = new List8D_Model_Tag;
		$tagDbTable = $tag->getMapper()->getDbTable();

		
		$result = $tmDbTable->fetchAll(
		  $tmDbTable->select(Zend_Db_Table::SELECT_WITH_FROM_PART)
		  ->setIntegrityCheck(false)
		  ->where("`list_id` = {$list->getId()}")
		  ->join('tag', 'tagmap.tag_id = tag.id')
		);

		$tagCheck = array();
		$tagCache = array();
		foreach($result as $row){
		  $tagCheck[$row->id] = $row;
		}
		$noQuery = false;
		// while tags, get child tags
		// The $tagCheck array will grow as we discover more
		// children, but only if they're not in the cache, until
		// we run out of tags to look at. Eventually $tagCache
		// should simply contain the tags we actually care about.
		while (!empty($tagCheck)) {
			
			// get the tag to check 
		  $row = array_pop($tagCheck);
		  
		  // check cache
		  if (empty($tagCache[$row->id])) {
		  	
		  	if ($direction!='none') {
		  		$query = $tagDbTable->select();
		  	} 
		  	
		  	if ($direction == 'up') {
		  		// direction is up so we need to get tags parent
		  		if ($row->parent_id !== null) {
			  		$query->where("`id` = ?",$row->parent_id);
			  	} else  {
			  		$noQuery = true;
			  	}
		  	} elseif ($direction == 'down') {
		  		// direction is down so we need to get tags children
		  		$query->where("`parent_id` = ?",$row->id);
		  	} elseif ($direction == 'both') {
		  		// direction is both so we need to get tags children and parent
		  		$query->where("`parent_id` = ?",$row->id)->orWhere("`id` = ?",$row->parent_id);
		  	}
		  	
		  	if ($direction!='none' && !$noQuery) {
		  		$result = $tagDbTable->fetchAll($query);

			  	foreach($result as $row2) {
			  		$tagCheck[$row2->id] = $row2;
			  	}
		  	}
		  	
				// store in cache
		  	$tagCache[$row->id] = $row->namespace;
		  	
		  }
			
		}
		
		return $tagCache;
		
	}
	
	function getById($id=null, $root=null) {

		$return = array();
	  	if (is_array($id)) {

	  		foreach($id as $singleId) {
	  	 		$class = get_class($this);
	  	  
				$newPeer = new $class();
				
	  			$return[] = $newPeer->getById($singleId);
	  		
	  		}
	  	
	  		return $return;
	  
	  	}

		$result = $this->getDbTable()->find($id);
		
		if (0 == count($result)) {
			return false;
		}
		
		$row = $result->current()->toArray();

		if ($root==null) {
			$root = new $row['class'];
		}

		if ($id!=null) {
			$root->setId($id);
		} 

		$root->setListId($row['list_id']);
		$root->setOrder($row['order']);
		$root->setAuthor($row['author']);
		$root->setData('start',$row['start']);
		$root->setData('end',$row['end']);


		return $root;

	}
	public function getAllYears() {
		$years = $this->getDbDataTable()->fetchAll($this->getDbDataTable()->select()->where( '`key` = ?', 'year')->group('value'));	
		$return = array();
		foreach ($years as $year) {
			$return[] = unserialize($year['value']);
		}
		return $return;
	}
}
