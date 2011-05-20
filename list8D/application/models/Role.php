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
* Class to describe a role
*
* @copyright  Copyright (c) 2009 University of Kent (http://www.kent.ac.uk)
* @license    http://www.gnu.org/licenses/gpl-2.0.txt     GNU General Public License, version 2
* @author list8d
*/

class List8D_Model_Role {

 	protected $_mapper;

	protected $_id;
	protected $_role;

	/**
	 * Returns the id of this role object
	 *
	 * @return integer the id
	 */
	function getId(){
		  return $this->_id;
	}
	
	/**
	 * Sets the id of this role object
	 * @param integer $value the id to set
	 * @return object
	 */
	function setId($value){
		$this->_id=$value;
		return $this;
	}

	/**
	 * Returns the name of this role
	 * @return object
	 */
	function getRoleName(){
		return $this->_role;
	}
	
	/**
	 * Sets the role name of this role
	 *
	 * @param string $value the name to set
	 * @return object
	 */
	function setRoleName($value){
		$this->_role=$value;
		return $this;
	}
	
	/**
	 * Sets the mapper for this role
	 *
	 * @param object $mapper the mapper object
	 * @return object
	 */
 	function setMapper($mapper)
 	{
 	    $this->_mapper = $mapper;
 	    return $this;
 	}
 	
	/**
	 * Gets the mapper object for this role
	 *
	 * @return object
	 */
 	public function getMapper()
 	{ 
 	    if (null === $this->_mapper) {
 	        $this->setMapper(new List8D_Model_RoleMapper());
 	    }
 	    return $this->_mapper;
 	}
 	
	/**
	 * Saves this role
	 */
 	public function save() {
 	    $this->getMapper()->save($this);
 	}
	
	/**
	 * Deletes this role
	 */
	function delete() {
		$this->getMapper()->delete($this);
	}
 	
	/**
	 * Finds a role with the specified id
	 *
	 * @param integer $id
	 * @return object
	 */
 	public function find($id) {
 	    $this->getMapper()->find($id, $this);
 	    return $this;
 	}
 	
	/**
	 * Fetches all roles
	 * 
	 * @return array of role objects
	 */
 	public function fetchAll() {
 	    return $this->getMapper()->fetchAll();
 	}
 	
 	public function __toString() {
 		return $this->_role;
 	}

}
