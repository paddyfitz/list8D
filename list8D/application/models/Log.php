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
* Class to describe a log
*
* @copyright  Copyright (c) 2009 University of Kent (http://www.kent.ac.uk)
* @license    http://www.gnu.org/licenses/gpl-2.0.txt     GNU General Public License, version 2
* @author list8d
*/
class List8D_Model_Log {
	
	protected $_id;
	protected $_action;
	protected $_table;
	protected $_row_id;
	protected $_changed;
	protected $_user;
	protected $_column;
	protected $_value_from;
	protected $_value_to;
	protected $_mapper;
	
	/**
	 * Returns the action that has been logged
	 *
	 * @return string 
	 */
	public function getAction() {
		return $this->_action;
	}
	
	/**
	 * Returns the name of the table affected
	 *
	 * @return string
	 */
	public function getTable() {
		return $this->_table;
	}
	
	/**
	 * Returns the affected row id
	 *
	 * @return integer
	 */
	public function getRowId() {
		return $this->_row_id;
	}
	
	/**
	 * Returns the date at which item was changed
	 * 
	 * @return timestamp 
	 */
	public function getChanged() {
		return $this->_changed;
	}
	
	/**
	 * Returns the user who did the action
	 *
	 * @return string
	 */
	public function getUser() {
		return $this->_user;
	}
	
	/**
	 * Returns the column name affected
	 *
	 * @return string column
	 */
	public function getColumn() {
		return $this->_column;
	}
	
	/**
	 * Returns the original value in the db before the change
	 *
	 * @return string 
	 */
	public function getValueFrom() {
		return $this->_value_from;
	}
	
	/**
	 * Returns the new value in the db after the change
	 *
	 * @return string
	 */
	public function getValueTo() {
		return $this->_value_to;
	}
	
	/**
	 * Sets the action that has occurred
	 *
	 * @param string $action
	 * @return object
	 */
	public function setAction($action) {
		$this->_action = $action;
		return $this;
	}
	
	/**
	 * Sets the table that is affected
	 *
	 * @param string $table
	 * @return object
	 */
	public function setTable($table) {
		$this->_table = $table;
		return $this;
	}
	
	/**
	 * Sets the row id affected
	 *
	 * @param integer $row_id
	 * @return object
	 */
	public function setRowId($row_id) {
		$this->_row_id = $row_id;
		return $this;
	}
	
	/**
	 * Sets the date at which the item was changed
	 *
	 * @param timestamp $changed
	 * @return object
	 */	 
	public function setChanged($changed) {
		$this->_changed = $changed;
		return $this;
	}
	
	/**
	 * Sets the user who did the change
	 *
	 * @param string $user
	 * @return object
	 */
	public function setUser($user) {
		$this->_user = $user;
		return $this;
	}
	
	/**
	 * Sets the column name affected
	 *
	 * @param string $column
	 * @return object
	 */
	public function setColumn($column) {
		$this->_column = $column;
		return $this;
	}
	
	/**
	 * Sets the original value in the db
	 *
	 * @param string $value_from
	 * @return object
	 */
	public function setValueFrom($value_from) {
		$this->_value_from = $value_from;
		return $this;
	}
	
	/** Sets the new value in the db
	 * 
	 * @param string $value_to
	 * @return object
	 */
	public function setValueTo($value_to) {
		$this->_value_to = $value_to;
		return $this;
	}
	
	/**
	 * Saves
	 * 
	 * @param array $data
	 */
	function save($data=array()) {
		$this->getMapper()->save($data);
	}
	
	/**
	 * sets the mapper class for the logger
	 *
	 * @return object
	 */
	function setMapper($mapper)
 	{
 	    $this->_mapper = $mapper;
 	    return $this;
 	}
 	
	/**
	 * Returns the mapper class for the logger
	 *
	 * @return object
	 */
	public function getMapper()
 	{ 
 	    if (null === $this->_mapper) {
 	        $this->setMapper(new List8D_Model_LogMapper());
 	    }
 	    return $this->_mapper;
 	}
 	
	/**
	* Gets an sql where clause for the given parameters
	* 
	* @param array $params
	* @return array
	*/
 	public function getFilters($params=array()) {
		return $this->getMapper()->getFilters($params);
	}
 	
	/**
	 * Gets the logs for the given parameters
	 * 
	 * @param array $params
	 * @return array
	 */
 	public function getLogs($params=array()) { 
		return $this->getMapper()->getLogs($params);
	}
	
	/**
	 * Gets a specific log specified by id
	 *
	 * @param integer $id
	 * @return object
	 */
	public function getLog($id) {
		$this->getMapper()->find($id, $this);
		return $this;
	}	
}