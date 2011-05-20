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
* mapper for a role object
*
* @copyright  Copyright (c) 2009 University of Kent (http://www.kent.ac.uk)
* @license    http://www.gnu.org/licenses/gpl-2.0.txt     GNU General Public License, version 2
* @author list8d
*/

class List8D_Model_RoleMapper {
	
	protected $_dbTable;

	/**
	 * Sets the db table
	 *
	 * @param string $dbTable
	 * @return object
	 */
	public function setDbTable($dbTable) {
		if (is_string($dbTable)) {
			$dbTable = new $dbTable();
		}
		if (!$dbTable instanceof Zend_Db_Table_Abstract) {
			throw new Exception('Invalid table data gateway provided');
		}
		$this->_dbTable = $dbTable;
		return $this;
	}
	
	/**
	 * Gets the db table
	 * 
	 * @return object
	 */
	public function getDbTable() {
		if (null === $this->_dbTable) {
			$this->setDbTable('List8D_Model_DbTable_Role');
		}
		return $this->_dbTable;
	}
	
	/**
	 * Saves the role to the db
	 *
	 * @param List8D_Model_Role $role
	 */
	public function save(List8D_Model_Role $role) {
		$data = array(
			'role' => $role->getRoleName(),
			'updated' => date('Y-m-d H:i:s'),
		);

		if (null === ($id = $role->getId())) {
			$this->getDbTable()->insert($data);
			// log the insert
			$role->log(array('action'=>'insert', 'table'=>$this->getDbTable()->info('name'), 'id'=>$id));
		} else {
		
			// we have to do a select first to find out what the current values are for logging (see below)
			$existingData = $this->getDbTable()->fetchAll($this->getDbTable()->select()->where( 'id = ?', $id));
			
			$this->getDbTable()->update($data, array('id = ?' => $id));
			
			// log changes
			// go through every piece of data for the object and if it's changed, make a separate log entry for it
			foreach ($data as $key=>$value) {
				if ($data[$key] != $existingData[0][$key]) {
					$this->log(array('action'=>'update', 'table'=>$this->getDbTable()->info('name'), 'id'=>$id, 'column'=>$key, 'value_from'=>$existingData[0][$key], 'value_to'=>$data[$key]));
				}
			}
			
		}
	}

	/**
	 * Sets data on a specific role based on the passed-in id
	 * 
	 * @param integer $id
	 * @param List8D_Model_Role $role
	 */
	public function find($id, List8D_Model_Role $role) {
		$result = $this->getDbTable()->find($id);
		if (0 == count($result)) {
			return;
		}
		$row = $result->current();
		$role->setId($row->id)
			->setRoleName($row->role);
	}
	
	/**
	 * Fetches all roles
	 * 
	 * @return array of role objects
	 */
	public function fetchAll() {
		$resultSet = $this->getDbTable()->fetchAll();
		$entries   = array();
		foreach ($resultSet as $row) {
			$entry = new List8D_Model_Role();
			$entry->setId($row->id)
				->setRoleName($row->role);

			$entries[] = $entry;
		}
		return $entries;
	}
	
}
