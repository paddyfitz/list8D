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
* Mapper class for a list item
*
* @copyright  Copyright (c) 2009 University of Kent (http://www.kent.ac.uk)
* @license    http://www.gnu.org/licenses/gpl-2.0.txt     GNU General Public License, version 2
* @author list8d
*/ 

class List8D_Model_ItemMapper extends List8D_Model_BranchMapper {

	protected $_data_table_name = "List8D_Model_DbTable_ItemData";
	protected $_table_name = "List8D_Model_DbTable_Item";
	
	/**
	 * Saves an item and its metadata to the database
	 *
	 * @param object $item - the item object to save
	 */
	function save(List8D_Model_Item $item) {
		//$this->_db->setFetchMode(Zend_Db::FETCH_OBJ);
		
		$objectData = $this->getObjectDataArray($item);
		
		if($item->getId() === null){
			//insert
			$objectData['created'] = date('Y-m-d H:m:s');
			$objectData['updated'] = date('Y-m-d H:m:s');
			$this->getDbTable()->insert($objectData);
			$item->setId($this->getDbTable()->getAdapter()->lastInsertId());
			
			// creating a new item, so add some data by default
			//$item->setPrivateNotes('');
			//$item->setCoreText(0);
			//$item->setRecommendedForPurchase(0);
			
			// log the insert/duplicate
			if ($item->getDuplicate()) {
				$item->log(array('action'=>'duplicate', 'table'=>$this->getDbTable()->info('name'), 'id'=>$item->getId(), 'column'=>'', 'value_from'=>$item->getDuplicate(), 'value_to'=>''));
			}
			else {
				$item->log(array('action'=>'insert', 'table'=>$this->getDbTable()->info('name'), 'id'=>$item->getId()));
				
			}
			
		}
		else{
			//update
			$objectData['updated'] = date('Y-m-d H:m:s');
			
			// we have to do a select first to find out what the current values are for logging (see below)
			$existingData = $this->getDbTable()->fetchAll($this->getDbTable()->select()->where( 'id = ?', $item->getId()));
			
			$this->getDbTable()->update($objectData, "id = ".$item->getId());
			
			// log changes
			// go through every piece of data for the object and if it's changed, make a separate log entry for it
			foreach ($objectData as $key=>$value) {
				if ($objectData[$key] != $existingData[0][$key]) {
					$item->log(array('action'=>'update', 'table'=>$this->getDbTable()->info('name'), 'id'=>$item->getId(), 'column'=>$key, 'value_from'=>$existingData[0][$key], 'value_to'=>$objectData[$key]));
				}
			}
		}
		$this->saveData($item);
	}
	
	/**
	 * Returns all items for a given list id
	 *
	 * @param integer $id - the id of the list for which to return the list items
	 * @return array of items as objects
	 */
	function getItems($list_id) {

		//fetch all items from the db where the list_id = $list_id
		$itemsResultSet =	$this->getDbTable()->fetchAll(
			$this->getDbTable()->select()
				->where( 'list_id = ?', $list_id)
				->order('order')
		);
		
		//create empty return array
		$items = array();

		//for each result returned in resultSet create Item object and fill
		foreach($itemsResultSet as $itemResult) {
			$class = $itemResult->class;
			$item = new	$class();
			$item->setId($itemResult->id)
				->setPosition($itemResult->order)
				->setListId($itemResult->list_id)
				->setClass($itemResult->class)
				->setResourceId($itemResult->resource_id)
				->setOrder($itemResult->order)
				->setStart($itemResult->start)
				->setEnd($itemResult->end)
				->setCreated($itemResult->created)
				->setUpdated($itemResult->updated)
				->setAuthor($itemResult->author);
			//fill with stuff from its item_data table.
			$item->getData();
			$items[$item->getId()] = $item;
		}

		return $items;
	}
	
	/**
	 * Populates an empty item with data from the row in the database with the given id
	 *
	 * @param integer $id - the id of the item
	 * @param object $item - empty item to populate
	 */
	function find($id, List8D_Model_Item $item) {
		$result = $this->getDbTable()->find($id);
		if (0 == count($result)) {
			return;
		}

		$row = $result->current();
		$item->setId($row->id)
				->setListId($row->list_id)
				->setClass($row->class)
				->setResourceId($row->resource_id)
				->setOrder($row->order)
				->setStart($row->start)
				->setEnd($row->end)
				->setCreated($row->created)
				->setUpdated($row->updated)
				->setAuthor($row->author);
			//fill with stuff from its item_data table.
			$item->getData();

	}
	
	//maybe make this abstract
	/**
	 * Returns the metadata held in the object data array
	 *
	 * @param object $item the item from which to return the metadata
	 * @return array of metadata
	 */
	function getObjectDataArray($item){
		$dataArray = array(
				'list_id' => $item->getListId(),
				'class' => "List8D_Model_Item",
				'resource_id' => $item->getResourceId(),
				'order' => $item->getOrder(),
				'start' => $item->getStart(),
				'end' => $item->getEnd(),
				'created' => $item->getCreated(),
				'updated' => $item->getUpdated(),
				'author' => $item->getAuthor(),
			);
		return $dataArray;
	}

}