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
* Mapper class for log objects
*
* @copyright  Copyright (c) 2009 University of Kent (http://www.kent.ac.uk)
* @license    http://www.gnu.org/licenses/gpl-2.0.txt     GNU General Public License, version 2
* @author list8d
*/

class List8D_Model_LogMapper {
	
	protected $_data_table_name = "List8D_Model_DbTable_Log";
	protected $_table_name = "List8D_Model_DbTable_Log";
	protected $_dbTable;
	
	/**
	 * Saves the log to the db
	 *
	 * @param array $info
	 */
	function save($info) {

		if (!isset($info['column']) || $info['column'] == null) {
			$info['column'] = '';
		}
		if (!isset($info['value_from']) || $info['value_from'] == null) {
			$info['value_from'] = '';
		}
		if (!isset($info['value_to']) || $info['value_to'] == null) {
			$info['value_to'] = '';
		}
		
		$user = new List8D_Model_User;
		$user->getCurrentUser();
		$userId = null;
		if (null !== $user->getId()) {
			$userId = $user->getId();
		}
		$data = array(
			'action'=>$info['action'],
			'table'=>$info['table'],
			'row_id'=>$info['id'],
			'changed'=>new Zend_Db_Expr('NOW()'),
			'user'=>$userId,
			'column'=>$info['column'],
			'value_from'=>$info['value_from'],
			'value_to'=>$info['value_to'],
		);
		//insert
		$this->getDbTable()->insert($data);
	}
	
	/**
	 * Sets the affected table
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
	 * Gets the affected db table
	 * 
	 * @return object
	 */
	public function getDbTable() {
		if (null === $this->_dbTable) {
			$this->setDbTable('List8D_Model_DbTable_Log');
		}
		return $this->_dbTable;
	}
	
	/**
	 * Gets a where clause for the array that is passed in
	 *
	 * @param array $params
	 * @return string
	 */
	public function getFilters($params=array()) {
		//action	table	row_id	changed	user
		$where = '';
		$valid_params = array('process',	'table', 'row_id', 'column', 'user');
		foreach ($params as $param=>$value) {
			if (in_array($param, $valid_params)) {
				// we can't have param names called 'action'
				if ($param == 'process') {
					$param = 'action';
				}
				// need to escape fields called 'table' in mysql
				if ($param == 'table') {
					$param = '`table`';
				}
				if ($value != '') {
					$where .= $param ."='". $value . "' AND ";
				}
			}
		}
		// dates work a little differently
		// param for start and end will be in timestamp, so convert to mysql-compatible format
		if (isset($params['start']) && $params['start'] != '') {
			$where .= " changed >= '". date("Y-m-d H:i:s", (int)$params['start']) . "' AND ";
		}
		if (isset($params['end']) && $params['end'] != '') {
			$where .= " changed <= '". date("Y-m-d H:i:s", (int)$params['end']) . "' AND ";
		}
		
		$where = substr($where, 0, -5);
		return $where;
	}
	
	
	//deprecated
	public function getLogs($params=array()) {
		//action	table	row_id	changed	user
		$where = '';
		$valid_params = array('process',	'table', 'row_id', 'column', 'user');
		foreach ($params as $param=>$value) {
			if (in_array($param, $valid_params)) {
				// we can't have param names called 'action'
				if ($param == 'process') {
					$param = 'action';
				}
				// need to escape fields called 'table' in mysql
				if ($param == 'table') {
					$param = '`table`';
				}
				if ($value != '') {
					$where .= $param ."='". $value . "' AND ";
				}
			}
		}
		// dates work a little differently
		// param for start and end will be in timestamp, so convert to mysql-compatible format
		if (isset($params['start']) && $params['start'] != '') {
			$where .= " changed >= '". date("Y-m-d H:i:s", (int)$params['start']) . "' AND ";
		}
		if (isset($params['end']) && $params['end'] != '') {
			$where .= " changed <= '". date("Y-m-d H:i:s", (int)$params['end']) . "' AND ";
		}
		
		$where = substr($where, 0, -5);
		
		if ($where != '') {
			$logsResultSet =	$this->getDbTable()->fetchAll(
				$this->getDbTable()->select()->where($where));
		}
		else {
			$logsResultSet =	$this->getDbTable()->fetchAll(
				$this->getDbTable()->select());
		}
		
		//create empty return array
		$logs = array();
		
		//for each result returned in resultSet create Item object and fill
		foreach($logsResultSet as $logResult) {
			$log = new List8D_Model_Log();
			$log->setId($logResult->id)
				->setAction($logResult->action)
				->setTable($logResult->table)
				->setRowId($logResult->row_id)
				->setChanged($logResult->changed)
				->setUser($logResult->user)
				->setColumn($logResult->column)
				->setValueFrom($logResult->value_from)
				->setValueTo($logResult->value_to);
			$logs[$log->getId()] = $log;
		}
		return $logs;
		
	}
	
	
	/**
	 * Sets data on a specific log entry based on the passed-in id
	 * 
	 * @param integer $id
	 * @param List8D_Model_Log $log
	 */
	 
	public function find($id, List8D_Model_Log $log) {
		$result = $this->getDbTable()->find($id);
		if (0 == count($result)) {
			return;
		}
		$row = $result->current();
		$log->setId($logResult->id)
			->setAction($logResult->action)
			->setTable($logResult->table)
			->setRowId($logResult->row_id)
			->setChanged($logResult->changed)
			->setUser($logResult->user)
			->setColumn($logResult->column)
			->setValueFrom($logResult->value_from)
			->setValueTo($logResult->value_to);
	}
	
}