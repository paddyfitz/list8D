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
* Mapper class for a resource item
*
* @copyright  Copyright (c) 2009 University of Kent (http://www.kent.ac.uk)
* @license    http://www.gnu.org/licenses/gpl-2.0.txt     GNU General Public License, version 2
* @author list8d
*/

class List8D_Model_ResourceMapper extends List8D_Model_RootMapper
{

	protected $_data_table_name = "List8D_Model_DbTable_ResourceData";
	protected $_table_name = "List8D_Model_DbTable_Resource";
	
	/**
	 * Saves the resource
	 *
	 * @param object $resource the resource to save
	 */
	function save(List8D_Model_Resource $resource) {
		
		//if($resource->getCreated() == null){
		//	$resource->setCreated(date("Y-m-d H:i:s"));
		//}
		//$resource->setUpdated(date("Y-m-d H:i:s"));
		
		$objectData = $this->getObjectDataArray($resource);
		
		if($resource->getId() === null){
			//insert
			$objectData['created'] = date("Y-m-d H:i:s");
			$objectData['updated'] = date("Y-m-d H:i:s");
			$this->getDbTable()->insert($objectData);
			$resource->setId($this->getDbTable()->getAdapter()->lastInsertId());
			
			// log the insert
			$resource->log(array('action'=>'insert', 'table'=>$this->getDbTable()->info('name'), 'id'=>$resource->getId()));
		}
		else{
			//update
			$_where = $this->getDbTable()->getAdapter()->quoteInto('id = ?', $resource->getId());
			
			// we have to do a select first to find out what the current values are for logging (see below)
			$existingData = $this->getDbTable()->fetchAll($this->getDbTable()->select()->where( 'id = ?', $resource->getId()));
			
			$objectData['updated'] = date("Y-m-d H:i:s");
			$this->getDbTable()->update($objectData, $_where );
			
			// log changes
			// go through every piece of data for the object and if it's changed, make a separate log entry for it
			foreach ($objectData as $key=>$value) {
				if ($objectData[$key] != $existingData[0][$key]) {
					$resource->log(array('action'=>'update', 'table'=>$this->getDbTable()->info('name'), 'id'=>$resource->getId(), 'column'=>$key, 'value_from'=>$existingData[0][$key], 'value_to'=>$objectData[$key]));
				}
			}
			
		}
		$this->saveData($resource);
		
	}
	
	/**
	 * Finds the resource held as the specified id in the database and fills the passed resources' data array from the db
	 * 
	 * @param integer $id the id of the resource to fetch
	 * @param object $resource the resource object to populate
	 */
	function find($id, List8D_Model_Resource $resource) {
		$result = $this->getDbTable()->find($id);
		if (0 == count($result)) {
			return;
		}

		$row = $result->current();
		$resource->setId($row->id)
				->setClass($row->class);
			//fill with stuff from its item_data table.
			$resource->getData();

	}
	
	/**
	 * Returns a resource object
	 *
	 * @param array $metatronData the data to search the table for a matching resource
	 * @return integer the id of the resource matching the metatronData
	 */
	//look into the db and see if the resource is there
	function getResource($metatronData){
		
		$primaryKeyName = $metatronData['namespace']."_primaryKey";
		
		$result = $this->getDbDataTable()->fetchAll(
			$this->getDbDataTable()
			->select()
			->where("`key` = ?", $primaryKeyName)
			->where("`value` = ?",$metatronData['primaryKey'])
		);
		
		if(count($result) == 0){
			return false;
		}
		
		return $result->current()->row_id;
	}
	
	/**
	 * Returns the resource specified by the id
	 *
	 * @param integer $id - the id to of the resource entry to return
	 * @return object the resource
	 */
	function getById($id){
		$result = $this->getDbTable()->find($id);
		$class = $result->current()->class;

		$resource = new $class();
		$resource->setId($result->current()->id);
		$resource->setClass($result->current()->class);
		return $resource;
	}
	
	/**
	 * Returns the metadata array held within this resource object
	 *
	 * @return array of metadata held in this object
	 */
	//maybe make this abstract
	function getObjectDataArray($root){
		$class = $root->getClass();
		if(empty($class)) {
			$class = get_class($root);
		}
		$dataArray = array(
				'class' => $class,
			);
		return $dataArray;
	}
	
	/**
	 * Sets metadata from non-primary metatron
	 *
	 * @param array $data the metadata to add
	 * @param object $resource the resource to which to add the metadata
	 */
	function setAdditionalMetadata($data, $resource){

		$existing = $resource->getDataValues();

		foreach((array) $data as $dataKey => $dataValue){
		
			if (isset($existing[$dataKey]) && is_array($existing[$dataKey])) {
			
				$resource->addDataWhenArray($dataKey,$dataValue);
			} else if(empty($existing[$dataKey])){
				$resource->setData($dataKey, $dataValue);
			}
		}
		$this->save($resource);
	}
}