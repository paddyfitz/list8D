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
* Mapper for root class
*
* @copyright  Copyright (c) 2009 University of Kent (http://www.kent.ac.uk)
* @license    http://www.gnu.org/licenses/gpl-2.0.txt     GNU General Public License, version 2
* @author list8d
*/

class List8D_Model_RootMapper {

	protected $_dbTable;
	protected $_dbDataTable;

	protected $_data_table_name;
	protected $_table_name;



	const TABLE_STANDARD = 0;
	const TABLE_DATA = 1;

	
	/**
	 * Sets the name of the database table associated with the object
	 *
	 * @param string or object $dbTable - name of the table
	 * @param string $type - whether it is a standard or metadata table
	 * @return object itself
	 */
	public function setDbTable($dbTable,$type=List8D_Model_RootMapper::TABLE_STANDARD) {

		if (is_string($dbTable)) {
			$dbTable = new $dbTable();
		}
		if (!$dbTable instanceof Zend_Db_Table_Abstract) {
			throw new Exception('Invalid table data gateway provided');
		}
		switch ($type) {
			case List8D_Model_RootMapper::TABLE_DATA;
				$this->_dbDataTable = $dbTable;
				break;
			case List8D_Model_RootMapper::TABLE_STANDARD:
				default:
					$this->_dbTable = $dbTable;
					break;
		}

		return $this;
	}
	
	/**
	 * Gets the metadata table for the object
	 *
	 * @return object
	 */
	public function getDbDataTable() {
		return $this->getDbTable(List8D_Model_RootMapper::TABLE_DATA);
	}

	/**
	 * Gets the master table for the object
	 *
	 * @param string $type - defaults to standard (non-metadata) table
	 * @return object table
	 */
	public function getDbTable($type=List8D_Model_RootMapper::TABLE_STANDARD) {
		if (null === $this->_dbTable) {

			$this->setDbTable($this->_data_table_name,List8D_Model_RootMapper::TABLE_DATA);
			$this->setDbTable($this->_table_name);
		}
		switch ($type) {
			case List8D_Model_RootMapper::TABLE_DATA:
				return $this->_dbDataTable;
				break;
			case List8D_Model_RootMapper::TABLE_STANDARD:
				default:
					return $this->_dbTable;
					break;
		}
	}
	
	/**
	 * Returns object pertaining to the row with id given
	 *
	 * @param integer $id  - integer relating to the id of the row in the appropriate database table for the object, or array of ids
	 * @param object $root - reference to the superclass of objects
	 * @return if an array of ids passed in, array of objects returned. If single id, single object returned
	 */
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

		foreach($row as $key => $value) {

			$function = "set".str_replace(" ","",ucwords(str_replace("_"," ", $key)));


			if (method_exists($root,$function)) {
				$root->$function($value);
			}
		}


		return $root;

	}
			
	/**
	 * Saves the metadata of object to the metadata table
	 *
	 * @param object $obj - the object for which the metadata should be saved
	 */
	public function saveData(List8D_Model_Root $obj){	
		//get the table name of the main table as we need that to construct the referencing column
		$tableName = $this->getDbTable()->info('name');
		
		$existingData = $this->getDbDataTable()->fetchAll(
			$this->getDbDataTable()->select()->where( 'row_id = ?', $obj->getId())
		);//maybe use toArray() here?
	
		$data = $obj->getDataValues();
		
		if (!empty($data)) {
			
			/*
			foreach($obj->getDataValues() as $key => $value){
				echo $key;
				echo $value;
				
			}
			exit;
			*/
			
			// get the data objects, which includes the id of each blob of data
			$dataObjects = $obj->getDataObjectsById($obj->getDuplicate());
			
			
							
			//for each item of data loop through
			foreach($obj->getDataValues() as $key => $value) {
				
				// covert any dangerous characters to safe one except slashes at first.
				
				
				$value = $this->_replaceDangerousCharacters($value);
				$value = $this->_replaceAmps($value);
				
				//this is the key one,serialize the array and replace slashes to double-slashes in the string of serialized array .				
				$value=str_replace('\\','\\\\',serialize($value));
				
				if(($key != "end") && ($key != "start")){
					
					//assume we havent pulled it from the database
					$found = false;
				
					$value_ori = ''; // need to know the original value for the logs

					//loop through the data pulled from the data table
					foreach($existingData as $dataRow){

						//if the key is found then end loop
						if($dataRow->key == $key){
							$found = true;
							$value_ori = $dataRow->value;
							break;
						}
					}
					
				
					$row = null;
				
					//if it is found then get the current row from the rowset and save to row
					//if not found, create a new row.  Set the values and then save.
					if( $found ) {
						$row = $existingData->current();
						$row->value = $value;
						$row->save();
						if ($value_ori != $value && !($value == 's:0:"";' && $value_ori=='')) {
							$this->log(array('action'=>'update', 'table'=>$this->getDbDataTable()->info('name'), 'id'=>$row->id, 'column'=>'value', 'value_from'=>$value_ori, 'value_to'=>$value));
						}
					}
					else {
					
						$row = $existingData = $this->getDbDataTable()->createRow(); //basically an insert
						$row->row_id = $obj->getId();
						$row->key = $key;
						$row->value = $value;
						$row->save();
						
						
						// log the insert/duplicate
						if ($obj->getDuplicate()) {
							// loop through each data object of the current obj, and log the value_from field as being the id of data object which matches key/value of the newly-created data object
							foreach ($dataObjects as $object) {
								if ($object['key'] == $key && $object['value'] == $value) {
									$this->log(array('action'=>'duplicate', 'table'=>$this->getDbDataTable()->info('name'), 'id'=>$row->id, 'column'=>'value', 'value_from'=>$object['id'], 'value_to'=>$value));
									break;
								}
							}
						}
						else {
							$this->log(array('action'=>'insert', 'table'=>$this->getDbDataTable()->info('name'), 'id'=>$row->id, 'column'=>'value', 'value_from'=>$value_ori, 'value_to'=>$value));
						}
						
					}
				}
				
				/*
				else{
					$existingList = new List8D_Model_List($obj->getId());
					
					if($key == "end"){
						$existingList->setEndDate($value);
						$existingList->save();
					}
					else{
						$existingList->setStartDate($value);
						$existingList->save();
					}
				}
				*/
				
			}
		}
	}
			
	/**
	 * Fetch a resultset containing metadata for the object with the given id
	 *
	 * @param integer $id - integer representing the id of the object we wish to fetch metadata for
	 * @return resultset result set
	 */
	public function getDataObjectsById($id) {
		$dataResultSet = $this->getDbDataTable()->fetchAll(
			$this->getDbDataTable()->select()->where("row_id = ?",$id)
		);
		return $dataResultSet;
	}
		
	/**
	 * Loads metadata for the given object into that object's data array
	 *
	 * @param object $root - the object for which we want to get metadata
	 *
	 */
	public function getData(List8D_Model_Root $root){
		
		if ($root->getId()) {
			
      $db = Zend_Registry::get('dbResource');
			$db = $db->getDbAdapter();
			
			$dataResultSet = $db->query("select * from ".$this->getDbDataTable()->getTableName()." where row_id = ".$root->getId())->fetchAll();
			
			//$dataResultSet = $this->getDbDataTable()->fetchAll(
			//	$this->getDbDataTable()->select()->where("row_id = ?",$root->getId())
			//);

			
			$data = array();
			
			foreach($dataResultSet as $dataResult){
				if ($dataResult['value'] == "0000-00-00 00:00:00")
					$dataResult['value'] = null;
				if ($dataResult['value'] == serialize(false) || @unserialize($dataResult['value']) !== false)
					$value = unserialize($dataResult['value']);
				else 
					$value = $dataResult['value'];
				$data[$dataResult['key']] = $value;
			}
			//return $data;

			$root->dataLoaded = true;

			return $data;
		}
	}
	
	/**
	 * Grabs all values from the id column in the database table pertaining to the calling object
	 *
	 * @return array of all ids
	 */		
	public function fetchAllID(){
	
		$tableName = $this->getDbTable()->info('name');
		
		$dataResultSet = $this->getDbTable()->fetchAll(
			$this->getDbTable()->select()
			 ->from($tableName, array('id'))
		);
		
		$data = array();
		foreach ($dataResultSet as $dataResult) {
			$data[] = $dataResult['id'];
		}

		return $data;
	}
	
	/**
	 * Search the metadata information for the object for the given criteria
	 *
	 * @param array $criteria - array of criteria
	 * @return array list of ids of matching objects
	 */
	function searchData($criteria) {
		
		$select = $this->getDbDataTable()->select()
			->from(array('list_data' => 'list_data'), 'row_id')
			->distinct();
		
		// get the function arguments
		$args = func_get_args();


		// Knock the first argument of as thats field

		foreach ($criteria as $key => $arg) {
				// basically doing $select->where() and using $arg as the arguments

				
				$select->where('`key` = ?',$key);
				
				
				foreach($arg as $condition) {

					$select->where($condition);
				}
				
				
//				call_user_func_array(array($select,'where'),$arg);
			
		}
						
		$matching_data = $this->getDbDataTable()->fetchAll($select);

		$list = array();
		foreach($matching_data as $list_id) {			
			$list[] = $list_id->row_id;
			
		}
		
		return $list;
	
	}
			
	/**
	 * Delete an object and its metadata from the database
	 * @param object $root - the object to delete
	 */
	public function delete(List8D_Model_Root $root) {
	
		// we need to get the ids of the data objects that are going to be deleted when the main object gets deleted
		$dataResultSet = $this->getDbDataTable()->fetchAll(
			$this->getDbDataTable()->select()->where("row_id = ?",$root->getId())
		);
		foreach($dataResultSet as $dataResult){
			// now we can log the data deletes
			$this->log(array('action'=>'delete', 'table'=>$this->getDbDataTable()->info('name'), 'id'=>$dataResult->id,'value_from'=>serialize($dataResult->toArray())));
		}
		
		// remove this object's data
		$this->getDbDataTable()->delete("row_id = ".$root->getId());
		
		// log the delete
    $this->log(array(
      'action'=>'delete',
      'table'=>$this->getDbTable()->info('name'),
      'id'=>$root->getId(),
      'value_from'=> isset( $dataResult ) ? serialize($dataResult->toArray()) : null));
		
		// remove this object row
		$this->getDbTable()->delete("id = ".$root->getId());
				
	}
			
	/**
	 * Persists logging information to the logging system
	 *
	 * @param array $data - the data to log
	 */	
	public function log($data=array()) {
		$log = new List8D_Model_Log();
		$log->save($data);
	}

	 /*
 public function save(Default_Model_Guestbook $guestbook)
		{
				$data = array(
						'email'   => $guestbook->getEmail(),
						'comment' => $guestbook->getComment(),
						'created' => date('Y-m-d H:i:s'),
				);

				if (null === ($id = $guestbook->getId())) {
						unset($data['id']);
						$this->getDbTable()->insert($data);
				} else {
						$this->getDbTable()->update($data, array('id = ?' => $id));
				}
		}

		public function find($id, Default_Model_Guestbook $guestbook)
		{
				$result = $this->getDbTable()->find($id);
				if (0 == count($result)) {
						return;
				}
				$row = $result->current();
				$guestbook->setId($row->id)
									->setEmail($row->email)
									->setComment($row->comment)
									->setCreated($row->created);
		}

		public function fetchAll()
		{
				$resultSet = $this->getDbTable()->fetchAll();
				$entries   = array();
				foreach ($resultSet as $row) {
						$entry = new Default_Model_Guestbook();
						$entry->setId($row->id)
									->setEmail($row->email)
									->setComment($row->comment)
									->setCreated($row->created)
									->setMapper($this);
						$entries[] = $entry;
				}
				return $entries;
		}
*/
	private function _replaceAmps ($value) {
		
		if (is_array($value)) {
		  foreach($value as &$v) {
		  	$v = $this->_replaceAmps($v);
		  }
		} else {
		  // amps are replaced like this to ensure we dont get &amp;amp;
		  $value = preg_replace("/&(?![A-Za-z]{0,4}\w{2,3};|#[0-9]{2,3};)/","&amp;" , $value);
		}

		return $value;
	}
	
	private function _replaceDangerousCharacters ($value) {
		
		if (is_array($value)) {
		  foreach($value as &$v) {
		  	$v = $this->_replaceDangerousCharacters($v);
		  }
		} else {
		  // amps are replaced like this to ensure we dont get &amp;amp;
		  $value = str_replace(array('"','\'','<','>',"\t",),
				array('&quot;','&#039;','&lt;','&gt;','&nbsp;&nbsp;'),
				$value);;
		}
		
		return $value;
	
	}
}
