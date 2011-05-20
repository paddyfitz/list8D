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
* Root abstract class for lists and list items
*
* @copyright  Copyright (c) 2009 University of Kent (http://www.kent.ac.uk)
* @license    http://www.gnu.org/licenses/gpl-2.0.txt     GNU General Public License, version 2
* @author list8d
*/
abstract class List8D_Model_Branch extends List8D_Model_Root {
	
	protected $_start;
	protected $_end;
	protected $_created;
	protected $_author;
	protected $_position;
	protected $_order;
	protected $_class;
	protected $_listId;
	public $_list;
	public $_trunk;
	
	/**
	 * Fetches the start date for a date-bound object
	 *
	 * @return timestamp start date
	 */
	function getStart(){
		if ($this->_start && $this->_start!= "0000-00-00 00:00:00") {
			return $this->_start;
		} else {
			return null;
		}
	}
	
	/**
	 * Fetches the start date for a date-bound object, formatted in the specified way or default
	 *
	 * @param string $format - the optional format to control how the date is returned
	 * @return string formatted start date
	 */
	function getStartDate($format=false) {
		
		if ($this->_start && $this->_start!= "0000-00-00 00:00:00") {
		
			if ($format) {
	  		return date($format,strtotime($this->_start));
	  	} else {
	  		$themeSettings = new Zend_Config_Ini(APPLICATION_PATH."/configs/theme.ini");
	  		if ($themeSettings->defaultDateFormat) {
	  			return date($themeSettings->defaultDateFormat,strtotime($this->_start));
	  		} else {
	  			return date('l jS \of F Y',strtotime($this->_start));
	  		}
	  	}
	  	
			return $this->_start;
		} else {
			return null;
		}
	}
	
	/**
	 * Fetches the end date for a date-bound objects
	 *
	 * @return timestamp end date
	 */
	function getEnd(){

		if ($this->_end && $this->_end != "0000-00-00 00:00:00") {
			return $this->_end;
		} else {
			return null;
		}
	}
	
	/**
	 * fetches the end date for a date-bound object, formatted in the specified way or default
	 *
	 * @param string $format - the optional format to control how the date is returned
	 * @return string formatted end date
	 */
	function getEndDate($format=false) {
		if ($this->_end && $this->_end!= "0000-00-00 00:00:00") {
			if ($format) {
	  		return date($format,strtotime($this->_end));
	  	} else {
	  		$themeSettings = new Zend_Config_Ini(APPLICATION_PATH."/configs/theme.ini");
	  		if ($themeSettings->defaultDateFormat) {
	  			return date($themeSettings->defaultDateFormat,strtotime($this->_end));
	  		} else {
	  			return date('l jS \of F Y',strtotime($this->_end));
	  		}
	  	}
	  
			return $this->_end;
		} else {
			return null;
		}
	}
	
	/**
	 * Fetches the order position of the calling object
	 *
	 * @return integer the order of the item in the containing list
	 */
	function getOrder(){
		return $this->_order;
	}
	
	/**
	 * Fetches the class of the calling object
	 *
	 * @return string the class
	 */
	function getClass(){
		return $this->_class;
	}
	
	/**
	 * Sets the start date of a date-bound object
	 *
	 * @param timestamp $start - the start date
	 * @return object itself
	 */
	function setStart($start){
		if ($start=="0000-00-00 00:00:00")
			$start = null;
		if($this->getEnd() == null){
			$this->_start = $start;
			$this->_data['start']['value'] = $start;
			$this->_dataValues['start'] = $start;
		}
		else{
			if(strtotime($start) < strtotime($this->getEnd()) ){
				$this->_start = $start;
				$this->_data['start']['value'] = $start;
				$this->_dataValues['start'] = $start;
			}
		}
		return $this;		
	}
	
	/**
	 * Utility function, alias for setStart
	 *
	 * @param timestamp $_start - the date to start
	 */
	function setStartDate($_start){
		if ($_start=="0000-00-00 00:00:00")
			$_start = null;

		$this->setStart($_start);
	}
	
	/**
	 * Sets the end date of a date-bound object
	 *
	 * @param timestamp $end - the end date
	 * @return object itself
	 */
	function setEnd($end){
		if ($end=="0000-00-00 00:00:00")
			$end = null;
		if($this->getStart() == null){
			$this->_end = $end;
			$this->_data['end']['value'] = $end;
			$this->_dataValues['end'] = $end;
		}
		else{
			if(strtotime($end) > strtotime($this->getStart())){
				$this->_end = $end;
				$this->_data['end']['value'] = $end;
				$this->_dataValues['end'] = $end;
			}
		}
		return $this;
	}
	
	/** 
	 *
	 * Utility function - alias for setEnd()
	 * @param timestamp $_end the end date
	 */
	function setEndDate($_end){
		if ($_end=="0000-00-00 00:00:00")
			$_end = null;
		$this->setEnd($_end);
	}
	
	/**
	 * Sets the order placement of the object in an ordered list
	 *
	 * @param integer $_order - the placement to give to the object
	 * @return object itself
	 */
	function setOrder($_order){
		$this->_order = $_order;
		return $this;
	}
	
	/**
	 * Sets the class of the object
	 *
	 * @param string $_class - the class to set
	 * @return object itself
	 */
	function setClass($_class){
		$this->_class = $_class;
		return $this;
	}
	
	/**
	 * Utility function - alias for setListId()
	 *
	 * @param integer $_listId - the id to set
	 */
	function setList_id($_listId) {
		$this->setListId($_listId);
	}
	
	/**
	 * Fetch the created date of the object where appropriate
	 *
	 * @return timestamp created date
	 */
	function getCreated() {
		return $this->_created;
	}
	
	/**
	 * Sets the id in the array of a list object. Does not persist.
	 * 
	 * @param integer $_listId the id to set
	 * @return object itself
	 */
	function setListId($_listId) {
		$this->_listId = $_listId;
		return $this;
	}

	/**
	 * Fetches the array-held id of the list
	 *
	 * @return integer id of the list
	 */
	public function getListId() {
		return $this->_listId;
	}
	
	public function getTrunkId() {
		$item = $this;
		while ($item2=$item->getList()) {
			$item = $item2;
		}
		return $item->getId();
	}
	
	public function getTrunk() {
		if (!isset($this->_trunk)) {
			$item = $this;
			while ($item2=$item->getList()) {
				$item = $item2;
			}
			$this->_trunk = $item;
		}
		return $this->_trunk;
	}
	
	/**
	 * returns the list held by this object
	 *
	 * @return object the list
	 */
	public function getList() {
		
		if (empty($this->_list)) {
			$listPeer = new List8D_Model_List;
			$this->_list = $listPeer->getById($this->getListId()); 
		}
		
		return $this->_list;
	}
	
	public function setList($list) {
		$this->_list = $list;
	}
	
	/**
	 * Sets the order placement of the object in an ordered list
	 *
	 * @param integer $position - the placement to give to the object
	 * @return object itself
	 */
	function setPosition($position) {
		$this->_order = $position;
		return $this;
	}
	
	/**
	 * Retrieves the position of the object in an ordered list
	 *
	 * @return integer the position
	 */
	function getPosition() {
		return $this->_order;
	}
	
	/**
	 * Retrieves the author associated with the object
	 *
	 * @return string the author
	 */
	function getAuthor() {
		return $this->_author;
	}
	
	/**
	 * Moves an item in a list to a new position and/or a new list
	 *
	 * @param integer $position - the position to move to
	 * @param integer $listId - the optional list to move to
	 * @return object itself
	 */
	public function moveTo($position,$listId=null) {
		
		if (is_null($listId)) {
			$listId = $this->getListId();
		}
		
		if ($listId==$this->getListId()) {
			$same = true;
		} else {
			$same = false;
		}
		
		$listPeer = new List8D_Model_List();
		
		if (!$same) {
			$oldListId = $this->getListId();
			$this->setListId($listId);
			$this->setPosition($position);
			$this->save();
			
			
			$oldList = $listPeer->getById($oldListId);
			$i=0;
			foreach($oldList->getChildren() as $child) {
				$child->setPosition($i);
				$i++;
			}
			$oldList->save();
		}
		
		$newList = $listPeer->getById($this->getListId());
		
		$i=0;
		foreach($newList->getChildren() as $child) {
			
			if ($i==$position) {
				$this->setListId($listId);
				$this->setPosition($position);
				$this->save();
				$i++;
			} 
			
			if ($child->getId()!=$this->getId()) {
				$child->setPosition($i);
				$i++;	
				$child->save();
			}
		

		}
		
		if ($i==$position) {
			$this->setListId($listId);
			$this->setPosition($position);
			$this->save();
			$i++;
		}
		
		return $this;
		
	} 
	
	/**
	 * Perform a fixPostions on this objects parent.
	 *
	 * @return object $this
	 */
	public function fixParentsPositions() {
		
		if ($parent = $this->getList()) {
			$parent->fixPositions();
		}
	
		return $this;
	
	}
	
}
